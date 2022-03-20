const aboutTitles = document.querySelectorAll(".about-dropdown__title");
[...aboutTitles].forEach(
  (btn) =>
  (btn.onclick = function () {
    this.nextElementSibling.classList.toggle(
      "about-dropdown__content_active"
    );
  })
);
jQuery(document).ready(function (a) {
  const t = a(".search__input"),
    e = a(".ajax-search");
  t.keyup(function () {
    let t = a(this).val();
    t.length > 2
      ? a.ajax({
        url: "/wp-admin/admin-ajax.php",
        type: "POST",
        data: { action: "ajax_search", term: t },
        success: function (a) {
          e.fadeIn(200).html(a);
        },
      })
      : e.fadeOut(200);
  }),
    a(document).mouseup(function (a) {
      0 === t.has(a.target).length &&
        0 === e.has(a.target).length &&
        e.fadeOut(200);
    });
});

const body = document.body;
const cartBtn = document.querySelector(".cart-btn");
const cartClose = document.querySelector(".minicart-container__close");
const cartBlock = document.querySelector(".minicart");
const cartCont = document.querySelector(".minicart-container");
const menuButton = document.querySelector(".show-menu");
const menuArrow = document.querySelectorAll(".navigation__btn-show");
const navigation = document.querySelector(".navigation-container");
const showUserMenu = document.querySelector(".header__user-open");
const userMenu = document.querySelector(".user-menu");
const userMenuCont = document.querySelector(".user-menu__container");
const userMenuClose = document.querySelector(".user-menu__close");
const showSearch = document.querySelector(".show-search");
const searchForm = document.querySelector(".header__search");
const closeNavigation = document.querySelector(".navigation-container__close");
const backgroundLayer = document.querySelector(".bg-layer");
const footerMenu = document.querySelectorAll(".footer-navigation__title");
const navigationMainBtn = document.querySelectorAll(".navigation__main-link");

[...navigationMainBtn].forEach(
  (btn) =>
  (btn.onclick = function () {
    if (window.innerWidth < 1128) {
      this.lastElementChild.classList.toggle("navigation__category_active");
      this.classList.toggle("navigation__main-link_active");
    } else {
      return;
    }
  })
);

