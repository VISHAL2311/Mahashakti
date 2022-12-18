/* Menu Open Function S */

function openNav() {

    document.getElementById("menu").style.left = "0";

    document.getElementById("menu__open").style.display = "none";

    document.getElementById("menu__close").style.display = "inline-block";

    $("html").addClass('html_overlap');

    $("body").addClass('menu_overlap');

}

/* Menu Open Function E */



/* Menu Close Function S */

function closeNav() {

    document.getElementById("menu").style.left = "-100%";

    document.getElementById("menu__open").style.display = "inline-block";

    document.getElementById("menu__close").style.display = "none";

    $("html").removeClass('html_overlap');

    $("body").removeClass('menu_overlap');

    $(".menu .is-open").removeClass("is-close");

}

/* Menu Close Function E */



/* Responsive Class Add S */

$( document ).ready(function() {

    $(".menu ul li").find("ul").before('<span class="is-open"></span>');

    $(".menu ul li").find("ul").parent("li").addClass("is-open-a");

    $(".menu .is-open").click(function(event) {

        event.preventDefault();

        $(this).toggleClass("is-close");

    });

});

/* Responsive Class Add E */



/* Tab Menu Height Calc S */

$("#menu > ul > li > a").hover(function(){

    var liMaxHeight = -1;

    $(".tab-menu li ul").each(function(index) {

        if ($(this).outerHeight() > liMaxHeight) {

            liMaxHeight = $(this).outerHeight();

        }

    });

    $(".tab-menu").css("min-height", liMaxHeight);

    $(".tab-menu > li > ul").css("min-height", liMaxHeight);

    console.log($(".tab-menu > li > ul").css("min-height", liMaxHeight));

});

/* Tab Menu Height Calc E */



/* Hide Header on on scroll down S */

var didScroll;

var lastScrollTop = 0;

var delta = 5;

var navbarHeight = $('header').outerHeight();



$(window).scroll(function(event){

    didScroll = true;

});



// setInterval(function() {

//     if (didScroll) {

//         hasScrolled();

//         didScroll = false;

//     }

// }, 250);



// function hasScrolled() {
//     var st = $(this).scrollTop();
//     if(Math.abs(lastScrollTop - st) <= delta)
//         return;
//     if (st > lastScrollTop && st > navbarHeight){
//         $('header').removeClass('nav-down').addClass('nav-up');
//     } else {
//         if(st + $(window).height() < $(document).height()) {
//             $('header').removeClass('nav-up').addClass('nav-down');
//         }
//     }    
//     lastScrollTop = st;
// }
$(document).scroll(function (e){
  var scroll = $(window).scrollTop();
    if (scroll > $('header').height()) {
     $('header').addClass('header-sticky');
    }else {
     $('header').removeClass('header-sticky');
    }
    if (scroll > 100) {
         $('header').addClass('fadeInDown');
        }
        else {    
         $('header').removeClass('fadeInDown');
    }
});

/* Hide Header on on scroll down E */



/* Center Logo JS S */

function brandCenter() {

    var getNav = $("nav.menu");

    if (getNav.hasClass("brand-center")) {

        var postsArr = new Array(),

            index = $("nav.brand-center"),

            $postsList = index.find('ul.brand-navbar');



        //Create array of all posts in lists

        index.find('ul.brand-navbar > li').each(function() {

            postsArr.push($(this).html());

        });



        //Split the array at this point. The original array is altered.

        var firstList = postsArr.splice(0, Math.round(postsArr.length / 2)),

            secondList = postsArr,

            ListHTML = '';



        var createHTML = function(list) {

            ListHTML = '';

            for (var i = 0; i < list.length; i++) {

                ListHTML += '<li>' + list[i] + '</li>';

            }

        }



        //Generate HTML for first list

        createHTML(firstList);

        $postsList.html(ListHTML);

        index.find("ul.brand-nav").first().addClass("navbar-left");



        //Generate HTML for second list

        createHTML(secondList);

        $postsList.after('<ul class="brand-nav brand-navbar"></ul>').next().html(ListHTML);

        index.find("ul.brand-nav").last().addClass("navbar-right");



        //Wrap navigation menu

        index.find("ul.brand-nav.navbar-left").wrap("<div class='col_half left'></div>");

        index.find("ul.brand-nav.navbar-right").wrap("<div class='col_half right'></div>");

    }

}

brandCenter ();

/* Center Logo JS E */

/** Start Code for active class */
$(document).ready(function() {

// Get current page URL
var url = window.location.href;

// select file name
url = url.substr(url.lastIndexOf("/") + 1);

// If file name not avilable
if(url == ''){
url = 'www.douglasconstructiongroup.com';
}

// Loop all menu items
$('.brand-navbar li').each(function(){

 // select href
 var href = $(this).find('a').attr('href');

 href = href.substr(href.lastIndexOf("/") + 1);

 // Check filename
 if(url == href){

  // Select parent class
  var parentClass = $(this).parent('ul').attr('class');

  if(parentClass == 'sub-menu'){

   // Add class
   $(this).parents('.brand-navbar li').addClass('active');
  }else{

   // Add class
   $(this).addClass('active');
  }

 }
});

/** Start Code for detilas page active class */

// Get current page URL
var urlsegment = window.location.href;  

// Get second last segment page URL
var secondlastsegment = urlsegment.split('/').reverse()[1]; 

// Loop all menu items
$('.brand-navbar li').each(function(index, value){

    // select all menu href
    var urltxt = $(this).find('a').attr('href');

    urltxt = urltxt.substr(urltxt.lastIndexOf("/") + 1);

    // Check filename
    if(secondlastsegment != 'www.douglasconstructiongroup.com' && secondlastsegment == urltxt){
        $(this).addClass('active');
    }

});
/** End Code for detilas page active class */

});
/** End Code for active class */