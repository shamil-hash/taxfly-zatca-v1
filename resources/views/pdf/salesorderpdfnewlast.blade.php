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
            padding: 30px;
        }

        .content .confidentiality-box {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 5px;
        }

        .content .confidentiality-title {
            background: #000;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 14px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .content p span {
            font-weight: bold;
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
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1; /* Adjust opacity as needed */
            pointer-events: none; /* Ensures the watermark doesn't interfere with clicking elements */
            z-index: 9999;
        }
        .watermark img {
            max-width: 100%; /* Make sure it's responsive */
            height: auto;
        }
    </style>
</head>

<body>
    {{-- <div class="page"> --}}
        <!-- Header -->
        <div class="watermark">
            <img style="margin-left:10px;" src="{{ public_path('images/logoimage/plexpay.jpg') }}" alt="Watermark">
        </div>
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
        <div class="content" style="">
            <div class="confidentiality-box">
                <div class="confidentiality-title">TERMS & CONDITIONS:</div>
                <p><strong>Software:</strong> 50% Advance on confirmation with LPO balance on Implementation.</p>
<p><strong>Validity:</strong> Offer is valid for 10 days from today.</p>
<p><strong>Variations:</strong> Any variations on the quotation will be charged accordingly.</p>
<p><strong>Warranty:</strong> 1 month back-to-base warranty from Plexpay Software only, starting from the date of purchase. Plexpay Payments LLC's obligations and liabilities under the warranty shall be limited to the correction of defects or failure, provided that the equipment is operated in accordance with the manufacturer's power and environmental specifications. In case of any physical or electrical damage, the warranty will be null & void.</p><br>
<p><strong>Renewal and Payments:</strong> You will be automatically sent an invoice via email for the Renewal and Support Term fee. Plexpay makes every effort to provide you with an invoice and notice of renewal 15 days prior to the end of each Maintenance and Support Term. However, it is your responsibility to note this date and keep us informed of the email address of the primary contact for your business, so that we can reach you. If payment is not received within 15 days of the invoiced date, it will be assumed that you do not wish to renew, and your license will be terminated. No repairs, alterations, or adjustments will be made to the equipment except by the manufacturer's authorized representative.</p><br>
<p><strong>The yearly Renewal and Support Fee:</strong> The total of the current subscription plan in which server rent is included.</p><br>
<p><strong>Service Availability:</strong> Support is limited to 12 months from the date of purchase of your first Plexpay. Service availability may occasionally deviate from stated hours due to downtime for systems and server maintenance and observed public holidays. Plexpay cannot guarantee that you will not experience delays in having one of our technical support consultants answer your query, as call volumes fluctuate and so too will response time.</p>

            </div>
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
