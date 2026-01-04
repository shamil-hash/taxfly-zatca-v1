<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Purchase Details</title>

    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
        <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
             .btn-primary{
            background-color: #187f6a;
            color: white;
        }
        th,
        td {
            border: 1.5px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }
    </style>
</head>

@php
    use App\Models\Softwareuser;
    use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $adminroles = DB::table('adminusers')
    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
    ->where('user_id', $adminid)
    ->get();
@endphp

<body>
    <!-- Page Content Holder -->
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:15px;margin-top:18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
        <!-- content -->
        <h2>Purchase Details</h2>
        <input type="hidden" id="receipt_no" value="{{$receipt_no}}">

        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Buying Cost</th>
                    <th>Date and Time</th>
                   <th>Price without {{$tax}}</th>
                    <th>Value Added Tax</th>
                    <th>Price with {{$tax}}</th>
                    <th>Barcode</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                <tr>
                     <td class="hide">{{ $detail->product_id }}</td>
                    <td>
                        {{ $detail->product_name }}
                    </td>
                    <td>
                        {{ $detail->quantity }}
                    </td>
                    <td>
                        {{ $detail->unit }}
                    </td>
                    <td>
                            <b>{{ $currency }}</b> {{ $detail->buycost }}
                        </td>
                    <td>
                        {{ \Carbon\Carbon::parse($detail->created_at)->format('d-M-Y | h:i:s A') }}
                    </td>
                    <td>
                            <b>{{ $currency }}</b> {{ $detail->price_without_vat }}
                        </td>
                        <td>
                           <b>{{ $currency }}</b> {{ $detail->price - $detail->price_without_vat }}
                        </td>
                        <td><b>{{ $currency }}</b> {{ $detail->price }}</td>
                   <td>
                        <button class="btn btn-primary open-modal"
                            data-receipt="{{ $receipt_no }}"
                            data-quantity="{{ $detail->quantity }}"
                            data-product_name="{{ $detail->product_id }}">
                            Print Barcode
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- content end -->
    </div>

    <!-- Barcode Modal -->
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeModalLabel">Print Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal_receipt_no">
                    <input type="hidden" id="modal_product_name">
                    <label for="quantity">Enter Quantity:</label>
                    <input type="number" id="quantity" class="form-control" min="1">
                    <small class="text-danger" id="error-message" style="display: none;">Quantity exceeds available stock!</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="printBarcode" class="btn btn-success">Print Barcode</button>
                </div>
            </div>
        </div>
    </div>
     <script>
        $(document).ready(function () {
            $(".open-modal").click(function () {
                var receiptNo = $(this).data("receipt");
                var product_name = $(this).data("product_name");
                var maxQuantity = $(this).data("quantity");

                $("#modal_receipt_no").val(receiptNo);
                $("#modal_product_name").val(product_name);
                $("#quantity").val(maxQuantity).attr("max", maxQuantity); // Set default value to maxQuantity
                $("#error-message").hide();
                $("#barcodeModal").modal("show");
            });

            $("#quantity").on("input", function () {
                var enteredValue = parseInt($(this).val());
                var maxQuantity = parseInt($(this).attr("max"));

                if (enteredValue > maxQuantity) {
                    $("#error-message").show();
                    $("#printBarcode").prop("disabled", true);
                } else {
                    $("#error-message").hide();
                    $("#printBarcode").prop("disabled", false);
                }
            });

            $("#printBarcode").click(function () {
                var receiptNo = $("#modal_receipt_no").val();
                var product_name = $("#modal_product_name").val();
                var quantity = $("#quantity").val();

                if (quantity == "" || parseInt(quantity) <= 0) {
                    alert("Please enter a valid quantity!");
                    return;
                }

                // Redirect to barcode printing page (replace with actual logic)
                window.location.href = "/print-barcode?receipt_no=" + receiptNo + "&quantity=" + quantity + "&product_name="+product_name;
            });
        });
    </script>
</body>

</html>
