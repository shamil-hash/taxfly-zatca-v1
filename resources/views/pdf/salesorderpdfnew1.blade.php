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
                <div class="confidentiality-title">STATEMENT OF CONFIDENTIALITY</div>
                <p>Here,</p>
                <p>1) <span>PLEXPAY TECHNOLOGIES</span> IS REFERRED AS <span>"FIRST PARTY"</span></p>
                <p>2) <span>"{{ $custs }}"</span> IS REFERRED AS <span>"SECOND PARTY"</span></p><br><br>
                <p>
                    This Proposal is submitted to <span>"SECOND PARTY"</span> for the purpose of evaluating
                    <span>"FIRST PARTY",</span> methodologies and methods with respect to <span>"FIRST PARTY"</span>.
                    This proposal and any other information disclosed during discussions of this engagement represent
                    the proprietary, confidential information pertaining to <span>"FIRST PARTY"</span>. Other products
                    and brand names may be trademarks or registered trademarks of their respective owners.
                </p><br><br>
                <p>
                    BY ACCEPTING THIS proposal, <span>"SECOND PARTY"</span> AGREES THAT THE INFORMATION
                    IN THIS PROPOSAL WILL NOT BE DISCLOSED OUTSIDE AND WILL NOT BE DUPLICATED,
                    USED, OR DISCLOSED FOR ANY PURPOSE OTHER THAN TO EVALUATE THIS proposal.
                    This proposal is subject to a mutually approved agreement or contract
                    specifying full terms and conditions.
                </p><br><br>
                <p>
                    The contents of this document are provided to <span>"SECOND PARTY"</span> in confidence
                    solely for evaluating whether the contract should be awarded to <span>"FIRST PARTY"</span>.
                </p>
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
