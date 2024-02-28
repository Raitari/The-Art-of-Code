<?php

define('RBSPAYMENT_SBER_PAYMENT_NAME', 'Sber');

define('RBSPAYMENT_SBER_PROD_URL' , 'https://securepayments.sberbank.ru/payment/rest/');
define('RBSPAYMENT_SBER_TEST_URL' , 'https://3dsec.sberbank.ru/payment/rest/');
define('RBSPAYMENT_SBER_PROD_URL_ALTERNATIVE_DOMAIN' , 'https://lk.sbergate.ru/');

define('RBSPAYMENT_SBER_ENABLE_LOGGING', true);
define('RBSPAYMENT_SBER_ENABLE_CART_OPTIONS', true);

define('RBSPAYMENT_SBER_MEASUREMENT_NAME', 'шт'); //FFD v1.05
define('RBSPAYMENT_SBER_MEASUREMENT_CODE', 0); //FFD v1.2

define('RBSPAYMENT_SBER_SKIP_CONFIRMATION_STEP', true);
define('RBSPAYMENT_SBER_CUSTOMER_EMAIL_SEND', true); //PLUG-4667
define('RBSPAYMENT_SBER_ENABLE_CALLBACK', true);

define('RBSPAYMENT_SBER_CURRENCY_CODES', serialize(array(
    'USD' => '840',
    'UAH' => '980',
    'RUB' => '643',
    'RON' => '946',
    'KZT' => '398',
    'KGS' => '417',
    'JPY' => '392',
    'GBR' => '826',
    'EUR' => '978',
    'CNY' => '156',
    'BYR' => '974',
    'BYN' => '933'
)));
