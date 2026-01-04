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
            font-size: 16px;
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
            font-size: 14px;
            text-transform: uppercase;
            color: #444;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 950px; /* Prevents overflow */
            font-size: 14px;
            color: #ffffff;
            background: #003366;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
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
                    <p>TRN: 104498850700003</p>
                    <p>Mob: +971 50439 4772 , +971 509286772</p>
                    </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content" style="margin-left:-50px;">
            <p class="title">Proposal for</p>
            <h1 class="main-heading">Plex Pay</h1>
            <p class="sub-heading">PX1S Terminal Machine & PlexPay Billing</p>
            <p class="secondary-text">REZZY NUTS</p>
            <p class="secondary-text">Abudhabi, UAE</p>
            <p class="secondary-text">Project Contract for 1 Year Support Project Time - 45 Days</p>
        </div>

        <!-- Footer -->
        <div class="footer" style="margin-left:-50px;">
            <p>🌐 www.plexpay.ae</p>
            <p>📞 +971 56 942 9454</p>
        </div>
    {{-- </div> --}}
</body>

</html>
