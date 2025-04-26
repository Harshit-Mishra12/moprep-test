
  
$('.country-sec').owlCarousel({
    navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
    loop: true,
    items: 2,
    margin: 5,
    // animateOut: 'fadeOut',
    nav: true,
    dots: true,
    autoplay: true,
    left: true,
    freeDrag: false,
    lazyLoad: true,
    autoplayTimeout: 9000,
    autoplayHoverPause: true,
    smartSpeed: 250,
    autoHeight:true,
    responsiveClass: true,
    responsive: {
        0: {
			items:1,
            nav: true,
            dots: true,
        },
        600: {
			items:3,
            nav: false,
            dots: true,
        },
        1000: {
			items:5,
            nav: true,
            dots: true,
        },

        1400: {
			items:5,
            nav: true,
            dots: true,
        }
     },
  });

  
$('.testimonials-sec').owlCarousel({
    navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
    // loop: true,
    items: 3,
    margin: 0,
    // animateOut: 'fadeOut',
    nav: true,
    dots: true,
    autoplay: true,
    left: true,
    freeDrag: false,
    lazyLoad: true,
    autoplayTimeout: 9000,
    autoplayHoverPause: true,
    smartSpeed: 250,
    autoHeight:true,
    responsiveClass: true,
    responsive: {
        0: {
			items:1,
            nav: true,
            dots: true,
        },
        600: {
			items:3,
            nav: false,
            dots: true,
        },
        1000: {
			items:3,
            nav: true,
            dots: true,
        },

        1400: {
			items:3,
            nav: true,
            dots: true,
        }
     },
  });