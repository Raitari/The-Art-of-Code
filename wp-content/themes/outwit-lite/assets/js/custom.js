/**
 * Custom JS for theme elements
 */

// ADDED DELAY TO LIST ITEMS
let time = 0.1;

let content = document.querySelectorAll('.wp-block-navigation__container,.wp-block-page-list');
content.forEach((item) => {
  let firstChild = item.childNodes
  firstChild.forEach(item => {
    let lastChild = item.lastChild;
    let ul = 'wp-block-navigation__submenu-container';
    if (lastChild.classList.contains(ul)) {
      lastChild.classList.add('main-list')
      let nestedChild = lastChild.children;
      Array.from(nestedChild).forEach((item, index) => {
        item.setAttribute("style", `transition-delay: ${time * index}s`);
      })
    }
  })
})

let cartBtn = document.querySelectorAll('.add_to_cart_button');
cartBtn.forEach((item) => {
  let text = 'added to cart';
  item.addEventListener('click', function () {
    item.innerHTML = text;
  })
})


//  FOR HEADER PRODUCT SEARCH 

const searchInput = document.querySelector('.header .search-product input[type="search"]');
const searchDummy = document.querySelector('.dummy-icon .wp-block-search__button');
const searchWrap = document.querySelector('.header .search-product');


const outwitShowHandler = (e) => {
  e.preventDefault();
  let body = document.body;
  let heightHeader = searchWrap.clientHeight
  body.style.marginTop = `${heightHeader}px`
  body.classList.toggle('search-toggle');
}

if (searchWrap !== null) {

  if (searchDummy !== null) {
    searchDummy.addEventListener('click', outwitShowHandler)
  }

  document.addEventListener('click', function (e) {
    if (!searchWrap.contains(e.target) && !searchDummy.contains(e.target)) {
      let body = document.body;
      body.classList.remove('search-toggle')
      body.style.marginTop = `0px`

    }
  }
  )
}

// let body = document.body;
let body = document.body;
window.addEventListener('scroll', function (e) {
  var ivalueY = (this.scrollY * -1 / 2);
  var ivalueS = (this.scrollY * 0.1 + 0.1);
  var ivalueBtn = (this.scrollY * -1 / 3);
  let sliderContent = document.querySelector('.slider-content h2');
  let headerImage = document.querySelector('.header-media .wp-block-cover__image-background');
  let btnContent = document.querySelector('.slider-content .wp-block-button');
  if (sliderContent !== null) {
    headerImage.setAttribute('style', `transform: scale( ${100 + ivalueS}%)`);
    sliderContent.setAttribute('style', `transform: translate3d(0, ${ivalueY}px, 0)`);
    sliderContent.style.opacity = 1;
    sliderContent.style.transition = 'none';
    btnContent.setAttribute('style', `transform: translate3d(0, ${ivalueBtn}px, 0)`)
    btnContent.style.opacity = 1;
    btnContent.style.transition = 'none';
  }
})


window.addEventListener('DOMContentLoaded', function () {
  let sliderContent = document.querySelector('.slider-content h2');
  let btnContent = document.querySelector('.slider-content .wp-block-button');
  if (sliderContent !== null) {
    sliderContent.style.transform = ` translateY(0)`
    sliderContent.style.opacity = 1
    btnContent.style.transform = ` translateY(0)`
    btnContent.style.opacity = 1
  }
})


/*
*
*
// SHOW OVERLAY MENU WHILE CLICK HAMBURGER MENU --- MODERN MENU
*
*/

let showMenuFunc = () => {
  let hamburgerBtn = document.querySelector('.hamburger');
  if (hamburgerBtn !== null) {
    removeOverlayMenu()
    hamburgerBtn.addEventListener('click', function () {
      let hamburgerText = document.querySelector('.hamburger-text');
      body.classList.toggle('show-menu')
      if (body.classList.contains('show-menu')) {
        hamburgerText.innerHTML = 'Close'
      } else {
        hamburgerText.innerHTML = 'Menu'
      }
    })
  }
}

let removeOverlayMenu = () => {
  let body = document.body;
  let hamburgerBtn = document.querySelector('.hamburger');
  if (hamburgerBtn !== null) {
    body.addEventListener('click', function (e) {
      let hamburgerText = document.querySelector('.hamburger-text');
      let navContainerOpen = document.querySelector('.modern-menu .wp-block-navigation__responsive-container');
      if (!hamburgerBtn.contains(e.target) && !navContainerOpen.contains(e.target)) {
        body.classList.remove('show-menu')
        hamburgerText.innerHTML = 'Menu'
        navContainerOpen.classList.remove('is-menu-open')
        navContainerOpen.classList.remove('has-modal-open')
      }
    })
  }
}

let showMobileMenu = () => {
  let windowWidth = window.innerWidth;
  removeOverlayMenu()
  let navContainerOpen = document.querySelector('.modern-menu .wp-block-navigation__responsive-container');
  if (navContainerOpen !== null) {
    let hamburgerBtn = document.querySelector('.hamburger');
    let navContainerClose = document.querySelector('.modern-menu .wp-block-navigation__responsive-container-close');
    hamburgerBtn.addEventListener('click', function () {
      if (windowWidth < 1366) {
        navContainerOpen.classList.toggle('is-menu-open')
        navContainerOpen.classList.toggle('has-modal-open')
      } else {
        navContainerOpen.classList.remove('is-menu-open')
        navContainerOpen.classList.remove('has-modal-open')
      }
    })

    navContainerClose.addEventListener('click', function () {
      let hamburgerText = document.querySelector('.hamburger-text');
      let body = document.body;
      navContainerOpen.classList.remove('is-menu-open')
      navContainerOpen.classList.remove('has-modal-open')
      body.classList.remove('show-menu')
      hamburgerText.innerHTML = 'Menu'
    })
  }
}



window.addEventListener('resize', showMobileMenu)
window.addEventListener('load', showMobileMenu)


showMenuFunc();