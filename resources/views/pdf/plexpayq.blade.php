<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A4 Page Layout</title>

    <style>
        /* @page {
            size: A4;
            margin: 20mm;
        } */

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            position: relative;
        }

        .header {
            background: #003366;
            color: #ffffff;
            padding: 20px;
            text-align: left;
            margin-left:-50px;
            margin-top:-50px;
            /* width: 100%; */
            width: 950px; /* Prevents overflow */
        }

        img {
            height: 30px;
            width: auto;
            margin-left: 30px;
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
            max-width: 80%; /* Prevents overflow */
            margin: auto;
        }

        .content p {
            margin: 10px 0;
            font-size: 14px;
        }

        .content .title {
            font-size: 30px;
            font-weight: bold;
            color: #003f63;
        }

        .content .main-heading {
            font-size: 24px;
            font-weight: bold;
            color: #005b8f;
            text-transform: uppercase;
        }

        .content .sub-heading {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content .secondary-text {
            font-size: 24px;
            text-transform: uppercase;
            color: #444;
            font-weight: 900;

        }

        .content .secondarys-text {
            font-size: 14px;
            text-transform: uppercase;
            color: #444;
            font-weight: 900;

        }

        .footer {
        position: absolute;
        bottom: 0;
        width: 760px; /* Prevents overflow */
        font-size: 14px;
        color: #ffffff;
        background-color: #003366;
        padding: 10px 20px;
        display: flex;
        align-items: center;
    }

    .footer-left {
        flex: 1; /* Occupies space on the left */
        text-align: left;
    }

    .footer-right {
        flex: 1; /* Occupies space on the right */
        text-align: right;
    }

    .footer p {
        margin: 0;
    }
        .contact-info p {
            margin: 5px 0;
            font-size: 12px;
            margin-left: 30px;

        }

        .contact-info .contact {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    {{-- <div class="page"> --}}
        <!-- Header -->
        <div class="header">
            <div style="margin-top:20px;">

                <img src="{{ public_path('images/logoimage/plexwhite.png') }}" alt="PLEXPAY Logo">
                <div class="contact-info">
                    <p>PLEXPAY TECHNOLOGIES LLC</p>
                    <p>#116, 1st FLOOR, COMPUTER PLAZA</p>
                    <p>Dubai, UAE</p>
                    <p>+971 56 942 9454</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content" style="margin-top: 100px;">
            <p class="title" style="color: #21336d;font-weight:bold;">PROPOSAL FOR</p><br>
            <img src="{{ public_path('images/logoimage/plexpay.jpg') }}" alt="Branch Logo" class="imagecss" style="padding-bottom:15px;width:300px;height:80px;margin-left:-2px;">
            <p class="secondary-text" style="margin-top:-20px;">PLEXPAY BILLING</p><br><br><br>
            <p class="secondary-text" style="color: #0049ab;">{{ $custs }}</p>
            <p class="secondary-text">{{ $CAddress }}</p>
            <p class="secondarys-text" style="margin-top: 350px;">Project Contract for 1 Year Support Project Time - 45 Days</p>
        </div>

        <!-- Footer -->
        <div class="footer" style="margin-left:-50px;">
            <div class="footer-left" >
                <p style="margin-top: 10px;">www.plexpay.ae</p>
            </div>
            <div class="footer-right">
                <p style="margin-top: -20px;">+971 56 942 9454</p>
            </div>
        </div>
    {{-- </div> --}}
</body>

</html>
