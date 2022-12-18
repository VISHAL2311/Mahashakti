// testimonial Slider Home Page
$(document).ready(function(){
	$('.testimonials_slider').owlCarousel({		
		items:3,
		loop:false,		
		dots: true,
		autoplay:true,
		dotsEach: 1,
		margin:45,
		smartSpeed:1500,
		nav: false,
		responsive:{
			0:{
				items:1,
				margin:15,	
			},					
			578:{
				items:2,
				margin:15,		
			},					
			1025:{
				items:3,
				margin:30,	
				autoplay: false,
				loop: false,	
			},
			1366:{
				margin:45,
			}	
		}
	});	
	$( ".owl-prev").html('<i class="fa fa-angle-left"></i>');
	$( ".owl-next").html('<i class="fa fa-angle-right"></i>');
})
// Boat Slider Home Page
var boatSlider = new Swiper(".boat-card-slider", {
	slidesPerView: 2.3,
	centeredSlides: false,
	slidesPerGroupSkip: 1,
	grabCursor: true,
	spaceBetween: 60,
	autoplay: true,
	autoplay: {
	    delay: 3000,
	},
	loop: true,
	keyboard: {
		enabled: true,
	},
	breakpoints: {
		0:{
			slidesPerView: 1,
			spaceBetween: 0,
			loop: true,
		},
		768: {
			slidesPerView: 2,
			spaceBetween: 15,
			loop: true,
		},
		1025: {
			slidesPerView: 2.33,
			spaceBetween: 30,
		},
		1367: {
			slidesPerView: 2.33,
			spaceBetween: 60,
		},

	},
	scrollbar: {
		el: ".swiper-scrollbar",
	},
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
	pagination: {
		el: ".swiper-pagination",
		clickable: true,
	},
	on: {
	    init() {
	      this.el.addEventListener('mouseenter', () => {
	        this.autoplay.stop();
	      });

	      this.el.addEventListener('mouseleave', () => {
	        this.autoplay.start();
	      });
	    }
	  },
});


// Gallery Slider Home Page
$(document).ready(function() {
	if ( $(window).width() <= 767 ) {
	  startCarousel();
	} else {
	  $('.gallary_slider.owl-carousel').addClass('off');
	}
});  
$(window).resize(function() {
	if ( $(window).width() < 767 ) {
	startCarousel();
	} else {
	stopCarousel();
	  }
});  
function startCarousel(){
	$(".gallary_slider").owlCarousel({    
		items : 2,
		itemsMobile : [600,1],
		navigationText:["",""],
		pagination:true,
		autoplay:true,
		margin: 10,
		dots: false,
		loop:true,
		nav:true,
		navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"],

		responsive:{
			0:{
				items:1,	
			},					
			578:{
				items:2,	
			},	
		}

	});
}
function stopCarousel() {
	var owl = $('.gallary_slider.owl-carousel');
	owl.trigger('destroy.owl.carousel');
	owl.addClass('off');
}
// Service Slider Home Page
$(document).ready(function(){
$(".service_slider").owlCarousel({    
	items : 3,
	dots: false,
	loop: false,
	nav: true,
	navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"],
	responsive:{
		0:{
			items:1,
			autoplay:true,
			loop: true,
		},					
		578:{
			autoplay:true,
			loop: true,
			items:2,
			margin: 20,
		},
		1024:{
			loop: false,
			items: 2,
			margin: 30,
		}	
	}
});
});