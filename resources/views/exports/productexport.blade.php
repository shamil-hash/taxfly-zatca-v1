<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export Product Data</title>
</head>

<body>
@php

use App\Models\Softwareuser;
use App\Models\Adminuser;

$userid = Session('softwareuser');
$adminid = Softwareuser::Where('id', $userid)
->pluck('admin_id')
->first();
$tax = Adminuser::Where('id', $adminid)
->pluck('tax')
->first();
@endphp


    <?php $i = 0; ?>
    @php($i=0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>product</th>
                <th>product details</th>
                <th>unit</th>
                <th>buy cost</th>
                <th>purchase vat</th>
                <th>rate</th>
                <th>Inclusive Rate</th>
                <th>Inclusive vat Amount</th>
                <th>sell cost</th>
                <th>vat</th>
                <th>category</th>
                <th>barcode</th>
            </tr>
        </thead>
        <tbody>
            @php($i=0)
            @foreach($products as $product)
            @php($i++)

            <tr>
                <td>{{ $i }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->productdetails }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ $product->buy_cost }}</td>
                <td>{{ $product->purchase_vat }}</td>
                <td>{{ $product->rate }}</td>
                <td>{{ $product->inclusive_rate }}</td>
                <td>{{ $product->inclusive_vat_amount }}</td>
                <td>{{ $product->selling_cost }}</td>
                <td>{{ $product->vat }}</td>
                <td>{{ $product->category_name }}</td>
                <td>{{ $product->barcode }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