cartBtn.onclick = function () {
  cartBlock.classList.toggle("minicart_shown");
  cartCont.classList.toggle("minicart-container__active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.toggle("jija");
};

cartClose.onclick = function () {
  cartBlock.classList.toggle("minicart_shown");
  backgroundLayer.classList.toggle("bg-layer_active");
  cartCont.classList.toggle("minicart-container__active");
  body.classList.remove("jija");
};
//scrol btn

function trackScroll() {
  if (window.pageYOffset > 1000) {
    goTopBtn.classList.add("back_to_top-show");
  } else if (window.pageYOffset < 1000) {
    goTopBtn.classList.remove("back_to_top-show");
  }
}

function backToTop() {
  if (window.pageYOffset > 0) {
    window.scrollBy(0, -60);
    setTimeout(backToTop, 0);
  }
}

var goTopBtn = document.querySelector(".back_to_top");

window.addEventListener("scroll", trackScroll);
goTopBtn.addEventListener("click", backToTop);

//scrolbtn finished

menuButton.onclick = function () {
  navigation.classList.toggle("navigation-container_active");
  backgroundLayer.classList.toggle("bg-layer_active");
};

backgroundLayer.onclick = function () {
  searchForm.classList.remove("header__search_active");
  this.classList.toggle("bg-layer_active");
  navigation.classList.remove("navigation-container_active");
  cartBlock.classList.remove("minicart_shown");
  userMenu.classList.remove("user-menu_active");
  cartCont.classList.remove("minicart-container__active");
  body.classList.remove("jija");
  userMenuCont.classList.remove("user-menu__container_active");
  [...navigationMainBtn].forEach(function (item, i, arr) {
    item.lastElementChild.classList.remove("navigation__category_active");
    item.classList.remove("navigation__main-link_active");
  });
};

closeNavigation.onclick = function () {
  navigation.classList.toggle("navigation-container_active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.remove("jija");
};

showSearch.onclick = function () {
  searchForm.classList.toggle("header__search_active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.toggle("jija");
};
showUserMenu.mouseover = function () {
  userMenu.classList.toggle("user-menu_active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.toggle("jija");
};
showUserMenu.onclick = function () {
  userMenu.classList.toggle("user-menu_active");
  userMenuCont.classList.toggle("user-menu__container_active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.toggle("jija");
};

userMenuClose.onclick = function () {
  userMenu.classList.toggle("user-menu_active");
  userMenuCont.classList.toggle("user-menu__container_active");
  backgroundLayer.classList.toggle("bg-layer_active");
  body.classList.remove("jija");
};

[...footerMenu].forEach(
  (btn) =>
  (btn.onclick = function () {
    btn.nextElementSibling.classList.toggle("footer-navigation__list_active");
    this.classList.toggle("footer-navigation__title_active");
  })
);

let textToShow = document.querySelector(".about__full-text");
let showBtn = document.querySelector(".about__show-btn");
if (showBtn != null) {
  showBtn.onclick = function () {
    textToShow.classList.toggle("about__full-text_active");
    this.classList.toggle("about__show-btn_active");
    if (this.classList.contains("about__show-btn_active")) {
      this.innerHTML = "-";
    } else {
      this.innerHTML = "+";
      window.scrollTo({
        top: 2200,
        behavior: "smooth",
      });
    }
  };
}
// Additional

// Auth page
let authElement = document.querySelector(".forgot-password");
if (authElement != null) {
  authElement.addEventListener("click", () => {
    document
      .querySelector(".reset-password")
      .classList.add("reset-password_active");
  });
}

let authElement2 = document.querySelector(".close-reset-password");
if (authElement2 != null) {
  authElement2.addEventListener("click", () => {
    document
      .querySelector(".reset-password")
      .classList.remove("reset-password_active");
  });
}

//Blog filter close
let blogFiltElem = document.querySelector(".blog-filters-shown");
let blogFiltElem2 = document.querySelector(".blog-filter-close");

if (blogFiltElem != null) {
  blogFiltElem.addEventListener("click", () => {
    document
      .querySelector(".blog-filter")
      .classList.toggle("blog-filter_active");
  });
}

if (blogFiltElem2 != null) {
  blogFiltElem2.addEventListener("click", () => {
    document
      .querySelector(".blog-filter")
      .classList.toggle("blog-filter_active");
  });
}

//Faq page
let kbTitle = document.querySelectorAll(".kb-section__subtitle");
[...kbTitle].forEach(
  (btn) =>
  (btn.onclick = function () {
    btn.nextElementSibling.classList.toggle("kb-section__description_active");
  })
);

// My account
const myAccOfferBtns = document.querySelectorAll(
  ".b2bking_myaccount_individual_offer_top"
);
if (myAccOfferBtns != null) {
  [...myAccOfferBtns].forEach(
    (btn) =>
    (btn.onclick = function () {
      btn.nextElementSibling.classList.toggle("shown-offer_active");
    })
  );
  let blocks = document.querySelectorAll(".shown-offer");
  if (blocks[0] != undefined) {
    blocks[0].classList.add("shown-offer_active");
    const myAccOfferBtnsDesk = document.querySelectorAll(".offer-button");
    myAccOfferBtnsDesk[0].classList.add("offer-button_active");
    [...myAccOfferBtnsDesk].forEach(
      (btn) =>
      (btn.onclick = function () {
        doSmth(btn);
      })
    );
  }
}

function doSmth(btn) {
  for (var i = 0; i < blocks.length; i++) {
    if (
      blocks[i].firstElementChild.innerHTML.replace(/\s+/g, "") ==
      btn.innerHTML.replace(/\s+/g, "")
    ) {
      blocks[i].classList.toggle("shown-offer_active");
      for (let d = 0; d < myAccOfferBtnsDesk.length; d++) {
        myAccOfferBtnsDesk[d].classList.remove("offer-button_active");
      }
      btn.classList.add("offer-button_active");
    } else {
      blocks[i].classList.remove("shown-offer_active");
    }
  }
}

// Order sort

const allOrdersBtn = document.querySelector(".orders__navigation-btn_all");
const statusBtn = document.querySelector(".title-status");
const ordersNavigation = document.querySelector(".orders__navigation");
const pendingOrdersBtn = document.querySelector(
  ".orders__navigation-btn_onhold"
);
const completeOrdersBtn = document.querySelector(
  ".orders__navigation-btn_complete"
);
const currentStatus = document.querySelectorAll(".order-item__status");
if (allOrdersBtn != null) {
  allOrdersBtn.onclick = function () {
    currentStatus.forEach(function (item, i, arr) {
      item.parentNode.classList.remove("order-item_hide");
    });
    ordersNavigation.classList.remove("orders__navigation_active");
  };
  statusBtn.onclick = function () {
    ordersNavigation.classList.toggle("orders__navigation_active");
  };

  pendingOrdersBtn.onclick = function () {
    currentStatus.forEach(function (item, i, arr) {
      if (item.innerHTML === "On hold") {
        item.parentNode.classList.remove("order-item_hide");
        return;
      } else {
        item.parentNode.classList.add("order-item_hide");
        return;
      }
    });
    ordersNavigation.classList.remove("orders__navigation_active");
  };
  completeOrdersBtn.onclick = function () {
    currentStatus.forEach(function (item, i, arr) {
      if (item.innerHTML === "Completed") {
        item.parentNode.classList.remove("order-item_hide");
        return;
      } else {
        item.parentNode.classList.add("order-item_hide");
        return;
      }
    });
    ordersNavigation.classList.remove("orders__navigation_active");
  };
}

let allCount = document.querySelector(".all-count"),
  onholdCount = document.querySelector(".onhold-count"),
  completedCount = document.querySelector(".completed-count");
if (allCount != null) {
  let all = 0;
  let hold = 0;
  let complete = 0;
  for (var i = 0; i < currentStatus.length; i++) {
    all++;
    if (currentStatus[i].innerHTML === "Completed") {
      complete++;
    } else if (currentStatus[i].innerHTML === "On hold") {
      hold++;
    }
  }
  allCount.innerHTML = "(" + all + ")";
  onholdCount.innerHTML = "(" + hold + ")";
  completedCount.innerHTML = "(" + complete + ")";
}
// Total price calc
const validateProductPage = document.querySelector('.product-container');
const totalPrice = document.querySelector(".product__total-price");
if (validateProductPage && totalPrice) {
  const qty = document.querySelector(".input-text");
  const price = document.querySelector(".product-price-calc");
  const currency = document.querySelector(
    ".woocommerce-Price-currencySymbol"
  ).innerHTML;
  const priceTable = document.querySelector(".b2bking_shop_table");
  const priceTableBody = priceTable.getElementsByTagName("tbody")[0];
  const productPage = document.querySelector(".product-head");
  const priceRows = Array.prototype.slice.call(
    priceTableBody.getElementsByTagName("tr")
  );
  const shownPrice = document
    .querySelector(".product__price")
    .getElementsByTagName("bdi")[0];
  priceRows.forEach((element) => {
    element.querySelector(".woocommerce-Price-currencySymbol ").remove();
  });
  let priceArray = [];
  let qtyArray = [];
  for (let index = 0; index < priceRows.length; index++) {
    const element = priceRows[index];
    quantity = Number(
      element.firstElementChild.textContent.split(" ")[0].replace(/[^0-9]/g, "")
    );
    amount = Number(element.childNodes[3].textContent);
    qtyArray.push(quantity);
    priceArray.push(amount);
  }
  let qtyLenght = qtyArray.length;
  function calcResult() {
    for (let i = 0; i < qtyLenght; i++) {
      currentValue = Number(qty.value);
      if (currentValue < qtyArray[0]) {
        let drawableprice = priceArray[0];
        return drawableprice;
      } else if (currentValue >= qtyArray[i] && currentValue < qtyArray[i + 1]) {
        let drawableprice = priceArray[i];
        return drawableprice;
      } else if (currentValue >= qtyArray[qtyLenght - 1]) {
        let drawableprice = priceArray[qtyLenght - 1];
        return drawableprice;
      }
    }
  }
  let priceNum = Number(price.innerHTML)
  if (productPage != null) {
    if (qty != null && priceNum != 0) {
      totalPrice.innerHTML =
        currency + "" + Number(qty.value) * Number(price.innerHTML);
      qty.onchange = function () {
        if (price != null) {
          amount = calcResult();
          shownPrice.innerHTML =
            '<span class="woocommerce-Price-currencySymbol">' +
            currency +
            "</span>" +
            amount;
          totalPrice.innerHTML = currency + "" + Number(this.value) * amount;
        }
      };
    }
  }
}
// Search filter
const searchPage = document.querySelector(".search-page");
if (searchPage != null) {
  let prdbtn = document.querySelector(".search-navigation__btn_products");
  let blogbtn = document.querySelector(".search-navigation__btn_blog");
  let wikibtn = document.querySelector(".search-navigation__btn_wiki");

  let prdwrapper = document.querySelector(".product-results");
  let blogwrapper = document.querySelector(".blog-results");
  let wikiwrapper = document.querySelector(".wiki-results");

  prdbtn.classList.add("search-btn_active");

  prdbtn.onclick = function () {
    prdwrapper.classList.remove("search-block_hide");
    prdwrapper.classList.add("search-block_active");
    prdbtn.classList.add("search-btn_active");
    wikibtn.classList.remove("search-btn_active");
    blogbtn.classList.remove("search-btn_active");
    wikiwrapper.classList.remove("search-block_active");
    blogwrapper.classList.add("search-block_hide");
    wikiwrapper.classList.add("search-block_hide");
    blogwrapper.classList.remove("search-block_active");
  };
  blogbtn.onclick = function () {
    blogbtn.classList.add("search-btn_active");
    prdbtn.classList.remove("search-btn_active");
    wikibtn.classList.remove("search-btn_active");
    blogwrapper.classList.remove("search-block_hide");
    blogwrapper.classList.add("search-block_active");
    prdwrapper.classList.remove("search-block_active");
    wikiwrapper.classList.remove("search-block_active");
    prdwrapper.classList.add("search-block_hide");
    wikiwrapper.classList.add("search-block_hide");
  };

  wikibtn.onclick = function () {
    wikibtn.classList.add("search-btn_active");
    prdbtn.classList.remove("search-btn_active");
    blogbtn.classList.remove("search-btn_active");
    wikiwrapper.classList.remove("search-block_hide");
    wikiwrapper.classList.add("search-block_active");
    prdwrapper.classList.remove("search-block_active");
    blogwrapper.classList.remove("search-block_active");
    prdwrapper.classList.add("search-block_hide");
    blogwrapper.classList.add("search-block_hide");
  };

  let encCount = document.querySelector(".wiki-count"),
    blogCount = document.querySelector(".blog-count"),
    shopCount = document.querySelector(".products-count");
  if (document.querySelector(".blog-results").children.length === 0) {
    blogCount.innerHTML = "(0)";
  } else {
    blogCount.innerHTML =
      "(" + document.querySelector(".blog-results").children.length + ")";
  }
  if (document.querySelector(".product-results").children.length === 0) {
    shopCount.innerHTML = "(0)";
  } else {
    shopCount.innerHTML =
      "(" + document.querySelector(".product-results").children.length + ")";
  }
  if (document.querySelector(".wiki-results").children.length === 0) {
    encCount.innerHTML = "(0)";
  } else {
    encCount.innerHTML =
      "(" + document.querySelector(".wiki-results").children.length + ")";
  }
}

//Wiki tabs
const wikiMainContainer = document.querySelector(".single-wiki");
if (wikiMainContainer != null) {
  let generalbtn = document.querySelector(".tabs__btn_general");
  let extbtn = document.querySelector(".tabs__btn_external");
  let cntbtn = document.querySelector(".tabs__btn_cnt");

  let generalwrapper = document.getElementById("wiki-general");
  let extwrapper = document.getElementById("wiki-external");
  let cntwrapper = document.getElementById("wiki-cnt");

  generalbtn.classList.add("tabs__btn_active");
  extwrapper.classList.add("wrapper-disable");
  cntwrapper.classList.add("wrapper-disable");

  generalbtn.onclick = function () {
    generalwrapper.classList.remove("wrapper-disable");
    generalbtn.classList.add("tabs__btn_active");
    extbtn.classList.remove("tabs__btn_active");
    cntbtn.classList.remove("tabs__btn_active");
    extwrapper.classList.add("wrapper-disable");
    cntwrapper.classList.add("wrapper-disable");
  };
  extbtn.onclick = function () {
    extwrapper.classList.remove("wrapper-disable");
    extbtn.classList.add("tabs__btn_active");
    generalbtn.classList.remove("tabs__btn_active");
    cntbtn.classList.remove("tabs__btn_active");
    generalwrapper.classList.add("wrapper-disable");
    cntwrapper.classList.add("wrapper-disable");
  };

  cntbtn.onclick = function () {
    cntwrapper.classList.remove("wrapper-disable");
    cntbtn.classList.add("tabs__btn_active");
    generalbtn.classList.remove("tabs__btn_active");
    extbtn.classList.remove("tabs__btn_active");
    extwrapper.classList.add("wrapper-disable");
    generalwrapper.classList.add("wrapper-disable");
  };
}

// Shop
const shopMainContainer = document.querySelector(".shop-wrapper");
if (shopMainContainer != null) {
  const productsWrapper = document.querySelector(".products");
  const sortBtn = document.querySelector(".shop-menu__sort");
  const sortForm = document.querySelector(".shop__order-by");
  const sortSelect = document.querySelector(".orderby");
  const filterBtn = document.querySelector(".shop-menu__params");
  const filter = document.querySelector(".filter-container");
  const filterClose = document.querySelector(".filter-close");
  const filterCategoryBtns = document.querySelectorAll(".shop-filter__button");
  const filterTitle = document.querySelectorAll(".wpc-filter-header");
  const filterInput = document.querySelectorAll(".filter-search");
  let layout = document.querySelector(".products");
  let gridButton = document.querySelector(".btn-grid");
  let listButton = document.querySelector(".btn-list");

  let lyt = localStorage.getItem("setLayout");

  if (lyt == 2) {
    listButton.classList.add("btn-list_active");
    layout.classList.add("row-layout");
  }

  if (lyt == 1) {
    gridButton.classList.add("btn-grid_active");
    layout.classList.add("grid-layout");
  }

  gridButton.onclick = function () {
    listButton.classList.remove("btn-list_active");
    gridButton.classList.add("btn-grid_active");
    layout.classList.remove("row-layout");
    layout.classList.add("grid-layout");
    localStorage.setItem("setLayout", 1);
  };

  listButton.onclick = function () {
    listButton.classList.add("btn-list_active");
    gridButton.classList.remove("btn-grid_active");
    layout.classList.add("row-layout");
    layout.classList.remove("grid-layout");
    localStorage.setItem("setLayout", 2);
  };

  const qtyInput = document.querySelectorAll(".product-quantity");
  [...qtyInput].forEach(
    (input) =>
    (input.onchange = function () {
      this.parentElement.previousElementSibling.previousElementSibling.href =
        this.parentElement.previousElementSibling.previousElementSibling.href.replace(
          /&.+$/,
          "&quantity=" + this.value
        );
    })
  );
  filterBtn.onclick = function () {
    filter.classList.toggle("filter-container_active");
  };

  filterClose.onclick = function () {
    filter.classList.toggle("filter-container_active");
  };

  sortBtn.onclick = function () {
    sortBtn.classList.toggle("sort-btn_active");
    sortForm.classList.toggle("shop__order-by_active");
    sortSelect.click();
  };

  [...filterTitle].forEach(
    (btn) =>
    (btn.onclick = function () {
      btn.parentElement.classList.toggle("wpc-filters-section_active");
    })
  );

  [...filterCategoryBtns].forEach(
    (btn) =>
    (btn.onclick = function () {
      btn.nextElementSibling.classList.toggle("list_active");
    })
  );

  let prdbtn = document.querySelectorAll(".product__btn");
  for (let i = 0; i < prdbtn.length; i++) {
    if (window.innerWidth < 1128) {
      if (prdbtn[i].innerHTML === "Add to cart") {
        prdbtn[
          i
        ].innerHTML = `<svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M0.5 1.05407C0.5 0.748065 0.755186 0.5 1.06997 0.5H3.1056C3.35093 0.5 3.56874 0.652607 3.64632 0.878857L4.1271 2.28094H15.93C16.1132 2.28094 16.2853 2.36655 16.3924 2.51103C16.4995 2.65552 16.5287 2.84125 16.4707 3.01022L14.6387 8.35302C14.5611 8.57927 14.3433 8.73188 14.098 8.73188H5.54834C5.18859 8.73188 4.89695 9.01538 4.89695 9.3651C4.89695 9.71482 5.18859 9.99832 5.54835 9.99832H13.6908C14.0056 9.99832 14.2608 10.2464 14.2608 10.5524C14.2608 10.8042 14.088 11.0168 13.8513 11.0842C14.0055 11.3261 14.0945 11.6114 14.0945 11.9169C14.0945 12.7912 13.3654 13.5 12.466 13.5C11.5666 13.5 10.8375 12.7912 10.8375 11.9169C10.8375 11.6207 10.9212 11.3435 11.0669 11.1065H8.13565C8.28129 11.3435 8.36498 11.6207 8.36498 11.9169C8.36498 12.7912 7.63588 13.5 6.73648 13.5C5.83709 13.5 5.10798 12.7912 5.10798 11.9169C5.10798 11.6161 5.19433 11.3348 5.34424 11.0953C4.45102 10.9968 3.757 10.2597 3.757 9.3651C3.757 8.65911 4.18919 8.05125 4.81041 7.77789L2.69478 1.60814H1.06997C0.755186 1.60814 0.5 1.36007 0.5 1.05407ZM5.95916 7.62374H13.6871L15.1392 3.38907H4.50708L5.95916 7.62374ZM7.22503 11.9169C7.22503 12.1792 7.0063 12.3919 6.73648 12.3919C6.46666 12.3919 6.24793 12.1792 6.24793 11.9169C6.24793 11.6547 6.46666 11.442 6.73648 11.442C7.0063 11.442 7.22503 11.6547 7.22503 11.9169ZM12.9546 11.9169C12.9546 12.1792 12.7359 12.3919 12.466 12.3919C12.1962 12.3919 11.9775 12.1792 11.9775 11.9169C11.9775 11.6547 12.1962 11.442 12.466 11.442C12.7359 11.442 12.9546 11.6547 12.9546 11.9169Z" fill="white"/>
</svg>`;
        prdbtn[i].style.width = "30px";
        prdbtn[i].style.height = "30px";
      }
    }
  }
}

// Return to promo

let currentDate = Date.now();
let visitDate = localStorage.getItem("adVisitDate");
let intervalDate = currentDate - visitDate;
if (intervalDate > 7200000) {
  localStorage.removeItem("adVisitor");
}

let adVisitorCont = document.querySelector(".advisitor-btn");
if (localStorage.adVisitor === "rhodiola") {
  adVisitorCont.innerHTML =
    '<a href="/rhodiola-promo" class="btn advisitor_btn">Rhodiola Promo</a>';
} else if (localStorage.adVisitor === "chaga") {
  adVisitorCont.innerHTML =
    '<a href="/chaga-promo" class="btn advisitor_btn">Chaga Promo</a>';
}

const authPageCheck = document.querySelector(".auth-page");
if (authPageCheck != null) {
  let authcont = document.querySelector(".container");
  let authpage = document.querySelector(".auth-page");
  let authheader = document.querySelector(".header");
  let authfooter = document.querySelector(".main-footer");
  let authmain = document.querySelector(".main-content");
  if (authpage) {
    authheader.classList.add("header-auth");
    authfooter.classList.add("footer-auth");
    authmain.classList.add("main-content-auth");
    authcont.classList.remove("container");
  }
}

let logoutBtn = document.querySelector(".logout-btn");
if (logoutBtn != null) {
  let logoutClose = document.querySelector(".close-logout-popup");
  let logoutPop = document.querySelector(".logout-popup");
  let logoutBg = document.querySelector(".desktop-popup-bg");
  logoutBtn.onclick = function () {
    logoutPop.classList.add("logout-popup_active");
    logoutBg.classList.add("desktop-popup-bg_active");
  };
  document.querySelector(".close-logout-popup").onclick = function () {
    document
      .querySelector(".logout-popup")
      .classList.remove("logout-popup_active");
    document
      .querySelector(".desktop-popup-bg")
      .classList.remove("desktop-popup-bg_active");
  };
  document.querySelector(".desktop-popup-bg").onclick = function () {
    document
      .querySelector(".logout-popup")
      .classList.remove("logout-popup_active");
    document
      .querySelector(".desktop-popup-bg")
      .classList.remove("desktop-popup-bg_active");
  };
}

// Bitrix Widget
document.addEventListener("DOMContentLoaded", function () {
  (function (w, d, u) {
    var s = d.createElement("script");
    s.async = true;
    s.src = u + "?" + ((Date.now() / 60000) | 0);
    var h = d.getElementsByTagName("script")[0];
    h.parentNode.insertBefore(s, h);
  })(
    window,
    document,
    "https://portal.vitaforestfood.com/upload/crm/site_button/loader_8_eal8j3.js"
  );
});

// Category description in catalog
// Full descriptions const
const foodSupplementsFull =
  "<h2>Food Supplements</h2><p>Due to unique characteristics of siberian mushrooms and herbs, VitaForest’s extracts and powders are used for production of active food supplements for already more than 5 years. They help to endure rugged environment and increase human body’s adaptation mechanisms.</p> <p>Handpicking of mushrooms and herbs in outlying Siberian areas, gentle processing and scientifically proven adjusted to each ingredient production technology give an opportunity to keep unique bioactive compounds in VitaForest's powders and extracts. Food supplements produced with siberian mushrooms and herbs may: </p> <ul> <li>help to endure rugged environment;</li> <li>regulates sleep patterns;</li> <li>increase libido;</li> <li>increase stress-resistance;</li> <li>decrease chronical fatigue;</li> <li>clear of apathy, inertness, constant urge to sleep;</li> <li>ease jet lag, effects of climate change and new schedule adjustments struggles;</li> <li>reduce effect of harmful aspects at production.</li> </ul> <p>VitaForest's extracts and powders from siberian mushrooms and herbs give an opportunity of production highly competitive and ecological friendly food supplements with high concentration of polysaccharides, glycosides, phytosterols, flavonoids and terpenoids. Their action is aimed at improving the well-being of a human body and solving local health problems. </p>";
const cosmeticsFull =
  "<h2>Cosmetics</h2><p>Cosmetic producers highly value VitaForest’s herbal, berry, fruit and mushroom powders for the fantastic characteristics of siberian areas. In nature, there are a huge amount of plants, berries, fruits and mushrooms which proved to be efficient during external application. However, it is Siberian nature that is deservedly considered a storehouse of the most valuable active components, which is confirmed by numerous scientific studies.</p> <p>Rhodiola rosea of Russian origin is added into creams and lotions as an effective agent for delaying aging due to high concentration of total rosavins (rosavin, rosarin, rosin) and salidroside in it. Siberian eleutherococcus contains a wide range of minerals which can be found only in that type of ginseng. It is used in cosmetic for skin care purposes. Nanain citronella hips are rich of E and C vitamins, minerals and essential oils and are natural antioxidants. Manchurian aralia rejuvenates, strengthens skin, improves it’s protection from external influences and recovers it elasticity and smoothes wrinkles.</p> <p>In cosmetic production Rhodiola rosea, chaga (Inonotus obliquus), milk thistle (Silybum marianum), purple coneflower (Echinacea purpurea), siberian ginseng (Eleutherococcus senticosus), Baikal skullcap (Scutellaria baicalensis) and other siberian plant, berry, fruit and mushroom powders and extracts are used.</p>";
const pharmaceuticalFull =
  "<h2>Pharmaceuticals</h2><p>VitaForest’s medicinal herbs, berries, fruits and mushrooms extracts and powders are used in production of anti-inflammatory, repairing, immunomodulatory and preventive care pastes, gels, suppositories and hygienic agents. It is driven by fact of high concentration of bioactive substances in Siberian plant, berry, fruit and mushroom sources and their unique characteristics. They are able to increase the body's resistance to various infections, promote wound healing and prevent the development of inflammatory processes. </p> <p>In pharmaceutical industry most commonly such dry powders and extracts as Rhodiola rosea, rose hip (Rosa spp.), astragalus (Astragalus spp.), hawthorn (Crataegus spp.), St. John's wort (Hypericum perforatum), elderberry (Sambucus nigra), stinging nettle (Urtica dioica), Siberian ginseng (Eleutherococcus senticosus) and other herbs, berries, fruits and mushrooms are used.</p> <p>We place greater focus on our product and we conduct quality tests on different stages of production. To start with, verification of herbs during foraging stage, qualitative and quantitative analysis of raw materials, and qualitative analysis of ready powder or extract. Sigma-Aldrich`s matter standards are used during laboratory tests implementation. Ready products comply with EU safety and quality standards.</p> <p>Upon your request, we can develop technical documentation and produce natural dry extracts and powders from any wild healthful siberian sources.</p>";
const nutritionFull =
  "<h2>Healthy nutrition</h2><p>Producers of sport, healthy, diabetica land other specialized food and beverage in numerous countries already acknowledged benefits of functional VitaForest extracts and powders, produced from Siberian berries, fruits, plants and mushrooms. Their uniqueness is manifests in the capability of improving general well-being and in high concentration of bioactive substances such as flavonoids, glycosides, polysaccharides, phytosterols, terpenoids and etc.  </p> <p>Application of extracts and powders made from Siberian berries, fruits, mushrooms and plants, including adaptogens, in production contributes to establishing highly competitive healthy food products lines which:</p> <ul> <li>increase physical and mental capacity, without negative impact on human body;</li> <li>support cognitive functions such as memory, thinking and focus abilities;</li> <li>delay aging;</li> <li>increase vitality;</li> <li>boost immunity;</li> <li>prevents tumor growth;</li> <li>increase durability during physical exercises and sport trainings.</li> </ul> <p>The most valuable properties relate to dry extracts and powders of Rhodiola rosea, Siberian ginseng (Eleutherococcus senticosus), maral root (Rhaponticum carthamoides), ginseng (Panax ginseng), schizandra (Schisandra chinensis) and Manchurian aralia (Aralia elata), black chokeberry (Aronia melanocarpa) are the richest with useful qualities</p> <p>The dry form of VitaForest extracts is convenient in use in the production of dietary marmalade, cookies, nutritional bars and snacks, muesli, cocktails, tea, coffee, fruit water infusions and other functional drinks.</p>";
const additivesFull =
  "<h2>Feed additives</h2><p>VitaForest’s functional extracts and powders made out of siberian wild herbs, berries, fruits, roots and mushrooms are used in veterinary medicine, dry pet food production and feed additives for farms and aquacultures. </p> <p>Siberian birch chaga is a natural energy source for animals. Chaga extracts and powders are used in veterinary practice and animal farming as a natural growth stimulant for juveniles. As researchers have proved, chaga positively affects blood and protein forming liver’s function, and, in addition, helps in curing indigestion among young animals and increases haemoglobin levels. </p> <p>Extracts and powders such medicinal herbs, berries, fruits and mushrooms as Rhodiola rosea, common dandelion (Taraxacum officinale), purple coneflower (Echinacea purpurea), baikal skullcap (Scutellaria baicalensis), common melilot (Melilotus officinalis) and others are used as animal feed supplement, bioactive ingredients for veterinary pastes, suppositories and other drugs, and also can be used as a flavoring and aromatic additive for the production of animal feeds. </p> <p>Siberian mushrooms, berries, fruits, roots and herbs are famous for their adaptogenic characteristics. Regular application positively affects growth, development, health and appearance of pets and animals.</p>";
const beveragesFull =
  "<h2>Food and beverages</h2><p>Due to the high content of biologically active substances, VitaForest dry functional extracts and powders are often used in confectionery, meat, dairy and other industries of new generation food and beverages - useful, stimulating various functions and systems of the human body. </p> <p>Dry extracts are used as flavoring additives to improve the organoleptic properties of alcoholic and low-alcoholic beverages. For example, oak (Quercus spp.), wormwood (Artemisia absinthium), willow (Salix spp.), thyme (Thymus spp.), elecampane (Inula helenium), tansy flower (Tanacetum vulgare) and yarrow (Achillea millefolium) plant extracts are used to impart natural bitterness in the production of vodkas, tinctures and vermouths. </p> <p>Chaga extract and powder are used in the production of cold tea, dairy and dairy-free drinks based on rice or almonds. Chaga gained a reputation of a perfect coffee substitute thanks to its rich flavor or an additive to it due to its ability to neutralize organic acids of coffee beans that irritate the stomach lining. </p> <p>Other most popular extracts and powders in the food and beverage industries are made of oregano (Origanum vulgare), hop (Humulus lupulus), maral root (Rhaponticum carthamoides), schisandra (Schisandra chinensis), Rhodiola rosea, rose hip (Rosa spp.), hawthorn (Crataegus spp.), raspberry (Rubus idaeus), wild strawberry (Fragaria vesca), grape (Vitis vinifera) and others. Addition of them into food and beverage products brings in unique, memorable and easily recognizable charm among many competitors.</p>";

// Switch cases with eventlisteners
const categoryDescriptionContainer = document.querySelector(
  ".shop-menu__category-desc"
);
if (categoryDescriptionContainer != null) {
  let currentPageUri = document.location.pathname;
  switch (currentPageUri) {
    case "/en/shop/industry-food-supplements/":
      categoryDescriptionContainer.innerHTML = foodSupplementsFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          foodSupplementsFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = foodSupplementsFull;" class="read-description">Read more</button>';
      });
      break;
    case "/en/shop/industry-cosmetic/":
      categoryDescriptionContainer.innerHTML = cosmeticsFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          cosmeticsFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = cosmeticsFull;" class="read-description">Read more</button>';
      });
      break;
    case "/en/shop/industry-pharmaceutical-industry/":
      categoryDescriptionContainer.innerHTML = pharmaceuticalFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          pharmaceuticalFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = pharmaceuticalFull;" class="read-description">Read more</button>';
      });
      break;
    case "/en/shop/industry-healthy-nutrition/":
      categoryDescriptionContainer.innerHTML = nutritionFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          nutritionFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = nutritionFull;" class="read-description">Read more</button>';
      });
      break;
    case "/en/shop/industry-feed-additives/":
      categoryDescriptionContainer.innerHTML = additivesFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          additivesFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = additivesFull;" class="read-description">Read more</button>';
      });
      break;
    case "/en/shop/industry-food-and-beverages/":
      categoryDescriptionContainer.innerHTML = beveragesFull;
      document.addEventListener("scroll", () => {
        categoryDescriptionContainer.innerHTML =
          beveragesFull.substring(0, 200) +
          '...</br><button onclick="categoryDescriptionContainer.innerHTML = beveragesFull;" class="read-description">Read more</button>';
      });
      break;
    default:
      console.log("main.js alert: This page does not contain descriptions");
  }
}

