</div>
<script>
    function hidd() {
        $("span.error").hide();
    }
    $(document).ready(function() {
        checkCookie_Footer();
    });
    function setCookie_Footer(c_name, value, exdays) {
        var exdate = new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
        document.cookie = c_name + "=" + c_value;
    }

    function getCookie_Footer(c_name) {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++) {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == c_name) {
                return unescape(y);
            }
        }
    }

    function checkCookie_Footer() {
        var popup = getCookie_Footer("popup");
        if (popup != 'Y') {
            document.getElementById('js-gdpr-consent-banner').style.display = '';
        } else {
            document.getElementById('js-gdpr-consent-banner').style.display = 'none';
            jQuery("#js-gdpr-consent-banner").html('');
        }
    }

    function GetGDPRCLOSE() {
        setCookie_Footer("popup", 'Y', 365);
        document.getElementById('js-gdpr-consent-banner').style.display = 'none';
        jQuery("#js-gdpr-consent-banner").html('');
        return false;
    }
</script>
<!-- Main Wrapper E -->

<script src="{{ $CDN_PATH.'assets/js/custom.js'}}?{{ Config::get('Constant.VERSION') }}"></script>
@if(Request::segment(1) == 'contact' || Request::segment(1) == 'boat' || Request::segment(1) == 'services')
<script src="{{ $CDN_PATH.'assets/libraries/phone/jquery.caret.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ $CDN_PATH.'assets/libraries/phone/jquery.mobilePhoneNumber.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
@endif
<script src="{{ url('assets/libraries/slick/slick/slick.min.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ url('assets/libraries/swiper/js/swiper-bundle.min.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ url('assets/js/index.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
@if(Request::segment(1) != '')
<script src="{{ $CDN_PATH.'assets/libraries/jquery-validation/js/jquery.validate.min.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ $CDN_PATH.'assets/libraries/jquery-validation/js/additional-methods.min.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ $CDN_PATH.'assets/js/common_form_validation.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script>
    var a2a_config = a2a_config || {};
    a2a_config.onclick = 1;
    a2a_config.num_services = 4;
</script>
<script async src="https://static.addtoany.com/menu/page.js"></script>
@endif

<!-- Java Script E -->
<script>
    $('.selectpicker').selectpicker({});
    $(document).ready(function($) {
        // Set initial zoom level
        var zoom_level = 100;
        // Click events
        $('#zoom_in').click(function() {
            zoom_page(10, $(this))
        });
        $('#zoom_out').click(function() {
            zoom_page(-10, $(this))
        });
        $('#zoom_reset').click(function() {
            zoom_page(0, $(this))
        });
        // Zoom function
        function zoom_page(step, trigger) {
            // Zoom just to steps in or out
            if (zoom_level >= 120 && step > 0 || zoom_level <= 80 && step < 0) return;
            // Set / reset zoom
            if (step == 0) zoom_level = 100;
            else zoom_level = zoom_level + step;
            // Set page zoom via CSS
            $('#wrapper').css({
                transform: 'scale(' + (zoom_level / 100) + ')', // set zoom
                transformOrigin: '50% 0' // set transform scale base
            });
            // Adjust page to zoom width
            if (zoom_level > 100) $('#wrapper').css({
                width: (zoom_level * 1.01) + '%'
            });
            else $('#wrapper').css({
                width: '100%'
            });
            // Activate / deaktivate trigger (use CSS to make them look different)
            if (zoom_level >= 120 || zoom_level <= 80) trigger.addClass('disabled');
            else trigger.parents('ul').find('.disabled').removeClass('disabled');
            if (zoom_level != 100) $('#zoom_reset').removeClass('disabled');
            else $('#zoom_reset').addClass('disabled');
        }
    });
</script>
<script defer>
    var chatbox = document.getElementById('fb-customer-chat');
    chatbox.setAttribute("page_id", "590973967688689");
    chatbox.setAttribute("attribution", "biz_inbox");
  </script>
  <!-- Your SDK code -->
  <script defer>
  var _x = function(d, s, id) {
    window.fbAsyncInit = function() {
      FB.init({
        xfbml            : true,
        version          : 'v14.0'
      });
    };
  
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  };
  setTimeout(function() { _x(document, 'script', 'facebook-jssdk') }, 5000);
  </script>
@yield('footer_scripts')
</body>
<!-- Body E -->

</html>