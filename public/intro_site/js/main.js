$(window).on('load', function () {
    $('.loader').fadeOut(1000);
    new WOW().init();
    $('body').addClass('o-auto');
});

$(document).ready(function () {

    $(".close-open-nav").on("click", function (e) {
        e.stopPropagation();
        $(this).toggleClass("active");
        if ($(this).hasClass("active")) {
            $(".nav_bar").addClass("active");
        } else {
            $(".nav_bar").removeClass("active");
        }
    });

    

    $("body").on("click", function () {
        $(".close-open-nav.active").click();
    });


    $('.owl-index').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });

    $(".owl-nav").width($(".owl-dots").outerWidth(true) + ($(".owl-nav > div").outerWidth(true) * 3));
    
    $('.owl-brands').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 2000,
        responsive: {
            0: {
                items: 2
            },

            600: {
                items: 4
            },
            1000: {
                items: 6
            }
        }
    });
    
    $(".body-content").css({marginTop : $("header").outerHeight(true)});
    
    $(window).resize(function () {
        
       $(".body-content").css({marginTop : $("header").outerHeight(true)});
        
    });
    
    // $(".nav_bar a").on("click", function (e) {
    //     e.preventDefault();
    //     $("html, body").animate({scrollTop : $("#" + $(this).attr("data-scroll")).offset().top - 45}, 1000);
    // });
    
    $(window).on("scroll", function () {
        $(".body-content > div").each(function () {
            if ($(window).scrollTop() >= $(this).offset().top - 50) {
                $(".nav_bar a[data-scroll='" + $(this).attr("id") + "']").addClass("active").parent().siblings().children().removeClass("active");
            }
        });
    });    

});