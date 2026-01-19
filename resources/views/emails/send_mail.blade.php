<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ $data['title'] ?? '' }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;margin:30px 0;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05);">

                <!-- Header -->
                <tr>
                    <td style="background:#2563eb;color:#fff;padding:20px;text-align:center;font-size:22px;font-weight:bold;">
                        {{ $data['title'] }}
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px;color:#333;font-size:16px;line-height:1.7;text-align:center;">
                        <p style="margin:0 0 20px 0;">
                            {{ $data['message'] }}
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f1f5f9;padding:15px;text-align:center;color:#777;font-size:13px;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
