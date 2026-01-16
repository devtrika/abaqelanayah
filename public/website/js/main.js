$(window).on("load", function () {
  $("body").removeClass("overflow");
});
$(document).ready(function () {
  $(".menu-icon").click(function () {
    $(".header-nav").addClass("active");
    $("body").addClass("overflow");
    $(".overlay").fadeIn();
  });
  $(".close-nav,.overlay").click(function () {
    $(".header-nav").removeClass("active");
    $("body").removeClass("overflow");
    $(".overlay").fadeOut();
  });
  if ($(window).width() <= 767) {
    $(".has-children .nav-link").click(function (e) {
      e.stopPropagation();
      e.preventDefault();
      $(".has-children .nav-link").not(this).removeClass("active");
      $(this).toggleClass("active");
      if ($(this).siblings().css("display") == "none") {
        $(this).siblings().slideDown(500);
      } else {
        $(this).siblings().slideUp(500);
      }
      $(".has-children .nav-link").not(this).siblings().slideUp(500);
    });
  }
  $(".filter-trigger").click(function () {
    $(".category-side").addClass("active");
    $("body").addClass("overflow");
    $(".filters-overlay").fadeIn();
  });
  $(".filters-overlay").click(function () {
    $(".category-side").removeClass("active");
    $("body").removeClass("overflow");
    $(".filters-overlay").fadeOut();
  });
  $(".account-trigger").click(function () {
    $(".account-trigger,.account-list").toggleClass("active");
    $("body").addClass("overflow");
    $(".account-overlay").fadeToggle();
  });
  $(".account-overlay").click(function () {
    $(".account-trigger,.account-list").removeClass("active");
    $("body").removeClass("overflow");
    $(".account-overlay").fadeOut();
  });

  if (typeof Swiper !== "undefined") {
    /***** main slider *****/
    var mainSwiper = new Swiper(".main-slider .swiper", {
      spaceBetween: 20,
      loop: true,
      autoplay: {
        delay: 10000,
      },
      pagination: {
        el: ".main-slider .swiper-pagination",
        clickable: true,
      },
    });

    /***** categories slider *****/
    var categoriesSwiper = new Swiper(".categories-slider .swiper", {
      spaceBetween: 10,
      slidesPerView: "auto",
      pagination: {
        el: ".categories-slider .swiper-pagination",
        clickable: true,
      },
    });

    /***** specials slider *****/
    var specialsSwiper = new Swiper(".specials-slider .swiper", {
      spaceBetween: 24,
      slidesPerView: "auto",
      pagination: {
        el: ".specials-slider .swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        0: {
          spaceBetween: 10,
        },
        768: {
          spaceBetween: 24,
        },
      },
    });

    /***** brands slider *****/
    var brandsSwiper = new Swiper(".brands-slider .swiper", {
      slidesPerView: "auto",
      pagination: {
        el: ".brands-slider .swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        0: {
          spaceBetween: 5,
        },
        768: {
          spaceBetween: 15,
        },
      },
    });

    /***** lattest slider *****/
    var lattestSwiper = new Swiper(".lattest-slider .swiper", {
      spaceBetween: 24,
      slidesPerView: "auto",
      pagination: {
        el: ".lattest-slider .swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        0: {
          spaceBetween: 10,
        },
        768: {
          spaceBetween: 24,
        },
      },
    });
  }
});
