<?php
/*
Plugin Name: WooCommerce Sber Checkout plugin
Plugin URI:
Description: Allows to use a payment gateway with the WooCommerce.
Version: 4.1.4
Author: RBSPayment
Text Domain: wc-sber-text-domain
Domain Path: /lang
*/
if (!defined('ABSPATH')) exit;
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once(__DIR__ . '/include.php');
if (file_exists(__DIR__ . '/SberDiscount.php')) {
include(__DIR__ . '/SberDiscount.php');
}
add_filter('plugin_row_meta', 'sber_register_plugin_links', 10, 2);
function sber_register_plugin_links($links, $file)
{
$base = plugin_basename(__FILE__);
if ($file == $base) {
$links[] = '<a href="admin.php?page=wc-settings&tab=checkout&section=sber">' . __('Settings', 'woocommerce') . '</a>';
}
return $links;
}
add_action('plugins_loaded', 'woocommerce_sber_init');
function woocommerce_sber_init()
{
load_plugin_textdomain('wc-sber-text-domain', false, dirname(plugin_basename(__FILE__)) . '/lang');
if (!class_exists('WC_Payment_Gateway')) {
return;
}
if (class_exists('WC_RBSPayment_Sber'))
return;
class WC_RBSPayment_Sber extends WC_Payment_Gateway
{
public function __construct()
{
$this->id = 'sber';
$icon_path = is_file(__DIR__ . DIRECTORY_SEPARATOR . 'logo.png') ? plugin_dir_url(__FILE__) . 'logo.png' : null;
$this->method_title = RBSPAYMENT_SBER_PAYMENT_NAME;
$this->method_description = __('Online acquiring and payment processing.', 'wc-' . $this->id . '-text-domain');
if (defined('RBSPAYMENT_SBER_ENABLE_REFUNDS') && RBSPAYMENT_SBER_ENABLE_REFUNDS === true) {
$this->supports = array(
'products',
'refunds',
);
}
$this->init_form_fields();
$this->init_settings();
$this->title = $this->get_option('title');
$this->merchant = $this->get_option('merchant');
$this->password = $this->get_option('password');
$this->test_mode = $this->get_option('test_mode');
$this->stage_mode = $this->get_option('stage_mode');
$this->description = $this->get_option('description');
$this->order_status_paid = $this->get_option('order_status_paid');
$this->icon = $icon_path;
$this->send_order = $this->get_option('send_order');
$this->tax_system = $this->get_option('tax_system');
$this->tax_type = $this->get_option('tax_type');
$this->success_url = $this->get_option('success_url');
$this->fail_url = $this->get_option('fail_url');
$this->FFDVersion = $this->get_option('FFDVersion');
$this->paymentMethodType = $this->get_option('paymentMethodType');
$this->paymentObjectType = $this->get_option('paymentObjectType');
$this->paymentObjectType_delivery = $this->get_option('paymentMethodType_delivery');
$this->enable_cacert = $this->get_option('enable_cacert');
$this->cacert_path = null;
if (file_exists(dirname(__FILE__) . "/cacert.cer") && $this->get_option('enable_cacert') == "yes") {
$this->enable_cacert = true;
$this->cacert_path = dirname(__FILE__) . "/cacert.cer";
}
$this->pData = get_plugin_data(__FILE__);
$this->logging = RBSPAYMENT_SBER_ENABLE_LOGGING;
$this->fiscale_options = RBSPAYMENT_SBER_ENABLE_CART_OPTIONS;
$this->orderNumberById = true; //false - must be installed WooCommerce Sequential Order Numbers
$this->allowCallbacks = RBSPAYMENT_SBER_ENABLE_CALLBACK;
$this->enable_for_methods = $this->get_option('enable_for_methods', array());
$this->test_url = RBSPAYMENT_SBER_TEST_URL;
$this->prod_url = RBSPAYMENT_SBER_PROD_URL;
if (defined('RBSPAYMENT_SBER_PROD_URL_ALTERNATIVE_DOMAIN') && defined('RBSPAYMENT_SBER_PROD_URL_ALT_PREFIX')) {
if (substr($this->merchant, 0, strlen(RBSPAYMENT_SBER_PROD_URL_ALT_PREFIX)) == RBSPAYMENT_SBER_PROD_URL_ALT_PREFIX) {
$pattern = '/^https:\/\/[^\/]+/';
$this->prod_url = preg_replace($pattern, rtrim(RBSPAYMENT_SBER_PROD_URL_ALTERNATIVE_DOMAIN, '/'), $this->prod_url);
} else {
$this->allowCallbacks = false;
}
}
add_action('valid-sber-standard-ipn-request', array($this, 'successful_request'));
add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
if (!$this->is_valid_for_use()) {
$this->enabled = false;
}
$this->callback();
}
public function process_refund($order_id, $amount = null, $reason = '')
{
$order = wc_get_order($order_id);
if ($amount == "0.00") {
$amount = 0;
} else {
$amount = $amount * 100;
}
$order_key = $order->get_order_key();
$args = array(
'userName' => $this->merchant,
'password' => $this->password,
'orderId' => get_post_meta($order_id, 'orderId', true),
'amount' => $amount
);
if ($this->test_mode == 'yes') {
$action_adr = $this->test_url;
} else {
$action_adr = $this->prod_url;
}
$gose = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr . 'getOrderStatusExtended.do', array(), $this->cacert_path);
$res = json_decode($gose, true);
if ($res["orderStatus"] == "2" || $res["orderStatus"] == "4") { //DEPOSITED||REFUNDED
$result = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr . 'refund.do', array(), $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$logData = $args;
$logData['password'] = '**removed from log**';
$this->writeLog("[DEPOSITED REFUND RESPONSE]: " . print_r($logData, true) . " \n" . $result);
}
} elseif ($res["orderStatus"] == "1") { //APPROVED 2x
if ($amount == 0) {
unset($args['amount']);
}
$result = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr . 'reverse.do', array(), $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$logData = $args;
$logData['password'] = '**removed from log**';
$this->writeLog("[APPROVED REVERSE RESPONSE]: " . print_r($logData, true) . " \n" . $result);
}
} else {
return new WP_Error('wc_' . $this->id . '_refund_failed', sprintf(__('Order ID (%s) failed to be refunded. Please contact administrator for more help.', 'wc-' . $this->id . '-text-domain'), $order_id));
}
$response = json_decode($result, true);
if ($response["errorCode"] != "0") {
if ($response["errorCode"] == "7") {
return new WP_Error('wc_' . $this->id . '_refund_failed', "For partial refunds Order state should be in DEPOSITED in Gateway");
}
return new WP_Error('wc_' . $this->id . '_refund_failed', $response["errorMessage"]);
} else {
$result = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr . 'getOrderStatusExtended.do', array(), $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$this->writeLog("[FINALE STATE]: " . $result);
}
$response = json_decode($result, true);
$orderStatus = $response['orderStatus'];
if ($orderStatus == '4' || $orderStatus == '3') {
return true;
} elseif ($orderStatus == '1') {
return true;
}
}
return false;
}
public function process_admin_options()
{
if ($this->test_mode == 'yes') {
$action_adr = $this->test_url;
$gate_url = str_replace("payment/rest", "mportal-uat/mvc/public/merchant/update", $action_adr);
} else {
$action_adr = $this->prod_url;
$gate_url = str_replace("payment/rest", "mportal/mvc/public/merchant/update", $action_adr);
if (defined('RBSPAYMENT_SBER_PROD_URL_ALTERNATIVE_DOMAIN')) {
$pattern = '/^https:\/\/[^\/]+/';
$gate_url = preg_replace($pattern, rtrim(RBSPAYMENT_SBER_PROD_URL_ALTERNATIVE_DOMAIN, '/'), $gate_url);
}
}
$gate_url .= substr($this->merchant, 0, -4);
$callback_addresses_string = get_option('siteurl') . "?wc-api=WC_RBSPayment_Sber&sber=callback";
if ($this->allowCallbacks !== false) {
$response = $this->_updateGatewayCallback($this->merchant, $this->password, $gate_url, $callback_addresses_string, null);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$this->writeLog("REQUEST: ". $gate_url . "\n[callback_addresses_string]: " . $callback_addresses_string . "\n[RESPONSE]: " . $response);
}
}
parent::process_admin_options();
}
public function _updateGatewayCallback($login, $password, $action_address, $callback_addresses_string, $ca_info = null)
{
$headers = array(
'Content-Type:application/json',
'Authorization: Basic ' . base64_encode($login . ":" . $password)
);
$data['callbacks_enabled'] = true;
$data['callback_type'] = "STATIC";
$data['callback_addresses'] = $callback_addresses_string;
$data['callback_http_method'] = "GET";
$data['callback_operations'] = "deposited,approved,declinedByTimeout";
$response = $this->_sendGatewayData(json_encode($data), $action_address, $headers, $ca_info);
return $response;
}
public function _sendGatewayData($data, $action_address, $headers = array(), $ca_info = null)
{
$curl_opt = array(
CURLOPT_HTTPHEADER => $headers,
CURLOPT_VERBOSE => true,
CURLOPT_SSL_VERIFYHOST => false,
CURLOPT_URL => $action_address,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POST => true,
CURLOPT_POSTFIELDS => $data,
CURLOPT_HEADER => true,
);
$ssl_verify_peer = false;
if ($ca_info != null) {
$ssl_verify_peer = true;
$curl_opt[CURLOPT_CAINFO] = $ca_info;
}
$curl_opt[CURLOPT_SSL_VERIFYPEER] = $ssl_verify_peer;
$ch = curl_init();
curl_setopt_array($ch, $curl_opt);
$response = curl_exec($ch);
if ($response === false) {
$this->writeLog("The payment gateway is returning an empty response.");
}
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);
return substr($response, $header_size);
}
public function callback()
{
if (isset($_GET['sber'])) {
$action = $_GET['sber'];
if ($this->test_mode == 'yes') {
$action_adr = $this->test_url;
} else {
$action_adr = $this->prod_url;
}
$action_adr .= 'getOrderStatusExtended.do';
$args = array(
'userName' => $this->merchant,
'password' => $this->password,
);
switch ($action) {
case "result":
$args['orderId'] = isset($_GET['orderId']) ? $_GET['orderId'] : null;
$order_id = $_GET['order_id'];
$order = new WC_Order($order_id);
$response = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr, array(), $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$logData = $args;
$logData['password'] = '**removed from log**';
$this->writeLog("[REQUEST RU]: " . $action_adr . ": " . print_r($logData, true) . "\n[RESPONSE]: " . print_r($response, true));
}
$response = json_decode($response, true);
$orderStatus = $response['orderStatus'];
if ($orderStatus == '1' || $orderStatus == '2') {
if ($this->allowCallbacks === false) {
$order->update_status($this->order_status_paid, "Sber " . __('Payment successful', 'wc-' . $this->id . '-text-domain'));
try {
wc_reduce_stock_levels($order_id);
} catch (Exception $e) {
}
update_post_meta($order_id, 'orderId', $args['orderId']);
$order->payment_complete();
}
if (!empty($this->success_url)) {
WC()->cart->empty_cart();
wp_redirect($this->success_url . "?order_id=" . $order_id);
exit;
}
wp_redirect($this->get_return_url($order));
exit;
} else {
if ($this->allowCallbacks === false) {
$order->update_status('failed', "Sber " . __('Payment failed', 'wc-' . $this->id . '-text-domain'));
}
if (!empty($this->fail_url)) {
wp_redirect($this->fail_url . "?order_id=" . $order_id);
exit;
}
wc_add_notice(__('There was an error while processing payment<br/>', 'wc-' . $this->id . '-text-domain') . $response['actionCodeDescription'], 'error');
wp_redirect($order->get_cancel_order_url());
exit;
}
$order->save();
break;
case "callback":
$args['orderId'] = isset($_GET['mdOrder']) ? $_GET['mdOrder'] : null;
$response = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr, array(), $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$logData = $args;
$logData['password'] = '**removed from log**';
$this->writeLog("[REQUEST CB]: " . $action_adr . ": " . print_r($logData, true) . "\n[RESPONSE]: " . print_r($response, true));
}
$response = json_decode($response, true);
$p = explode("_", $response['orderNumber']);
if ($this->orderNumberById) {
$order_id = $p[0];
} else {
$order_id = wc_sequential_order_numbers()->find_order_by_order_number($p[0]);
}
$order = new WC_Order($order_id);
$orderStatus = $response['orderStatus'];
if ($orderStatus == '1' || $orderStatus == '2') {
update_post_meta($order_id, 'orderId', $args['orderId']);
if (strpos($order->get_status(), "pending") !== false || strpos($order->get_status(), "failed") !== false) { //PLUG-4415, 4495
$order->update_status($this->order_status_paid, "Sber " . __('Payment successful', 'wc-' . $this->id . '-text-domain'));
$this->writeLog("[VALUE TO SET ORDER_STATUS]: " . $this->order_status_paid); //PLUG-7155
try {
wc_reduce_stock_levels($order_id);
} catch (Exception $e) {
}
$order->payment_complete();
echo "->" . $this->order_status_paid;
}
} elseif (empty(get_post_meta($order_id, 'orderId', true)))  {
$order->update_status('failed', "Sber " . __('Payment failed', 'wc-' . $this->id . '-text-domain'));
}
$order->save();
break;
}
exit;
}
}
/**
* Check if this gateway is enabled and available in the user's country
*/
function is_valid_for_use()
{
return true;
}
/*
* Admin Panel Options
*/
public function admin_options()
{
?>
<h3><?php echo RBSPAYMENT_SBER_PAYMENT_NAME; ?></h3>
<p><?php _e("Allow customers to conveniently checkout directly with ", 'wc-' . $this->id . '-text-domain'); ?><?php echo RBSPAYMENT_SBER_PAYMENT_NAME; ?></p>
<?php if ($this->is_valid_for_use()) : ?>
<table class="form-table">
<?php
$this->generate_settings_html();
?>
</table>
<?php else : ?>
<div class="inline error"><p>
<strong><?php _e('Error: ', 'woocommerce'); ?></strong>: <?php echo $this->id; ?><?php _e(' does not support your currency.', 'wc-' . $this->id . '-text-domain'); ?>
</p></div>
<?php
endif;
}
/*
* Initialise Gateway Settings Form Fields
*/
function init_form_fields()
{
$shipping_methods = array();
if (is_admin())
foreach (WC()->shipping()->load_shipping_methods() as $method) {
$shipping_methods[$method->id] = $method->get_method_title();
}
$form_fields = array(
'enabled' => array(
'title' => __('Enable/Disable', 'wc-' . $this->id . '-text-domain'),
'type' => 'checkbox',
'label' => __('Enable', 'woocommerce') . " " . RBSPAYMENT_SBER_PAYMENT_NAME,
'default' => 'yes'
),
'title' => array(
'title' => __('Title', 'wc-' . $this->id . '-text-domain'),
'type' => 'text',
'css' => 'width: 460px;',
'description' => __('Title displayed to your customer when they make their order.', 'wc-' . $this->id . '-text-domain'),
'desc_tip' => true,
),
'merchant' => array(
'title' => __('Login-API', 'wc-' . $this->id . '-text-domain'),
'type' => 'text',
'default' => '',
'desc_tip' => true,
),
'password' => array(
'title' => __('Password', 'wc-' . $this->id . '-text-domain'),
'type' => 'password',
'default' => '',
'desc_tip' => true,
),
'test_mode' => array(
'title' => __('Test mode', 'wc-' . $this->id . '-text-domain'),
'type' => 'checkbox',
'label' => __('Enable', 'woocommerce'),
'description' => __('In this mode no actual payments are processed.', 'wc-' . $this->id . '-text-domain'),
'default' => 'no'
),
'stage_mode' => array(
'title' => __('Payments type', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => 'one-stage',
'options' => array(
'one-stage' => __('One-phase payments', 'wc-' . $this->id . '-text-domain'),
'two-stage' => __('Two-phase payments', 'wc-' . $this->id . '-text-domain'),
),
),
);
$form_fields_ext1 = array(
'description' => array(
'title' => __('Description', 'wc-' . $this->id . '-text-domain'),
'type' => 'textarea',
'css' => 'width: 460px;',
'description' => __('Payment description displayed to your customer.', 'wc-' . $this->id . '-text-domain'),
),
'order_status_paid' => array(
'title' => __('Payed order status', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'description' => __('Payed order status.', 'wc-' . $this->id . '-text-domain'),
'default' => 'wc-completed',
'desc_tip' => true,
'options' => array(
'wc-processing' => _x('Processing', 'Order status', 'woocommerce'),
'wc-completed' => _x('Completed', 'Order status', 'woocommerce'),
),
),
'success_url' => array(
'title' => __('success_url', 'woocommerce'),
'type' => 'text',
'css' => 'width: 460px;',
'description' => __('Page your customer will be redirected to after a <b>successful payment</b>.<br/>Leave this field blank, if you want to use default settings.', 'wc-' . $this->id . '-text-domain'),
),
'fail_url' => array(
'title' => __('fail_url', 'woocommerce'),
'type' => 'text',
'css' => 'width: 460px;',
'description' => __('Page your customer will be redirected to after an <b>unsuccessful payment</b>.<br/>Leave this field blank, if you want to use default settings.', 'wc-' . $this->id . '-text-domain'),
),
'enable_for_methods' => array(
'title' => __('Enable for shipping methods', 'wc-' . $this->id . '-text-domain'),
'type' => 'multiselect',
'class' => 'wc-enhanced-select',
'css' => 'width: 460px;',
'default' => '',
'options' => $this->load_shipping_method_options(),
'desc_tip' => true,
'custom_attributes' => array(
'data-placeholder' => __('Select shipping methods (empty for all)', 'wc-' . $this->id . '-text-domain')
)
)
);
$form_fields = array_merge($form_fields, $form_fields_ext1);
if (file_exists(dirname(__FILE__) . "/cacert.cer")) {
$cert_field = array(
'enable_cacert' => array(
'title' => __("Verify SSL certificate", 'wc-' . $this->id . '-text-domain'),
'type' => 'checkbox',
'label' => __('Enable', 'woocommerce'),
'description' => __('Disabling verification makes the connection insecure. Just having encryption on a transfer is not enough as you cannot be sure that you are communicating with the correct end-point.', 'wc-' . $this->id . '-text-domain'),
'default' => 'yes'
),
);
$form_fields = array_merge($form_fields, $cert_field);
}
$form_fields_ext2 = array(
'send_order' => array(
'title' => __("Send cart data<br />(including customer info)", 'wc-' . $this->id . '-text-domain'),
'type' => 'checkbox',
'label' => __('Enable', 'woocommerce'),
'description' => __('If this option is enabled order receipts will be created and sent to your customer and to the revenue service.<br/>This is a paid option, contact your bank to enable it. If you use it, configure VAT settings. VAT is calculated according to the Russian legislation. VAT amounts calculated by your store may differ from the actual VAT amounts that can be applied.', 'wc-' . $this->id . '-text-domain'),
'default' => 'no'
),
'tax_system' => array(
'title' => __('Tax system', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => '0',
'options' => array(
'0' => __('General', 'wc-' . $this->id . '-text-domain'),
'1' => __('Simplified, income', 'wc-' . $this->id . '-text-domain'),
'2' => __('Simplified, income minus expences', 'wc-' . $this->id . '-text-domain'),
'3' => __('Unified tax on imputed income', 'wc-' . $this->id . '-text-domain'),
'4' => __('Unified agricultural tax', 'wc-' . $this->id . '-text-domain'),
'5' => __('Patent taxation system', 'wc-' . $this->id . '-text-domain'),
),
),
'tax_type' => array(
'title' => __('Default VAT', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => '0',
'options' => array(
'0' => __('No VAT', 'wc-' . $this->id . '-text-domain'),
'1' => __('VAT 0%', 'wc-' . $this->id . '-text-domain'),
'2' => __('VAT 10%', 'wc-' . $this->id . '-text-domain'),
'3' => __('VAT 18%', 'wc-' . $this->id . '-text-domain'),
'6' => __('VAT 20%', 'wc-' . $this->id . '-text-domain'),
'4' => __('VAT applicable rate 10/110', 'wc-' . $this->id . '-text-domain'),
'5' => __('VAT applicable rate 18/118', 'wc-' . $this->id . '-text-domain'),
'7' => __('VAT applicable rate 20/120', 'wc-' . $this->id . '-text-domain'),
),
),
'FFDVersion' => array(
'title' => __('Fiscal document format', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => 'v1_05',
'options' => array(
'v1_05' => __('v1.05', 'wc-' . $this->id . '-text-domain'),
'v1_2' => __('v1.2', 'wc-' . $this->id . '-text-domain'),
),
'description' => __('Also specify the version in your bank web account and in your fiscal service web account.', 'wc-' . $this->id . '-text-domain'),
),
'paymentMethodType' => array(
'title' => __('Payment type', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => '1',
'options' => array(
'1' => __('Full prepayment', 'wc-' . $this->id . '-text-domain'),
'2' => __('Partial prepayment', 'wc-' . $this->id . '-text-domain'),
'3' => __('Advance payment', 'wc-' . $this->id . '-text-domain'),
'4' => __('Full payment', 'wc-' . $this->id . '-text-domain'),
'5' => __('Partial payment with further credit', 'wc-' . $this->id . '-text-domain'),
'6' => __('No payment with further credit', 'wc-' . $this->id . '-text-domain'),
'7' => __('Payment on credit', 'wc-' . $this->id . '-text-domain'),
),
),
'paymentMethodType_delivery' => array(
'title' => __('Payment type for delivery', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => '1',
'options' => array(
'1' => __('Full prepayment', 'wc-' . $this->id . '-text-domain'),
'2' => __('Partial prepayment', 'wc-' . $this->id . '-text-domain'),
'3' => __('Advance payment', 'wc-' . $this->id . '-text-domain'),
'4' => __('Full payment', 'wc-' . $this->id . '-text-domain'),
'5' => __('Partial payment with further credit', 'wc-' . $this->id . '-text-domain'),
'6' => __('No payment with further credit', 'wc-' . $this->id . '-text-domain'),
'7' => __('Payment on credit', 'wc-' . $this->id . '-text-domain'),
),
),
'paymentObjectType' => array(
'title' => __('Type of goods and services', 'wc-' . $this->id . '-text-domain'),
'type' => 'select',
'class' => 'wc-enhanced-select',
'default' => '1',
'options' => array(
'1' => __('Goods', 'wc-' . $this->id . '-text-domain'),
'2' => __('Excised goods', 'wc-' . $this->id . '-text-domain'),
'3' => __('Job', 'wc-' . $this->id . '-text-domain'),
'4' => __('Service', 'wc-' . $this->id . '-text-domain'),
'5' => __('Stake in gambling', 'wc-' . $this->id . '-text-domain'),
'7' => __('Lottery ticket', 'wc-' . $this->id . '-text-domain'),
'9' => __('Intellectual property provision', 'wc-' . $this->id . '-text-domain'),
'10' => __('Payment', 'wc-' . $this->id . '-text-domain'),
'11' => __("Agent's commission", 'wc-' . $this->id . '-text-domain'),
'12' => __('Combined', 'wc-' . $this->id . '-text-domain'),
'13' => __('Other', 'wc-' . $this->id . '-text-domain'),
),
),
);
if (RBSPAYMENT_SBER_ENABLE_CART_OPTIONS === true) {
$form_fields = array_merge($form_fields, $form_fields_ext2);
}
$this->form_fields = $form_fields;
}
function get_product_price_with_discount($price, $type, $c_amount, &$order_data)
{
switch ($type) {
case 'percent':
$new_price = ceil($price * (1 - $c_amount / 100));
$order_data['discount_total'] -= ($price - $new_price);
break;
case 'fixed_product':
$new_price = $price - $c_amount;
$order_data['discount_total'] -= $c_amount / 100;
break;
default:
$new_price = $price;
}
return $new_price;
}
/*
* Generate the dibs button link
*/
public function generate_form($order_id)
{
$order = new WC_Order($order_id);
$amount = $order->get_total() * 100;
$coupons = array();
global $woocommerce;
if (!empty($woocommerce->cart->applied_coupons)) {
foreach ($woocommerce->cart->applied_coupons as $code) {
$coupons[] = new WC_Coupon($code);
}
}
if ($this->test_mode == 'yes') {
$action_adr = $this->test_url;
} else {
$action_adr = $this->prod_url;
}
$extra_url_param = '';
if ($this->stage_mode == 'two-stage') {
$action_adr .= 'registerPreAuth.do';
} else if ($this->stage_mode == 'one-stage') {
$extra_url_param = '&wc-callb=callback_function';
$action_adr .= 'register.do';
}
$order_data = $order->get_data();
$language = substr(get_bloginfo("language"), 0, 2);
switch ($language) {
case  ('uk'):
$language = 'ua';
break;
case ('be'):
$language = 'by';
break;
}
$jsonParams_array = array(
'CMS' => 'Wordpress ' . get_bloginfo('version') . " + woocommerce version: " . wpbo_get_woo_version_number(),
'Module-Version' => $this->pData['Version'],
);
if (RBSPAYMENT_SBER_CUSTOMER_EMAIL_SEND && !empty($order_data['billing']['email'])) {
$jsonParams_array['email'] = $order_data['billing']['email'];
}
if (!empty($order_data['billing']['phone'])) {
$jsonParams_array['phone'] = preg_replace("/(\W*)/", "", $order_data['billing']['phone']);
}
if (!empty($order_data['billing']['first_name'])) {
$jsonParams_array['payerFirstName'] = $order_data['billing']['first_name'];
}
if (!empty($order_data['billing']['last_name'])) {
$jsonParams_array['payerLastName'] = $order_data['billing']['last_name'];
}
if (!empty($order_data['billing']['address_1'])) {
$jsonParams_array['postAddress'] = $order_data['billing']['address_1'];
}
if (!empty($order_data['billing']['city'])) {
$jsonParams_array['payerCity'] = $order_data['billing']['city'];
}
if (!empty($order_data['billing']['state'])) {
$jsonParams_array['payerState'] = $order_data['billing']['state'];
}
if (!empty($order_data['billing']['postcode'])) {
$jsonParams_array['payerPostalCode'] = $order_data['billing']['postcode'];
}
if (!empty($order_data['billing']['country'])) {
$jsonParams_array['payerCountry'] = $order_data['billing']['country'];
}
$args = array(
'userName' => $this->merchant,
'password' => $this->password,
'amount' => $amount,
'returnUrl' => get_option('siteurl') . '?wc-api=WC_RBSPayment_Sber&sber=result&order_id=' . $order_id . $extra_url_param,
'currency' => unserialize(RBSPAYMENT_SBER_CURRENCY_CODES)[get_woocommerce_currency()],
'jsonParams' => json_encode($jsonParams_array),
);
if (!empty($order_data['customer_id'] && $order_data['customer_id'] > 0)) {
$client_email = !empty($order_data['billing']['email']) ? $order_data['billing']['email'] : "";
$args['clientId'] = md5($order_data['customer_id']  .  $client_email  . get_option('siteurl'));
}
if ($this->send_order == 'yes' && $this->fiscale_options === true) {
$args['taxSystem'] = $this->tax_system;
$order_items = $order->get_items();
$order_timestamp_created = $order_data['date_created']->getTimestamp();
$items = array();
$itemsCnt = 1;
foreach ($order_items as $value) {
$item = array();
$product_variation_id = $value['variation_id'];
if ($product_variation_id) {
$product = new WC_Product_Variation($value['variation_id']);
$item_code = $itemsCnt . "-" . $value['variation_id'];
} else {
$product = new WC_Product($value['product_id']);
$item_code = $itemsCnt . "-" . $value['product_id'];
}
$product_sku = get_post_meta($value['product_id'], '_sku', true);
$item_code = !empty($product_sku) ? $product_sku : $item_code;
$tax_type = $this->getTaxType($product);
$product_price = round((($value['total'] + $value['total_tax']) / $value['quantity']) * 100);
if ($product->get_type() == 'variation') {
}
$item['positionId'] = $itemsCnt++;
$item['name'] = $value['name'];
if ($this->FFDVersion == 'v1_05') {
$item['quantity'] = array(
'value' => $value['quantity'],
'measure' => RBSPAYMENT_SBER_MEASUREMENT_NAME
);
} else {
$item['quantity'] = array(
'value' => $value['quantity'],
'measure' => RBSPAYMENT_SBER_MEASUREMENT_CODE
);
}
$item['itemAmount'] = $product_price * $value['quantity'];
$item['itemCode'] = $item_code;
$item['tax'] = array(
'taxType' => $tax_type
);
$item['itemPrice'] = $product_price;
$attributes = array();
$attributes[] = array(
"name" => "paymentMethod",
"value" => $this->paymentMethodType
);
$attributes[] = array(
"name" => "paymentObject",
"value" => $this->paymentObjectType
);
$item['itemAttributes']['attributes'] = $attributes;
$items[] = $item;
}
$shipping_total = $order->get_shipping_total();
$shipping_tax = $order->get_shipping_tax();
if ($shipping_total > 0) {
$WC_Order_Item_Shipping = new WC_Order_Item_Shipping();
$itemShipment['positionId'] = $itemsCnt;
$itemShipment['name'] = __('Delivery', 'wc-' . $this->id . '-text-domain');
if ($this->FFDVersion == 'v1_05') {
$itemShipment['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_SBER_MEASUREMENT_NAME
);
} else {
$itemShipment['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_SBER_MEASUREMENT_CODE
);
}
$itemShipment['itemAmount'] = $itemShipment['itemPrice'] = $shipping_total * 100;
$itemShipment['itemCode'] = 'delivery';
$itemShipment['tax'] = array(
'taxType' => $tax_type = $this->getTaxType($WC_Order_Item_Shipping) //$this->tax_type
);
$attributes = array();
$attributes[] = array(
"name" => "paymentMethod",
"value" => $this->paymentObjectType_delivery
);
$attributes[] = array(
"name" => "paymentObject",
"value" => 4
);
$itemShipment['itemAttributes']['attributes'] = $attributes;
$items[] = $itemShipment;
}
$order_bundle = array(
'orderCreationDate' => $order_timestamp_created,
'cartItems' => array('items' => $items)
);
if (RBSPAYMENT_SBER_CUSTOMER_EMAIL_SEND && !empty($order_data['billing']['email'])) {
$order_bundle['customerDetails']['email'] = $order_data['billing']['email'];
}
if (!empty($order_data['billing']['phone'])) {
$order_bundle['customerDetails']['phone'] = preg_replace("/(\W*)/", "", $order_data['billing']['phone']);
}
if (class_exists('SberDiscount')) {
$discountHelper = new SberDiscount();
$discount = $discountHelper->discoverDiscount($args['amount'], $order_bundle['cartItems']['items']);
if ($discount != 0) {
$discountHelper->setOrderDiscount($discount);
$recalculatedPositions = $discountHelper->normalizeItems($order_bundle['cartItems']['items']);
$recalculatedAmount = $discountHelper->getResultAmount();
$order_bundle['cartItems']['items'] = $recalculatedPositions;
}
}
$args['orderBundle'] = json_encode($order_bundle);
}
if ($this->orderNumberById) {
$args['orderNumber'] = $order_id . '_' . time();
} else {
$args['orderNumber'] = trim(str_replace('#', '', $order->get_order_number())) . "_" . time(); // PLUG-3966, PLUG-4300
}
$headers = array(
'CMS: Wordpress ' . get_bloginfo('version') . " + woocommerce version: " . wpbo_get_woo_version_number(),
'Module-Version: ' . $this->pData['Version'],
);
$response = $this->_sendGatewayData(http_build_query($args, '', '&'), $action_adr, $headers, $this->cacert_path);
if (RBSPAYMENT_SBER_ENABLE_LOGGING === true) {
$logData = $args;
$logData['password'] = '**removed from log**';
$this->writeLog("[REQUEST]: " . $action_adr . ": \nDATA: " . print_r($logData, true) . "\n[RESPONSE]: " . $response);
}
$response = json_decode($response, true);
if (empty($response['errorCode'])) {
if (RBSPAYMENT_SBER_SKIP_CONFIRMATION_STEP == true) {
wp_redirect($response['formUrl']); //PLUG-4104 Comment this line for redirect via pressing button (step)
}
echo '<p><a class="button cancel" href="' . $response['formUrl'] . '">' . __('Proceed with payment', 'wc-' . $this->id . '-text-domain') . '</a></p>';
exit;
} else {
return '<p>' . __('Error code #' . $response['errorCode'] . ': ' . $response['errorMessage'], 'wc-' . $this->id . '-text-domain') . '</p>' .
'<a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Cancel payment and return to cart', 'wc-' . $this->id . '-text-domain') . '</a>';
}
}
function getTaxType($product)
{
$tax = new WC_Tax();
if (get_option("woocommerce_calc_taxes") == "no") { // PLUG-4056
$item_rate = -1;
} else {
$base_tax_rates = $tax->get_base_tax_rates($product->get_tax_class(true));
if (!empty($base_tax_rates)) {
$temp = $tax->get_rates($product->get_tax_class());
$rates = array_shift($temp);
$item_rate = round(array_shift($rates));
} else {
$item_rate = -1;
}
}
if ($item_rate == 20) {
$tax_type = 6;
} else if ($item_rate == 18) {
$tax_type = 3;
} else if ($item_rate == 10) {
$tax_type = 2;
} else if ($item_rate == 0) {
$tax_type = 1;
} else {
$tax_type = $this->tax_type;
}
return $tax_type;
}
function correctBundleItem(&$item, $discount)
{
$item['itemAmount'] -= $discount;
$diff_price = fmod($item['itemAmount'], $item['quantity']['value']); //0.5 quantity
if ($diff_price != 0) {
$item['itemAmount'] += $item['quantity']['value'] - $diff_price;
}
$item['itemPrice'] = $item['itemAmount'] / $item['quantity']['value'];
}
/**
* Check If The Gateway Is Available For Use.
*
* @return bool
*/
public function is_available()
{
$order = null;
$needs_shipping = false;
if (WC()->cart && WC()->cart->needs_shipping()) {
$needs_shipping = true;
} elseif (is_page(wc_get_page_id('checkout')) && 0 < get_query_var('order-pay')) {
$order_id = absint(get_query_var('order-pay'));
$order = wc_get_order($order_id);
if ($order && 0 < count($order->get_items())) {
foreach ($order->get_items() as $item) {
$_product = $item->get_product();
if ($_product && $_product->needs_shipping()) {
$needs_shipping = true;
break;
}
}
}
}
$needs_shipping = apply_filters('woocommerce_cart_needs_shipping', $needs_shipping);
if (!$needs_shipping) {
return parent::is_available();
}
if (!empty($this->enable_for_methods) && $needs_shipping) {
$order_shipping_items = is_object($order) ? $order->get_shipping_methods() : false;
$chosen_shipping_methods_session = WC()->session->get('chosen_shipping_methods');
if ($order_shipping_items) {
$canonical_rate_ids = $this->get_canonical_order_shipping_item_rate_ids($order_shipping_items);
} else {
$canonical_rate_ids = $this->get_canonical_package_rate_ids($chosen_shipping_methods_session);
}
if (!count($this->get_matching_rates($canonical_rate_ids))) {
return false;
}
}
return parent::is_available();
}
/**
* Indicates whether a rate exists in an array of canonically-formatted rate IDs that activates this gateway.
*
* @param array $rate_ids Rate ids to check.
* @return boolean
* @since  3.4.0
*
*/
private function get_matching_rates($rate_ids)
{
return array_unique(array_merge(array_intersect($this->enable_for_methods, $rate_ids), array_intersect($this->enable_for_methods, array_unique(array_map('wc_get_string_before_colon', $rate_ids)))));
}
/**
* Converts the chosen rate IDs generated by Shipping Methods to a canonical 'method_id:instance_id' format.
*
* @param array $order_shipping_items Array of WC_Order_Item_Shipping objects.
* @return array $canonical_rate_ids    Rate IDs in a canonical format.
* @since  3.4.0
*
*/
private function get_canonical_order_shipping_item_rate_ids($order_shipping_items)
{
$canonical_rate_ids = array();
foreach ($order_shipping_items as $order_shipping_item) {
$canonical_rate_ids[] = $order_shipping_item->get_method_id() . ':' . $order_shipping_item->get_instance_id();
}
return $canonical_rate_ids;
}
/**
* Converts the chosen rate IDs generated by Shipping Methods to a canonical 'method_id:instance_id' format.
*
* @param array $chosen_package_rate_ids Rate IDs as generated by shipping methods. Can be anything if a shipping method doesn't honor WC conventions.
* @return array $canonical_rate_ids  Rate IDs in a canonical format.
* @since  3.4.0
*
*/
private function get_canonical_package_rate_ids($chosen_package_rate_ids)
{
WC()->cart->calculate_shipping();
$shipping_packages = WC()->shipping()->get_packages();
$canonical_rate_ids = array();
if (!empty($chosen_package_rate_ids) && is_array($chosen_package_rate_ids)) {
foreach ($chosen_package_rate_ids as $package_key => $chosen_package_rate_id) {
if (!empty($shipping_packages[$package_key]['rates'][$chosen_package_rate_id])) {
$chosen_rate = $shipping_packages[$package_key]['rates'][$chosen_package_rate_id];
$canonical_rate_ids[] = $chosen_rate->get_method_id() . ':' . $chosen_rate->get_instance_id();
}
}
}
return $canonical_rate_ids;
}
/*
* Process the payment and return the result
*/
function process_payment($order_id)
{
$order = new WC_Order($order_id);
if (!empty($_GET['pay_for_order']) && $_GET['pay_for_order'] == 'true') {
$this->generate_form($order_id);
die;
}
$pay_now_url = $order->get_checkout_payment_url(true);
return array(
'result' => 'success',
'redirect' => $pay_now_url
);
}
/*
* Receipt page
*/
function receipt_page($order)
{
echo $this->generate_form($order);
}
function writeLog($var, $info = true)
{
if ($this->test_mode != "yes") {
}
$information = "";
if ($var) {
if ($info) {
$information = "\n\n";
$information .= str_repeat("-=", 64);
$information .= "\nDate: " . date('Y-m-d H:i:s');
$information .= "\nWordpress version " . get_bloginfo('version') . "; Woocommerce version: " . wpbo_get_woo_version_number() . "\n";
}
$result = $var;
if (is_array($var) || is_object($var)) {
$result = "\n" . print_r($var, true);
}
$result .= "\n\n";
$path = dirname(__FILE__) . '/wc_sber_' . date('Y-m') . '.log';
error_log($information . $result, 3, $path);
return true;
}
return false;
}
function sber_change_status_function($order_id)
{
$order = wc_get_order($order_id);
$order->update_status('wc-complete');
}
/**
* Loads all of the shipping method options for the enable_for_methods field.
*
* @return array
*/
private function load_shipping_method_options()
{
$data_store = WC_Data_Store::load('shipping-zone');
$raw_zones = $data_store->get_zones();
foreach ($raw_zones as $raw_zone) {
$zones[] = new WC_Shipping_Zone($raw_zone);
}
$zones[] = new WC_Shipping_Zone(0);
$options = array();
foreach (WC()->shipping()->load_shipping_methods() as $method) {
$options[$method->get_method_title()] = array();
$options[$method->get_method_title()][$method->id] = sprintf(__('Any &quot;%1$s&quot; method', 'woocommerce'), $method->get_method_title());
foreach ($zones as $zone) {
$shipping_method_instances = $zone->get_shipping_methods();
foreach ($shipping_method_instances as $shipping_method_instance_id => $shipping_method_instance) {
if ($shipping_method_instance->id !== $method->id) {
continue;
}
$option_id = $shipping_method_instance->get_rate_id();
$option_instance_title = sprintf(__('%1$s (#%2$s)', 'woocommerce'), $shipping_method_instance->get_title(), $shipping_method_instance_id);
$option_title = sprintf(__('%1$s &ndash; %2$s', 'woocommerce'), $zone->get_id() ? $zone->get_zone_name() : __('Other locations', 'woocommerce'), $option_instance_title);
$options[$method->get_method_title()][$option_id] = $option_title;
}
}
}
return $options;
}
}
if (!function_exists('add_sber_gateway')) {
function add_sber_gateway($methods)
{
$methods[] = 'WC_RBSPayment_Sber';
return $methods;
}
}
if (!function_exists('wpbo_get_woo_version_number')) {
function wpbo_get_woo_version_number()
{
if (!function_exists('get_plugins'))
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
$plugin_folder = get_plugins('/' . 'woocommerce');
$plugin_file = 'woocommerce.php';
if (isset($plugin_folder[$plugin_file]['Version'])) {
return $plugin_folder[$plugin_file]['Version'];
} else {
return NULL;
}
}
}
add_filter('woocommerce_payment_gateways', 'add_sber_gateway');
}