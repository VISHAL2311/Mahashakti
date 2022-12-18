<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Get Free Estimate Form {{$SITE_NAME}}</title>
        <style type="text/css">
            body {}

            table {
                border-collapse: collapse
            }

            table td {
                border-collapse: collapse
            }

            img {
                border: none;
            }
        </style>
    </head>

    <body style="padding:15px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top">
                    <table width="600" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, Helvetica, sans-serif">

                        <tr>
                            <td valign="middle"><a href="{{url('/')}}" title="{{$SITE_NAME}}" target="_blank"><img src="{!! App\Helpers\resize_image::resize($FRONT_LOGO_ID,236,135) !!}" alt="{{$SITE_NAME}}"></a></td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #ccc;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" valign="top" bgcolor="#fff" style="padding:20px 0;">
                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">


                                    <tr>
                                        <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px;line-height:30px;">Dear Administrator,</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px;line-height:24px;">{{ $first_name }} is interested in {{ $services_name }} and requested a free estimate. Please find the requested details as follows.</td>
                                    </tr>
                                    <tr>
                                        <td align="center" valign="top">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td height="20" align="left" valign="middle" style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:30px;">Enquiry Details:</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>Name:</strong> {{ $first_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>Email:</strong> <a href="mailto:{{ $email }}" target="_blank" style="text-decoration:none; color:#000;" title="{{ $email }}">{{ $email }}</a></td>
                                                </tr>
                                                @if(isset($phone_number) && !empty($phone_number))
                                                <tr>
                                                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>Phone:</strong> {{ $phone_number }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($services_name) && !empty($services_name))
                                                <tr>
                                                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>Interested In:</strong> {{ $services_name }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($user_message) && !empty($user_message))
                                                <tr>
                                                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>Message:</strong> {!! nl2br($user_message) !!}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td> 
                                    </tr>
                                    <tr>
                                        <td style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:24px;"><strong>Best Regards,</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:24px"><a href="{{ url('/') }}" target="_blank" style="font-family:Arial, Helvetica, sans-serif; text-decoration:none; color:#000; font-size:15px;" title="{{ $SITE_NAME }}">{{ $SITE_NAME }}</a></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">&nbsp;</td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="responsive_width" style="padding:20px;">
                        <tr>
                            <td width="100%" style="margin: 0;padding:0px 15px 10px 5px;">
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
                                <img alt="{{ Config::get('Constant.SITE_NAME') }} Pinterest" src="{{url('assets/images/socials/pinterest.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Pinterest" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                                @endif
                                @if(isset($yelpLink) && !empty($yelpLink))
                                <a href="{{$yelpLink}}" style="color:#ededed;">
                                <img alt="{{ Config::get('Constant.SITE_NAME') }} Yelp" src="{{url('assets/images/socials/yelp.png')}}" title="{{ Config::get('Constant.SITE_NAME') }} Yelp" style="display: inline-block;" width="32" height="32" border="0"></a>&nbsp;
                                @endif
                                </span>
                            </td>
                        </tr>
                        <tr style="width:100%">
                            <td style="padding:0; font-size:14px; color:#484659;">
                                <p style="font-family:'Segoe UI','Segoe WP','Segoe UI Regular','Helvetica Neue',Helvetica,Tahoma,'Arial Unicode MS',Sans-serif;padding:0;margin:0">{!!Config::get('Constant.FOOTER_COPYRIGHTS')!!} {!! date('Y') !!} <a href="{{ url('/') }}">{{ Config::get('Constant.SITE_NAME') }}</a>. All Rights Reserved.</p>
                            </td>
                        </tr>
                        <tr style="width:100%">
                            <td style="font-family:Arial, Helvetica, sans-serif; font-size:13px; ; line-height:16px;">Powered by <a href="https://netclues.com/" rel="nofollow" target="_blank" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; ; line-height:16px;">Netclues!</a></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>