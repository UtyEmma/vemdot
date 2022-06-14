<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Successfull Password Reset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        /**
         * Google webfonts. Recommended to include the .woff version for cross-client compatibility.
         */
        @media screen {
            @font-face {
                font-family: 'Merriweather';
                font-style: normal;
                font-weight: 400;
                src: local('Merriweather'), local('Merriweather'), url(http://fonts.gstatic.com/s/merriweather/v8/ZvcMqxEwPfh2qDWBPxn6nmB7wJ9CoPCp9n30ZBThZ1I.woff) format('woff');
            }

            @font-face {
                font-family: 'Merriweather Bold';
                font-style: normal;
                font-weight: 700;
                src: local('Merriweather Bold'), local('Merriweather-Bold'), url(http://fonts.gstatic.com/s/merriweather/v8/ZvcMqxEwPfh2qDWBPxn6nhAPw1J91axKNXP_-QX9CC8.woff) format('woff');
            }
        }

        /**
         * Avoid browser level font resizing.
         * 1. Windows Mobile
         * 2. iOS / OSX
         */
        body,
        table,
        td,
        a {
            -ms-text-size-adjust: 100%; /* 1 */
            -webkit-text-size-adjust: 100%; /* 2 */
        }

        /**
         * Remove extra space added to tables and cells in Outlook.
         */
        table,
        td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }

        /**
         * Better fluid images in Internet Explorer.
         */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /**
         * Remove blue links for iOS devices.
         */
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }

        /**
         * Fix centering issues in Android 4.4.
         */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /**
         * Collapse table borders to avoid space between cells.
         */
        table {
            border-collapse: collapse !important;
        }

        a {
            color: #CC7953;
        }

        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }
    </style>

</head>
<body style="background-color: #D2C7BA;">

<!-- start preheader -->
<div class="preheader" style="display: none; max-width: 0; max-height: 0; overflow: hidden; font-size: 1px; line-height: 1px; color: #fff; opacity: 0;">
    Successfull Password Reset On {{$user->date_format}}.
</div>
<!-- end preheader -->

<!-- start body -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">

    <!-- start logo -->
    <tr>
        <td align="center" bgcolor="#D2C7BA">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td align="center" valign="top" style="padding: 36px 24px;">
                        <a href="{{$user->settings->site_domain}}" target="_blank" style="display: inline-block;">
                            <img src="{{$user->settings->site_logo}}" alt="Logo" border="0" width="48" style="display: block; width: 48px; max-width: 48px; min-width: 48px;">
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- end logo -->

    <!-- start hero -->
    <tr>
        <td align="center" bgcolor="#D2C7BA">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 36px 24px 0; font-family: 'Merriweather Bold', serif; border-top: 5px solid #1b1363;">
                        <h3 style="margin: 0;  line-height: 28px; text-align:center">Successfull Password Reset on {{$user->date_format}}.</h3>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- end hero -->

    <!-- start copy block -->
    <tr>
        <td align="center" bgcolor="#D2C7BA">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr>
                    <td align="center" valign="top" width="600">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

                <!-- start copy -->
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Merriweather', serif; font-size: 16px; line-height: 24px;">
                        <p style="margin: 0;">Successfull Password Reset</p>
                    </td>
                </tr>
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Merriweather', serif; font-size: 16px; line-height: 24px;">
                        <p style="margin: 0;">Hi, <b>{{$user->name}}</b></p>
                        <p style="margin: 0;">You recieved this mail, because on the {{$user->date_format}} you placed request to reset your password.  Login to your {{ucfirst($user->settings->site_name)}} account to explore our awesome features</p>
                    </td>
                </tr>
                <!-- end copy -->
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Merriweather', serif; font-size: 16px; line-height: 24px;">
                       <p style="margin: 0;">If you did not initiate this request on your {{ucfirst($user->settings->site_name)}} account, kindly contact {{ucfirst($user->settings->site_name)}} (our 24/7 Live Support) or send an email to <a href="mailto::{{$user->settings->site_email}}">{{$user->settings->site_email}}</a>, stating your name and email.</p>
                    </td>
                </tr>

                <!-- start copy -->
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Merriweather', serif; font-size: 16px; line-height: 24px; border-bottom: 5px solid #1b1363">
                        <p style="margin: 0;">Regards! {{ucfirst($user->settings->site_name)}}.</p>
                    </td>
                </tr>
                <!-- end copy -->

            </table>
        </td>
    </tr>
    <!-- end copy block -->

    <!-- start footer -->
    <tr>
        <td align="center" bgcolor="#D2C7BA" style="padding: 24px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

                <!-- start permission -->
                <tr>
                    <td align="center" bgcolor="#D2C7BA" style="padding: 12px 24px; font-family: 'Merriweather', serif; font-size: 14px; line-height: 20px; color: #666;">
                        <p style="margin: 0;">{{$user->settings->site_address}}</p>
                        <p style="margin: 0;">Powered by {{ucfirst($user->settings->site_name)}}.</p>
                    </td>
                </tr>
                <!-- end permission -->

            </table>
        </td>
    </tr>
    <!-- end footer -->

</table>
<!-- end body -->

</body>
</html>