// Валидатор Quote'a
const quoteInProductPage = document.querySelector('.quote_plugin');
if (quoteInProductPage) {
  let oldForm = document.querySelector('.hide_this');
  oldForm.style.display = 'none';
}

let addToQuoteNoti = document.querySelector('.afrfqbt_single_page.button');
if (addToQuoteNoti) {
  addToQuoteNoti.addEventListener('click', () => {
    createNotificationOnce('Request', 'Succesfully added to request', 'Ok', 'add-to-request-noti');
  })
}

const invoiceBtn = document.querySelectorAll('.order-iteam__btn.btn.invoice');
let viewPort = document.documentElement.clientWidth;
if (invoiceBtn && viewPort > 1128) {
  invoiceBtn.forEach((element) => {
    element.innerHTML = '<svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
      '<path d="M11.6601 9.18438H4.1601C4.00385 9.18438 3.9101 9.30938 3.9101 9.43438V10.4344C3.9101 10.5906 4.00385 10.6844 4.1601 10.6844H11.6601C11.7851 10.6844 11.9101 10.5906 11.9101 10.4344V9.43438C11.9101 9.30938 11.7851 9.18438 11.6601 9.18438ZM13.4101 0.371876L12.0663 1.34063L10.8476 0.496876C10.3163 0.121876 9.62885 0.121876 9.0976 0.496876L7.9101 1.34063L6.69135 0.496876C6.44135 0.309376 6.12885 0.215626 5.81635 0.215626C5.4726 0.215626 5.19135 0.309376 4.94135 0.496876L3.7226 1.34063L2.37885 0.371876C1.75385 -0.0656244 0.910095 0.371876 0.910095 1.12188V15.2781C0.910095 15.9969 1.75385 16.4656 2.37885 16.0281L3.7226 15.0281L4.94135 15.9031C5.4726 16.2781 6.1601 16.2781 6.69135 15.9031L7.9101 15.0281L9.0976 15.9031C9.62885 16.2781 10.3163 16.2781 10.8476 15.9031L12.0663 15.0281L13.4101 16.0281C14.0351 16.4656 14.9101 16.0281 14.9101 15.2781V1.12188C14.9101 0.371876 14.0351 -0.0656244 13.4101 0.371876ZM13.4101 14.1531L12.0663 13.1844L9.9726 14.6844L7.87885 13.1844L5.81635 14.6844L3.7226 13.1844L2.4101 14.1531V2.24688L3.7226 3.21563L5.81635 1.71563L7.9101 3.21563L9.9726 1.71563L12.0663 3.21563L13.4101 2.24688V14.1531ZM11.6601 5.68438H4.1601C4.00385 5.68438 3.9101 5.80938 3.9101 5.93438V6.93438C3.9101 7.09063 4.00385 7.18438 4.1601 7.18438H11.6601C11.7851 7.18438 11.9101 7.09063 11.9101 6.93438V5.93438C11.9101 5.80938 11.7851 5.68438 11.6601 5.68438Z"' + ' fill="white"/>' + '</svg>';
  });
}