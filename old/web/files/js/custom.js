;(function($, window, undefined) {

	"use strict";

	jQuery(document).ready(function ($) {



		$(document).on('scroll', function () {
		    // if the scroll distance is greater than 100px
		    if ($(window).scrollTop() > 100) {
		      // do something
		    	$('.site-header').addClass('scrolled-header');
		    }
		    else {
		    	$('.site-header').removeClass('scrolled-header');
		    }
		});
		






		// Animation on scroll 
		new WOW().init();


		// Portfolio Isotope Filter

		// init Isotope
		var portfolioGrid = $('#portfolio-grid');

		portfolioGrid.imagesLoaded(function(){
		    portfolioGrid.isotope({
			    itemSelector: '.item',
			    layoutMode: 'fitRows',
			    "masonry": { "columnWidth": ".portfolio-grid-sizer" }
			});
		});

      	// filter functions
		var filterFns = {
		    // show if number is greater than 50
		    numberGreaterThan50: function() {
		      var number = $(this).find('.number').text();
		      return parseInt( number, 10 ) > 50;
		    },
		    // show if name ends with -ium
		    ium: function() {
		      var name = $(this).find('.name').text();
		      return name.match( /ium$/ );
		    }
		};

      	// bind filter button click
      	$('#projects-filter').on( 'click', 'a', function() {
		    var filterValue = $( this ).attr('data-filter');
		    // use filterFn if matches value
		    filterValue = filterFns[ filterValue ] || filterValue;
		    portfolioGrid.isotope({ filter: filterValue });
		    return false;
		});

      	// change is-checked class on buttons
		$('#projects-filter').each( function( i, buttonGroup ) {
	    	var $buttonGroup = $( buttonGroup );
	    	$buttonGroup.on( 'click', 'a', function() {
	      		$buttonGroup.find('.active').removeClass('active');
	      		$( this ).addClass('active');
	    	});
	  	});


		// Owl Carouse Testimonials

		 var owl = $("#owl-demo");
 
		  owl.owlCarousel({
      		
      		pagination : true,
    		paginationNumbers: false,
      		autoPlay: 5000, //Set AutoPlay to 3 seconds
		    items : 3, //10 items above 1000px browser width
		    itemsDesktop : [1000,3], //5 items between 1000px and 901px
		    itemsDesktopSmall : [900,2], // betweem 900px and 601px
		    itemsTablet: [600,1], //2 items between 600 and 0
		    itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
		  });

		  var owl = $("#owl-portfolio");
 
		  owl.owlCarousel({

		  	  pagination: false,
		      items : 3, //10 items above 1000px browser width
		      itemsDesktop : [1000,3], //5 items between 1000px and 901px
		      itemsDesktopSmall : [900,2], // betweem 900px and 601px
		      itemsTablet: [600,1], //2 items between 600 and 0
		      itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
		  });
		 
		  // Custom Navigation Events
		  $(".next").on('click' ,function(){
		    owl.trigger('owl.next');
		  })
		  $(".prev").on('click' ,function(){
		    owl.trigger('owl.prev');
		  })
		  $(".play").on('click', function(){
		    owl.trigger('owl.play',1000); //owl.play event accept autoPlay speed as second parameter
		  })
		  $(".stop").on('click', function(){
		    owl.trigger('owl.stop');
		  })



		// Submenu Show/Hide
        // $('nav.main-navigation ul > li, nav.main-navigation ul > li > ul > li').hover(function () {
        //     $(this).children('ul').stop(true, true).slideDown(200);
        // }, function () {
        //     $(this).children('ul').stop(true, true).slideUp(200);
        // });

		
		$('nav.main-navigation > ul > li').each(function(){
			$(this).find('.has-submenu').append('<i class="fa fa-angle-down"></i>');
		});


        // Blog Masonry
        var blogIsotope=function(){
            var imgLoad = imagesLoaded($('.blog-isotope'));
		   
            imgLoad.on('done',function(){

                $('.blog-isotope').isotope({
                    "itemSelector": ".blog-post",
                });
               
            })
           
           imgLoad.on('fail',function(){

                $('.blog-isotope').isotope({
                    "itemSelector": ".blog-post",
                });

           })  
           
        }
                   
        blogIsotope();



        // Flickr Images
        $('.flickr-images').jflickrfeed({
			limit: 6 ,
			qstrings: {id: '56174287@N02'},
			itemTemplate: '<li class="small-thumb"><a href="{{link}}" title="{{title}}"><img src="{{image_s}}" alt="{{title}}" /></a></li>'
		});



		// Off Canvas Navigation
		var offcanvas_open = false;
		var offcanvas_from_left = false;

		function offcanvas_right() {
			
			$(".sidebar-menu-container").addClass("slide-from-left");
			$(".sidebar-menu-container").addClass("sidebar-menu-open");		
			
			offcanvas_open = true;
			offcanvas_from_left = true;
			
			$(".sidebar-menu").addClass("open");
			$("body").addClass("offcanvas_open offcanvas_from_left");

			$(".nano").nanoScroller();
			
		}

		function offcanvas_close() {
			if (offcanvas_open === true) {
					
				$(".sidebar-menu-container").removeClass("slide-from-left");
				$(".sidebar-menu-container").removeClass("sidebar-menu-open");
				
				offcanvas_open = false;
				offcanvas_from_left = false;
				
				//$('#sidebar-menu-container').css('max-height', 'inherit');
				$(".sidebar-menu").removeClass("open");
				$("body").removeClass("offcanvas_open offcanvas_from_left");

			}
		}

		$(".side-menu-button").on('click', function() {
			offcanvas_right();
		});

		$("#sidebar-menu-container").on("click", ".sidebar-menu-overlay", function(e) {
			offcanvas_close();
		});

		$(".sidebar-menu-overlay").swipe({
			swipeLeft:function(event, direction, distance, duration, fingerCount) {
				offcanvas_close();
			},
			swipeRight:function(event, direction, distance, duration, fingerCount) {
				offcanvas_close();
			},
			tap:function(event, direction, distance, duration, fingerCount) {
				offcanvas_close();
			},
			threshold:0
		});


		// Mobile navigation
		$(".responsive-menu .menu-item-has-children").append('<div class="show-submenu"><i class="fa fa-chevron-circle-down"></i></div>');

	    $(".responsive-menu").on("click", ".show-submenu", function(e) {
			e.stopPropagation();
			
			$(this).parent().toggleClass("current")
							.children(".sub-menu").toggleClass("open");
							
			$(this).html($(this).html() == '<i class="fa fa-chevron-circle-down"></i>' ? '<i class="fa fa-chevron-circle-up"></i>' : '<i class="fa fa-chevron-circle-down"></i>');
			$(".nano").nanoScroller();
		});

		$(".responsive-menu").on("click", "a", function(e) {
			if( ($(this).attr('href') === "#") || ($(this).attr('href') === "") ) {
				$(this).parent().children(".show-submenu").trigger("click");
				return false;
			} else {
				offcanvas_close();
			}
		});


		// revolution slider
		$('.fullwidthbanner').revolution({
        	delay:6000,
        	startwidth:1170,
        	startheight:680,
        	onHoverStop: "off",
        	hideTimerBar: "on",
            thumbWidth: 100,
            thumbHeight: 50,
            thumbAmount: 3,
            hideThumbs: 200,
            navigationType: "bullet",
            navigationArrows: "verticalcentered",
            navigationStyle: "preview4",
            touchenabled: "on",
            navOffsetHorizontal: 0,
            navOffsetVertical: 20,
            stopAtSlide: -1,
            stopAfterLoops: -1,
            hideCaptionAtLimit: 0,
            hideAllCaptionAtLilmit: 0,
            hideSliderAtLimit: 0,
            hideThumbsOnMobile:"on",
         	hideNavDelayOnMobile:1500,
         	hideBulletsOnMobile:"on",
         	hideArrowsOnMobile:"on",
         	hideThumbsUnderResoluition:0,
            fullWidth: "on",
            shadow: 0
      	});



		//  go to top
      	var offset = 300,
		//browser window scroll (in pixels) after which the "back to top" link opacity is reduced
		offset_opacity = 1200,
		//duration of the top scrolling animation (in ms)
		scroll_top_duration = 700,
		//grab the "back to top" link
		$back_to_top = $('.go-top');

		//hide or show the "back to top" link
		$(window).on('scroll', function(){
			( $(this).scrollTop() > offset ) ? $back_to_top.addClass('go-top-visible') : $back_to_top.removeClass('go-top-visible go-top-fade-out');
			if( $(this).scrollTop() > offset_opacity ) { 
				$back_to_top.addClass('go-top-fade-out');
			}
		});

		//smooth scroll to top
		$back_to_top.on('click', function(event){
			event.preventDefault();
			$('body,html').animate({
				scrollTop: 0 ,
			 	}, scroll_top_duration
			);
		});

		
		


	});

})(jQuery, window);
