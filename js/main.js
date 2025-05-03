(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();

    // Initiate the wowjs
    new WOW().init();

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: true,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });

    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        margin: 24,
        dots: true,
        loop: true,
        nav : false,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });

})(jQuery);

// Navbar hide on scroll (vanilla JS)
let lastScrollTop = 0;
const navbar = document.getElementById("mainNavbar");
const scrollThreshold = 100; // Only trigger if scroll exceeds 100px

window.addEventListener("scroll", function () {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Ensure scrollDifference exceeds threshold
    const scrollDifference = Math.abs(scrollTop - lastScrollTop);

    if (scrollTop > scrollThreshold && scrollDifference > 50) {
        // Scrolling down after threshold -> hide navbar
        if (scrollTop > lastScrollTop) {
            navbar.style.top = "-80px"; // Hide navbar (adjust height if needed)
        } else {
            navbar.style.top = "0"; // Show navbar
        }
    } else if (scrollTop <= scrollThreshold) {
        // Show navbar when at the top of the page
        navbar.style.top = "0";
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Prevent negative scrollTop
});;