<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width"/>
    <title>{{ Config::get('Constant.SITE_NAME') }}</title>
    <style>
    @media(max-width:550px) {
    .responsive_width{width:94% !important;}
    }
    </style>
  </head>
  <body style="margin:0;padding:0;background-color:#eaeff2;">
    <center>
    <table class="mobile_device" width="100%" align="center" cellpadding="15" cellspacing="0" style="background:#eaeff2;font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;width:100%;margin:auto;">
      <tbody>
        <tr>
          <td>
            <table cellpadding="10" cellspacing="0" class="responsive_width" width="550px" style="width:550px;margin:auto;background-color:#fff;text-align:center;">
              <tbody>
                <tr>
                  <td width="100%" style="padding:20px 20px 15px" align="center">
                    <a href="{{url('/')}}" title="{{ Config::get('Constant.SITE_NAME') }}">
                      <img src="{!! App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID'),300,200) !!}" alt="{{ Config::get('Constant.SITE_NAME') }}" style="height:34px;width:150px" />
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
            <table width="550px" cellspacing="0" class="responsive_width" cellpadding="0" style="width:550px;margin:auto;background-color:#FFF;">
              <tbody>
                <tr>
                  <td>
                    <table width="100%" cellspacing="0" cellpadding="0" style="padding:20px;background:#f4f4f4">
                      <tr>
                        <td style="font-size:16px;" align="center">
                          <span style="color:#2574db; font-size:24px; font-weight:normal; font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif; display:block; text-align: center;margin-bottom: 10px;">Dear {{ $user->name}},</span>
                          <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;margin:15px 0 10px;padding:0;color:#333333">A request received to reset the password of {{ Config::get('Constant.SITE_NAME') }} account</p>
                          <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;margin:15px 0 10px;padding:0;color:#333333">Click on the link below to set a new password: </p>
                          <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;margin:15px 0 10px;padding:0;color:#333333"><a href="{{ url('powerpanel/password/reset', $resetToken).'?email='.urlencode($user->email) }}" style="background:#2574db;padding:6px 15px;display:inline-block;text-decoration:none;color:#fff;font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;font-size:16px;margin:8px 0 0 0;font-weight:bold;" target="_blank">RESET LINK</a></p>
                          <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;margin:15px 0 10px;padding:0;color:#333333">If you don’t want to change your password, you can ignore this email.</p>
                        </td>
                      </tr>
                    </table>
                    <table width="100%" cellspacing="0" cellpadding="0" style="padding:20px;background:#f8f8f8">
                    <tr>
                      <td style="font-size:16px;padding: 15px 0 0">
                        {!!Config::get('Constant.DEFAULT_SIGNATURE_CONTENT')!!}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
          <table cellpadding="0" cellspacing="0" class="responsive_width" width="550px" style="padding:20px;width:550px;margin:auto;">
            <tr>
              <td width="100%" style="margin: 0;padding:0px 15px 10px 5px;" align="center">
                <span>
                @php
                  $facebookLink = Config::get('Constant.SOCIAL_FB_LINK');
                  $twitterLink = Config::get('Constant.SOCIAL_TWITTER_LINK');
                  $instagramLink = Config::get('Constant.SOCIAL_INSTAGRAM_LINK');
                  $pinterestLink = Config::get('Constant.SOCIAL_PINTEREST_LINK');
                  $yelpLink = Config::get('Constant.SOCIAL_YELP_LINK');
                @endphp
                @if(isset($facebookLink) && !empty($facebookLink))
                  <a href="{{ $facebookLink }}" style="color:#ededed;">
                  <img src="{{url('assets/images/socials/facebook_icon2.png')}}" alt="{{ Config::get('Constant.SITE_NAME') }} Facebook" style="display: inline-block;" width="32" height="32" border="0" title="{{ Config::get('Constant.SITE_NAME') }} Facebook"></a>&nbsp;
                @endif
                @if(isset($twitterLink) && !empty($twitterLink))
                  <a href="{{$twitterLink}}" style="color:#ededed;">
                  <img alt="{{ Config::get('Constant.SITE_NAME') }} Twitter" src="{{url('assets/images/socials/twitter_icon2.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Twitter" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                @endif
                @if(isset($instagramLink) && !empty($instagramLink))
                  <a href="{{$instagramLink}}" style="color:#ededed;">
                  <img alt="{{ Config::get('Constant.SITE_NAME') }} Instagram" src="{{url('assets/images/socials/linkedin_icon2.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Instagram" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                @endif
                @if(isset($pinterestLink) && !empty($pinterestLink))
                  <a href="{{$pinterestLink}}" style="color:#ededed;">
                  <img alt="{{ Config::get('Constant.SITE_NAME') }} Pinterest" src="{{url('assets/images/socials/youtube_icon2.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Pinterest" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                @endif
                @if(isset($yelpLink) && !empty($yelpLink))
                  <a href="{{$yelpLink}}" style="color:#ededed;">
                  <img alt="{{ Config::get('Constant.SITE_NAME') }} Yelp" src="{{url('assets/images/socials/youtube_icon2.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Yelp" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                @endif
                </span>
              </td>
            </tr>
            <tr style="width:100%">
              <td style="text-align:center;padding:0; font-size:14px; color:#484659;">
                <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;padding:0;margin:0">{!!Config::get('Constant.FOOTER_COPYRIGHTS')!!} {!! date('Y') !!} <a href="{{ url('/') }}">{{ Config::get('Constant.SITE_NAME') }}</a>. All Rights Reserved.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  </center>
</body>
</html>