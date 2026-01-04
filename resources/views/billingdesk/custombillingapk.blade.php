
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billing Desk</title>
    {{-- @include('layouts/usersidebar') --}}
<style>
           body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        #content {
            padding: 20px;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Form Container */
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
        }

        /* Customer Tabs */
        .customer-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .customer-tab {
            padding: 12px 16px;
            background-color: #e2e8f0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .customer-tab:hover {
            background-color: #cbd5e1;
        }

        .customer-tab.active {
            background-color: #187f6a;
            color: white;
            font-weight: bold;
        }

        /* Input Rows */
        .inputs-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 0;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 500;
            color: #4a5568;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: #187f6a;
            box-shadow: 0 0 0 2px rgba(32, 99, 155, 0.2);
        }

        /* Main Content Layout */
        .content-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        @media (min-width: 992px) {
            .content-container {
                flex-direction: row;
            }

            .product-grid-container {
                flex: 1;
                order: 1;
            }

            .cart-box {
                width: 350px;
                order: 2;
            }
        }

        /* Product Grid */
     .product-grid-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 2px;
    max-height: 50vh;
    padding: 5px;
}

.product-item {
    background-color: #4EA8DE; /* Changed from white to light blue-gray */
    border: 1px solid #e2e8f0;
    color: white;
    border-radius: 8px;
    padding: 10px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.product-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-color: white;
    border: 2px solid white;
}

.product-item img {
    width: 70px; /* Reduced from 80px */
    height: 70px; /* Reduced from 80px */
    object-fit: contain;
    margin-bottom: 8px;
    border-radius: 4px;
}

.product-name {
    font-size: 16px; /* Reduced from 13px */
    font-weight: bold;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 32px; /* Reduced from 36px */
}

.product-price {
    font-size: 13px; /* Reduced from 14px */
    font-weight: bold;
    color: white;
    margin-top: auto;
}


        /* Cart Section */
       .cart-box {
    background-color: #ffffff;
    border: 1px solid #dbeafe;
    border-radius: 10px;
    padding: 20px;
    height: fit-content;
    position: sticky;
    top: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.cart-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #3b82f6;
    padding-bottom: 12px;
    border-bottom: 2px solid #e0e7ff;
}

/* Enhanced Cart Item Styling */
.cart-item {
    display: flex;
    flex-direction: column;
    padding: 12px 15px;
    border-bottom: 1px solid #e0e7ff;
    background-color: #f8fafc;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}
   .cart-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 15px;
        }
.cart-item:hover {
    background-color: #f0f5ff;
    transform: translateY(-1px);
}

.cart-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.cart-item-name {
    font-size: 15px;
    font-weight: 600;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 12px;
    color: #1e293b;
}

.cart-item-remove {
    color: #ef4444;
    cursor: pointer;
    font-size: 18px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.cart-item-remove:hover {
    transform: scale(1.1);
    color: #dc2626;
}

.cart-item-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price-container {
    display: flex;
    align-items: center;
    min-width: 110px;
}

.price-input {
    width: 80px;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 6px 10px;
    text-align: right;
    font-weight: 600;
    color: #065f46;
    background-color: #f0fdf4;
    transition: border-color 0.2s ease;
}

.price-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.quantity-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.quantity-control {
    display: flex;
    align-items: center;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    overflow: hidden;
    background-color: white;
}

.quantity-btn {
    background-color: #eff6ff;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e40af;
    transition: all 0.2s ease;
}

.quantity-btn:hover {
    background-color: #dbeafe;
    color: #1e3a8a;
}

.quantity-input {
    width: 45px;
    text-align: center;
    border: none;
    border-left: 1px solid #bfdbfe;
    border-right: 1px solid #bfdbfe;
    padding: 5px;
    font-weight: 500;
    color: #1e293b;
}

.unit-label {
    font-size: 13px;
    color: #64748b;
    margin-left: 5px;
    font-weight: 500;
}
        .cart-summary {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .cart-total-amount {
            color: #187f6a;
        }

        .invoice-btn {
            width: 100%;
            padding: 12px;
            background-color: #187f6a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .invoice-btn:hover {
            background-color: #164b7a;
        }

        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-btn {
            background-color: #f0f0f0;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
        }

        .quantity-btn:hover {
            background-color: #e0e0e0;
        }



        /* Remove Button */
        .remove-btn {
            background: none;
            border: none;
            color: #e53e3e;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            margin-left: 8px;
        }

        /* Search Results */
        .search-results {
            position: absolute;
            width: calc(100% - 24px);
            z-index: 1000;
            margin-top: 2px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #4EA8DE; /* Changed from white to light blue-gray */
            border: 1px solid #e2e8f0;
            color: white;
        }

        .search-results div {
            padding: 8px 12px;
            cursor: pointer;
        }

        .search-results div:hover {
            background-color: white;
            color:#4EA8DE;
        }
        /* Payment modal styling */
        .modal-container {
          background-color: white;
          border-radius: 8px;
          max-width: 500px;
          width: 90%;
          max-height: 80vh;
          overflow-y: auto;
          position: relative;
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .modal-content {
          padding: 20px;
        }

        .payment-options {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
          gap: 15px;
          margin-bottom: 20px;
        }

        .payment-btn {
          padding: 15px;
          border: 1px solid #e5e7eb;
          border-radius: 6px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          text-align: center;
          cursor: pointer;
          transition: all 0.2s ease;
          background: none;
          font-weight: 500;
        }

        .payment-btn:hover {
          border-color: #3b82f6;
          box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        .cash-btn {
          color: #10b981;
        }

        .pos-btn {
          color: #3b82f6;
        }

        .credit-btn {
          color: #f59e0b;
        }

        .cheque-btn {
          color: #8b5cf6;
        }

        .cheque-fields {
          margin-top: 20px;
          padding: 15px;
          border: 1px solid #e5e7eb;
          border-radius: 6px;
          display: flex;
          flex-direction: column;
          gap: 10px;
        }

        .cheque-fields input {
          padding: 8px 12px;
          border: 1px solid #d1d5db;
          border-radius: 5px;
        }

        .submit-cheque-btn {
          margin-top: 10px;
          padding: 8px 15px;
          background-color: #8b5cf6;
          color: white;
          border: none;
          border-radius: 5px;
          cursor: pointer;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .inputs-row {
                grid-template-columns: 1fr;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .cart-box {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }

            .product-item img {
                width: 60px;
                height: 60px;
            }

            .product-name {
                font-size: 12px;
            }

            .product-price {
                font-size: 13px;
            }
        }

        /* Additional utility classes */
        .hidden {
          display: none !important;
        }

        .bg-white {
          background-color: white;
        }

        .text-center {
          text-align: center;
        }

        /* Improved Cart Item Styling */
        .cart-item {
          display: flex;
          align-items: center;
          position: relative;
          background-color: #f9fafb;
          border-radius: 6px;
          border: 1px solid #e5e7eb;
        }

        .cart-item .text-sm {
          flex: 1;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        /* Quantity controls styling */
        .quantity-control {
          display: flex;
          align-items: center;
          border: 1px solid #e5e7eb;
          border-radius: 5px;
          overflow: hidden;
        }

        .quantity-control button {
          background-color: #f3f4f6;
          border: none;
          padding: 5px 8px;
          cursor: pointer;
          font-size: 12px;
        }

        .quantity-control button:hover {
          background-color: #e5e7eb;
        }

        /* Improved modal backdrop */
        .fixed {
          position: fixed;
        }

        .inset-0 {
          top: 0;
          right: 0;
          bottom: 0;
          left: 0;
        }

        .flex {
          display: flex;
        }

        .items-center {
          align-items: center;
        }

        .justify-center {
          justify-content: center;
        }

        /* Animation for modal */
        @keyframes modalFadeIn {
          from { opacity: 0; transform: translateY(-20px); }
          to { opacity: 1; transform: translateY(0); }
        }

        .show {
          display: flex !important;
          animation: modalFadeIn 0.3s ease forwards;
        }

        /* Improve focus states for accessibility */
        button:focus,
        input:focus,
        select:focus {
          outline: 2px solid #3b82f6;
          outline-offset: 1px;
        }

        /* Enhanced barcode scanner input */
        .barcode-field {
          background-color: #f9fafb;
          border: 2px solid #d1d5db;
          padding: 8px 12px;
          border-radius: 5px;
          font-size: 16px;
        }

        .barcode-field:focus {
          border-color: #3b82f6;
          background-color: #eff6ff;
        }

        /* Add New Customer button styling */
        .btn-info {
          background-color: #3b82f6;
          color: white;
          padding: 8px 15px;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          text-decoration: none;
          display: inline-block;
          text-align: center;
          transition: background-color 0.2s;
        }

        .btn-info:hover {
          background-color: #2563eb;
        }


    .category-filter {
    padding: 10px;
    background: white;
    border-radius: 8px;
    margin-bottom: 15px;
}

.category-buttons-container {
    display: grid;
    grid-template-columns: repeat(2, minmax(130px, 1fr)); /* Fixed 2 columns with min-width */
    gap: 2px; /* Match product grid gap */
}

.category-btn {
    padding: 8px;
    background-color: #187f6a; /* Match product item background */
    border: 1px solid #e2e8f0; /* Match product border */
    border-radius: 8px; /* Match product border radius */
    cursor: pointer;
    font-size: 16px; /* Match product font size */
    font-weight: bold;
    transition: all 0.2s;
    text-align: center;
    min-height: 80px; /* Match product item height */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    white-space: normal; /* Allow text wrapping */
    overflow: hidden;
    text-overflow: ellipsis;
    -webkit-line-clamp: 2; /* Limit to 2 lines like products */
    -webkit-box-orient: vertical;
    display: -webkit-box;
        word-wrap: break-word; /* allows long words to wrap */
    overflow-wrap: break-word;
    max-width: 140px; /* adjust based on layout */
    color: white;
}

.category-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-color: #187f6a;
}

.category-btn.active {
    background-color: #187f6a;
    color: white;
    font-weight: bold;
    border-color: white;
    border: 5px solid white;

}

/* Keep 2 columns on all screens if that's what you want */
@media (max-width: 768px) {
    .category-buttons-container {
        grid-template-columns: repeat(1, minmax(130px, 1fr)); /* Still 2 columns */
    }

    .category-btn {
        min-height: 90px; /* Slightly smaller on mobile if needed */
        font-size: 12px;
    }
}
    .clear-cart-btn {
  background: none;
  border: none;
  color: #ef4444;
  cursor: pointer;
  font-size: 16px;
  padding: 5px;
}
.takeaway-btn {
    background-color: white;
    color: #f59e0b;
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #f59e0b;
    font-size: 14px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s;
    margin-bottom: 10px;
}

.takeaway-btn:hover {
    background-color: #f59e0b;
    color: white;
}

.takeaway-btn.active {
    background-color: #f59e0b;
    color: white;
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

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
@endphp

<body>
    <!-- Page Content Holder -->
    <div id="content">


    <div class="container">
        <form action="submitdatanew" method="POST">
            @csrf
            <input type="hidden"  name="vat_type_value" value="1">
            <div class="main-container">
                 <!--Product Grid -->
                {{-- <div class="customer-tabs">
                    <button type="button" onclick="switchCustomer('customer_1')" class="customer-tab" data-customer-id="customer_1">C 1</button>
                    <button type="button" onclick="switchCustomer('customer_2')" class="customer-tab" data-customer-id="customer_2">C 2</button>
                    <button type="button" onclick="switchCustomer('customer_3')" class="customer-tab" data-customer-id="customer_3">C 3</button>
                    <button type="button" onclick="switchCustomer('customer_4')" class="customer-tab" data-customer-id="customer_4">C 4</button>
                    <button type="button" onclick="switchCustomer('customer_5')" class="customer-tab" data-customer-id="customer_5">C 5</button>
                    <button type="button" onclick="switchCustomer('customer_6')" class="customer-tab" data-customer-id="customer_6">C 6</button>
                    <button type="button" onclick="switchCustomer('customer_7')" class="customer-tab" data-customer-id="customer_7">C 7</button>
                    <button type="button" onclick="switchCustomer('customer_8')" class="customer-tab" data-customer-id="customer_8">C 8</button>
                    <button type="button" onclick="switchCustomer('customer_9')" class="customer-tab" data-customer-id="customer_9">C 9</button>
                    <button type="button" onclick="switchCustomer('customer_10')" class="customer-tab" data-customer-id="customer_10">C 10</button>
                    <button type="button" onclick="switchCustomer('customer_11')" class="customer-tab" data-customer-id="customer_11">C 11</button>
                    <button type="button" onclick="switchCustomer('customer_12')" class="customer-tab" data-customer-id="customer_12">C 12</button>
                    <button type="button" onclick="switchCustomer('customer_13')" class="customer-tab" data-customer-id="customer_13">C 13</button>
                    <button type="button" onclick="switchCustomer('customer_14')" class="customer-tab" data-customer-id="customer_14">C 14</button>
                    <button type="button" onclick="switchCustomer('customer_15')" class="customer-tab" data-customer-id="customer_15">C 15</button>

                </div> --}}
    <div class="inputs-row">

        {{-- <div class="input-group" style="display: none;">
            <a style="border-radius: 5px;margin-top:27px;width:100%;background-color:#187f6a" class="btn btn-info" data-toggle="modal" data-target="#addCustomerModal">Add New Customer</a>
        </div> --}}
<div>
    <button style="background-color: #187f6a; border: none; padding: 6px 14px;">
        <a href="/userdashboard" style="color: white; text-decoration: none;">HOME</a>
    </button>
    <button style="background-color: #187f6a; border: none; padding: 6px 14px;">
        <a href="/transactions" style="color: white; text-decoration: none;">Transaction History</a>
    </button>
</div>
        <div class="input-group customerselect-id">
            <label for="user_id" class="block text-sm font-medium text-gray-700">Select Customer</label>
            <select id="user_id" name="customer_id" class="border p-2 rounded w-full form-control" onchange="setCustomerDetails()">
                <option value="" selected>Select Customer</option>
                @foreach($creditusers as $user)
                    <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-current_lamount="{{ $user->current_lamount }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" class="form-control" id="customer_hidden_id" name="credit_id" readonly style="height: 20px;">
            <input type="hidden" id="hidden_customer_name" name="customer_name">
            <input type="hidden" id="current_lamount" name="current_lamount">
        </div>
        <div class="input-group customer-id">
            <label for="cust_id" class="block text-sm font-medium text-gray-700">Customer Name</label>
            <input type="text" id="cust_id" name="customer_name" class="border p-2 rounded w-full form-control" >
        </div>
        <div class="input-group barcode-input">
            <label for="barcodeScanner" class="block text-sm font-medium text-gray-700">Scan Barcode</label>
            <input type="text" id="barcodeScanner" class="barcode-field border p-2 rounded w-full form-control" placeholder="Scan Barcode..."   >
        </div>

        <div class="input-group search-product">
            <label for="productSearch" class="block text-sm font-medium text-gray-700">Search Product</label>
            <input type="text" id="productSearch" placeholder="Search Product..." class="form-control border p-2 rounded w-full" >
            <br>
            <div id="searchResults" class="search-results border p-2 rounded hidden bg-white max-h-48 overflow-y-auto"></div>
        </div>

    </div>
    <div class="content-container">
        <div class="category-filter">
            <div class="category-buttons-container">
                <button type="button" class="category-btn active" data-category-id="all">All Products</button>
                @foreach($categories as $category)
                    <button type="button" class="category-btn" data-category-id="{{ $category->id }}" >
                        {{ $category->category_name }}
                    </button>
                @endforeach
            </div>
        </div>
        <!-- Product Grid -->
        <div class="product-grid-container">
             <div class="product-grid">
                    @foreach($items as $product)
                    <div class="product-item"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->product_name }}"
                        data-selling-cost="{{ $product->selling_cost }}"
                        {{-- data-image="{{ asset($product->image) }}" --}}
                        data-unit="{{ $product->unit }}"
                        data-category-id="{{ $product->category_id }}"
                        data-buy-cost="{{ $product->buy_cost }}"
                        data-rate="{{ $product->rate }}"
                        data-barcode="{{ $product->barcode }}"
                        data-purchase-vat="{{ $product->purchase_vat }}"
                        data-inclusive-rate="{{ $product->inclusive_rate }}"
                        data-inclusive-vat-amount="{{ $product->inclusive_vat_amount }}"
                        data-vat="{{ $product->vat }}"
                        @if($branch == 1) data-remaining-stock="{{ $product->remaining_stock }}" @endif>

                        {{-- @if($branch!=63 && $branch!=2)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}">
                        @endif --}}
                        <div class="product-name" style="text-transform: uppercase;">
                            {{ $product->product_name }}
                        </div>

                        <div class="product-price">{{$currency}} <span style="font-size: 18px;">{{ $product->selling_cost }}</span></div>
                        @if($branch == 1)<div class="stock">Stock ({{ $product->remaining_stock }})</div>@endif
                    </div>
                    @endforeach

                </div>
            </div>

                <!-- Cart Section -->
                <div class="cart-box" >
                    <div class="cart-header flex justify-between items-center" >
                        <h3 class="cart-title">Cart</h3>
                        <button type="button" onclick="clearCart()" class="clear-cart-btn" title="Clear Cart" style="margin-left: 5px;">
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                    </div>

                    <div id="cart-items" class="cart-items"></div>

                    <div id="cart-summary" class="cart-summary hidden">
                        <div class="cart-total">
                            <span>Total:</span>
                            <span id="cart-total" class="cart-total-amount">{{$currency}} 0</span>
                        </div>
                        <div class="discount-section" style="margin-top: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="number" id="discountAmount" placeholder="Total Discount Amount"
                                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;"
                                    oninput="applyDiscount()">
                                <span>{{$currency}}</span>
                            </div>
                            <div id="discountDisplay" style="margin-top: 5px; color: #e53e3e; display: none;">
                                Discount Applied: <span id="discountValue">{{$currency}} 0.00</span>
                            </div>
                            <input type="hidden" id="totalDiscount" name="discount_amount" value="0">
                        </div>
                        <br>
                        <div id="payment-method" class="hidden">
                            <input type="hidden" id="payment_type" name="payment_type">
                            <input type="hidden" id="bill_grand_total" name="bill_grand_total" class="payment-input form-control">
                        </div>

                        <button type="button" class="invoice-btn" onclick="openPaymentModal()">Checkout</button>


                    <div id="paymentModal"
                             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden transition-all duration-300">
                            <div class="bg-white rounded-lg shadow-lg p-6 w-96 scale-0 opacity-0 transition-all duration-300 transform"
                                 style="position: fixed; z-index: 9999;">
                                 <div class="modal-content p-6" style="width: 300px; margin-top: 10px; text-align: center;">
                                    <h2 class="font-bold mb-4" style="font-size: 14px; margin-bottom: 20px;">Select Payment Method</h2>

                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        <button type="button"
                                            style="background-color: white; color: #22c55e; padding: 15px 20px; border-radius: 6px; border: 1px solid #22c55e; font-size: 16px; cursor: pointer; width: 100%; transition: all 0.3s;"
                                            onmouseover="this.style.backgroundColor='#22c55e'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='white'; this.style.color='#22c55e';"
                                            onclick="submitInvoice(1)">Cash</button>

                                        <button type="button"
                                            style="background-color: white; color: #3b82f6; padding: 15px 20px; border-radius: 6px; border: 1px solid #3b82f6; font-size: 16px; cursor: pointer; width: 100%; transition: all 0.3s;"
                                            onmouseover="this.style.backgroundColor='#3b82f6'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='white'; this.style.color='#3b82f6';"
                                            onclick="submitInvoice(4)">POS</button>

                                            <button id="creditButton" type="button"
                                            onclick="submitInvoice(3)">
                                            Credit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add this inside your form, maybe near the payment method section -->
                        {{-- @if($branch==63 || $branch==2)

                    <div class="takeaway-option" style="margin-top: 10px;">
                        <button type="button" id="takeawayBtn" class="takeaway-btn" onclick="toggleTakeaway()">
                            Take Away
                        </button>
                        <input type="hidden" id="mode" name="mode" value="0">
                    </div>
                    @endif --}}
                </div>
            </div>
                    </div>
                </form>
                {{-- @include('modal.add_customer_modal') --}}

            </div>
    </div>

    <script src="{{ asset('javascript/billing.js') }}"></script>

</body>

</html>
<script>
    const allProducts = @json($allProducts);
</script>
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const productItems = document.querySelectorAll('.product-item');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartSummary = document.getElementById('cart-summary');
    const cartTotal = document.getElementById('cart-total');
    const paymentMethodContainer = document.getElementById('payment-method');
    const barcodeScanner = document.getElementById('barcodeScanner');
    const productSearch = document.getElementById('productSearch');
    const searchResults = document.getElementById('searchResults');

    // barcodeScanner.focus();
    productItems.forEach(item => {
        item.addEventListener('click', function () {
            addToCart(this);
        });
    });

    barcodeScanner.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanBarcode();
        }
    });

    productSearch.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent Enter key from focusing barcode while searching
        }
    });

    productSearch.addEventListener('focus', function () {
        barcodeScanner.blur(); // Blur barcode when searching
    });

productSearch.addEventListener('input', function () {
    searchResults.innerHTML = '';
    const query = productSearch.value.trim().toLowerCase();
    const activeCategory = document.querySelector('.category-btn.active').dataset.categoryId;
    const branch = @json($branch);

    if (query.length > 0) {
        const filteredProducts = allProducts.filter(product => {
            const matchesSearch = product.product_name.toLowerCase().includes(query);
            const matchesCategory = activeCategory === 'all' || product.category_id == activeCategory;
            return matchesSearch && matchesCategory;
        });

        filteredProducts.forEach(product => {
            const resultItem = document.createElement('div');
            resultItem.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200', 'search-results');
            resultItem.textContent = product.product_name;
            resultItem.dataset.productId = product.id;
            resultItem.dataset.productName = product.product_name;
            resultItem.dataset.sellingCost = product.selling_cost;
            resultItem.dataset.unit = product.unit;
            resultItem.dataset.buyCost = product.buy_cost;
            resultItem.dataset.rate = product.rate;
            resultItem.dataset.purchaseVat = product.purchase_vat;
            resultItem.dataset.inclusiveRate = product.inclusive_rate;
            resultItem.dataset.vat = product.vat;
            resultItem.dataset.inclusiveVatAmount = product.inclusive_vat_amount;
            resultItem.dataset.remainingStock = product.remaining_stock; // Add stock info

            resultItem.addEventListener('click', function () {
                // Check stock for branch 1
                if (branch == 1 && product.remaining_stock !== undefined) {
                    if (parseInt(product.remaining_stock) <= 0) {
                        alert('This product is out of stock!');
                        return;
                    }
                }
                addToCart(this);
                searchResults.classList.add('hidden');
                productSearch.value = '';
            });

            searchResults.appendChild(resultItem);
        });

        searchResults.classList.remove('hidden');
    } else {
        searchResults.classList.add('hidden');
    }
});

    document.addEventListener('click', function (e) {
        if (e.target !== productSearch && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
            // barcodeScanner.focus();
        }
    });

window.scanBarcode = function () {
    let barcode = barcodeScanner.value.trim();
    if (barcode.length > 0) {
        let found = false;
        const branch = @json($branch);

        allProducts.forEach(function (product) {
            if (product.barcode.trim() === barcode) {
                // Check stock for branch 1
                if (branch == 1 && product.remaining_stock !== undefined) {
                    if (parseInt(product.remaining_stock) <= 0) {
                        alert('This product is out of stock!');
                        barcodeScanner.value = "";
                        found = true;
                        return;
                    }
                }

                let productElement = {
                    dataset: {
                        productId: product.id,
                        productName: product.product_name,
                        sellingCost: product.selling_cost,
                        unit: product.unit,
                        buyCost: product.buy_cost,
                        rate: product.rate,
                        purchaseVat: product.purchase_vat,
                        inclusiveRate: product.inclusive_rate,
                        inclusiveVatAmount: product.inclusive_vat_amount,
                        vat: product.vat,
                        remainingStock: product.remaining_stock // Add stock info
                    }
                };
                addToCart(productElement);
                barcodeScanner.value = "";
                found = true;
            }
        });

        if (!found) {
            alert("Product not found!");
            barcodeScanner.value = "";
        }
    }
};




window.updateProductPrice = function(input, vatRate, originalInclusiveRate, originalSellingCost) {
    const cartItem = input.closest('div[id^="cart-item-"]');
    if (!cartItem) return;

    const newPrice = parseFloat(input.value) || 0;
    if (newPrice <= 0) {
        input.value = originalSellingCost.toFixed(2);
        return;
    }

    // Get quantity
    const quantityInput = cartItem.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value) || 1;

    // Calculate total amount
    const totalAmount = newPrice * quantity;
    cartItem.querySelector('input[name="total_amount[]"]').value = totalAmount.toFixed(2);

    // Original values for reference
    const originalInclusive = parseFloat(cartItem.querySelector('input[name="original_inclusive_rate[]"]').value) || originalInclusiveRate;
    const originalSelling = parseFloat(cartItem.querySelector('input[name="original_selling_cost[]"]').value) || originalSellingCost;
    const fixedvat = parseFloat(cartItem.querySelector('input[name="fixed_vat[]"]').value);

    // Calculate the ratio of change
    const priceRatio = newPrice / originalSelling;

    // Calculate new inclusive rate proportionally
    const newInclusiveRate = (originalInclusive * priceRatio).toFixed(3);
    cartItem.querySelector('input[name="inclusive_rate[]"]').value = newInclusiveRate;

    // Calculate new VAT amount
    const newVatAmount = (newPrice - (newPrice / (1 + fixedvat / 100))).toFixed(3);
    cartItem.querySelector('input[name="vat_amount[]"]').value = newVatAmount;

    // Update MRP and net rate
    cartItem.querySelector('input[name="mrp[]"]').value = newPrice.toFixed(2);
    cartItem.querySelector('input[name="net_rate[]"]').value = newPrice.toFixed(2);

    // Fixed VAT percentage stays the same

    // Update the cart total
    updateCartTotal();

};
window.increaseQuantity = function(btn) {
    const quantityInput = btn.closest('.quantity-control').querySelector('.quantity-input');
    const cartItem = btn.closest('.cart-item');
    const branch = @json($branch);

    if (branch == 1) {
        // Get the remaining stock from the original product data attribute
        const productId = cartItem.querySelector('input[name="product_id[]"]').value;
        const product = getProductById(productId);
        if (product && product.dataset.remainingStock !== undefined) {
            const remainingStock = parseInt(product.dataset.remainingStock);
            const currentQuantity = parseInt(quantityInput.value) || 0;
            if (currentQuantity >= remainingStock) {
                alert('Cannot add more than available stock!');
                return;
            }
        }
    }

    // Rest of the increaseQuantity function
    const hiddenQuantityInput = cartItem.querySelector('input[name="quantity[]"]');
    const totalAmountInput = cartItem.querySelector('input[name="total_amount[]"]');
    const priceInput = cartItem.querySelector('.price-input');

    let currentQuantity = parseInt(quantityInput.value) || 1;
    const currentPrice = parseFloat(priceInput.value) || 0;

    quantityInput.value = currentQuantity + 1;
    hiddenQuantityInput.value = quantityInput.value;
    totalAmountInput.value = (parseInt(quantityInput.value) * currentPrice).toFixed(2);

    updateCartTotal();
    showCartSummary();
};

window.decreaseQuantity = function(btn) {
    // Find the quantity input relative to the clicked button
    const quantityInput = btn.closest('.quantity-control').querySelector('.quantity-input');
    const hiddenQuantityInput = btn.closest('.cart-item').querySelector('input[name="quantity[]"]');
    const totalAmountInput = btn.closest('.cart-item').querySelector('input[name="total_amount[]"]');
    const priceInput = btn.closest('.cart-item').querySelector('.price-input');

    // Get current values
    let currentQuantity = parseInt(quantityInput.value) || 1;
    const currentPrice = parseFloat(priceInput.value) || 0;

    // Only decrease if quantity is greater than 1
    if (currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
        hiddenQuantityInput.value = quantityInput.value;

        // Update total amount
        totalAmountInput.value = (parseInt(quantityInput.value) * currentPrice).toFixed(2);

        // Update cart and save
        updateCartTotal();

    }
};
window.applyDiscount = function() {
    const discountInput = document.getElementById('discountAmount');
    const discountDisplay = document.getElementById('discountDisplay');
    const discountValue = document.getElementById('discountValue');
    const totalDiscountInput = document.getElementById('totalDiscount');
    const cartTotalElement = document.getElementById('cart-total');
    const billGrandTotalInput = document.getElementById('bill_grand_total');

    // Get the original total before discount
    const originalTotal = parseFloat(billGrandTotalInput.value) || 0;

    // Get discount amount (ensure it's a positive number)
    let discountAmount = Math.abs(parseFloat(discountInput.value) || 0);

    // Validate discount doesn't exceed total
    if (discountAmount > originalTotal) {
        discountAmount = originalTotal;
        discountInput.value = discountAmount.toFixed(2);
    }

    // Calculate new total
    const newTotal = originalTotal - discountAmount;

    // Update display
    if (discountAmount > 0) {
        discountDisplay.style.display = 'block';
        discountValue.textContent = `{{$currency}} ${discountAmount.toFixed(2)}`;
    } else {
        discountDisplay.style.display = 'none';
    }

    // Update hidden fields
    totalDiscountInput.value = discountAmount.toFixed(2);
    billGrandTotalInput.value = newTotal.toFixed(2);

    // Update displayed total (with currency symbol)
    const currencySymbol = cartTotalElement.textContent.split(' ')[0];
    cartTotalElement.textContent = `${currencySymbol} ${newTotal.toFixed(2)}`;
};

window.updateCartTotal = function() {
    let subtotal = 0;
    const cartItems = document.querySelectorAll('#cart-items .cart-item');

    cartItems.forEach(item => {
        const totalAmount = parseFloat(item.querySelector('input[name="total_amount[]"]').value) || 0;
        subtotal += totalAmount;
    });

    // Get current discount
    const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
    const total = subtotal - discountAmount;

    // Update display
    const cartTotalElement = document.getElementById('cart-total');
    if (cartTotalElement) {
        const currencySymbol = cartTotalElement.textContent.split(' ')[0];
        cartTotalElement.textContent = `${currencySymbol} ${total.toFixed(2)}`;
    }

    // Update hidden fields
    document.getElementById('bill_grand_total').value = total.toFixed(2);

    // Show discount if applied
    const discountDisplay = document.getElementById('discountDisplay');
    const discountValue = document.getElementById('discountValue');
    if (discountAmount > 0) {
        discountDisplay.style.display = 'block';
        discountValue.textContent = `{{$currency}} ${discountAmount.toFixed(2)}`;
    } else {
        discountDisplay.style.display = 'none';
    }

    document.getElementById('totalDiscount').value = discountAmount.toFixed(2);
};


    window.removeCartItem = function(button) {
        const cartItem = button.closest('.border');
        cartItem.remove();
        updateCartTotal();

        const cartItems = document.querySelectorAll('#cart-items > div');
        if (cartItems.length === 0) {
            cartSummary.classList.add('hidden');
            paymentMethodContainer.classList.add('hidden');
        }
        // barcodeScanner.focus();
    };


});

</script>
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    generateCustomerID(); // Automatically generate ID on page load

    function generateCustomerID() {
        var daterandom = Date.now() % 1000000; // Generate random number based on timestamp
        document.getElementById("cust_id").value = daterandom; // Set the value
    }
});


</script> --}}
<script>
document.querySelector('.invoice-btn').addEventListener('click', function (e) {
    e.stopPropagation();
    document.getElementById('barcodeScanner').blur();

    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
});

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('show');
    modal.classList.add('hidden');
}


</script>
<script>
    function increaseQuantity(btn) {
    let input = btn.previousElementSibling;
    let hiddenInput = input.parentElement.nextElementSibling;
    let totalAmountInput = hiddenInput.nextElementSibling;

    // Increase the quantity
    input.value = parseInt(input.value) + 1;
    hiddenInput.value = input.value;
    totalAmountInput.value = (parseFloat(input.value) * parseFloat(totalAmountInput.value / (parseInt(input.value) - 1))).toFixed(2);

    updateCartTotal();
}

function decreaseQuantity(btn) {
    let input = btn.nextElementSibling;
    let hiddenInput = input.parentElement.nextElementSibling;
    let totalAmountInput = hiddenInput.nextElementSibling;

    // Decrease the quantity but not less than 1
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
        hiddenInput.value = input.value;
        totalAmountInput.value = (parseFloat(input.value) * parseFloat(totalAmountInput.value / (parseInt(input.value) + 1))).toFixed(2);

        updateCartTotal();
    }
}

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const customerIdInput = document.getElementById('cust_id');
  customerIdInput.addEventListener('focus', function(e) {
    e.stopPropagation();
  });
  customerIdInput.addEventListener('click', function(e) {
    e.stopPropagation();
    this.focus();
  });
});
</script>
<script>
    let activeCustomer = localStorage.getItem('activeCustomer') || 'customer_1';
    function showCartSummary() {
    const cartSummary = document.getElementById('cart-summary');
    if (cartSummary && cartSummary.classList.contains('hidden')) {
        cartSummary.classList.remove('hidden');
    }
}
    function updateCartTotal() {
        try {
            const cartItems = document.querySelectorAll('#cart-items > div');
            let total = 0;

            cartItems.forEach(item => {
                const totalAmountInput = item.querySelector('input[name="total_amount[]"]');
                if (totalAmountInput && totalAmountInput.value) {
                    total += parseFloat(totalAmountInput.value);
                }
            });

            // Update the cart total display
            const cartTotalElement = document.getElementById('cart-total');
            if (cartTotalElement) {
                // Extract the currency symbol from the current format
                const currencySymbol = cartTotalElement.textContent.split(' ')[0];
                cartTotalElement.textContent = `${currencySymbol} ${total.toFixed(2)}`;
            }

            // Store the total for this customer
            localStorage.setItem(activeCustomer + '_total', total.toFixed(2));

        // Show cart summary regardless of total
        showCartSummary();

        // Update hidden total input for submission
        const billGrandTotal = document.getElementById('bill_grand_total');
        if (billGrandTotal) {
            billGrandTotal.value = total.toFixed(2);
        }
            return total;
        } catch (error) {
            console.error('Error updating cart total:', error);
            return 0;
        }
    }
    function switchCustomer(customerId) {
    // Save cart data before switching

    resetTakeawayMode();

    // Set active customer
    localStorage.setItem('activeCustomer', customerId);
    activeCustomer = customerId;

    // Highlight the active button
    document.querySelectorAll('.customer-tab').forEach(btn => {
        if (btn.getAttribute('data-customer-id') === customerId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Clear the cart area
    document.getElementById('cart-items').innerHTML = '';

    // Load cart data if exists
    const cartDataString = localStorage.getItem(customerId);
    if (cartDataString) {
        try {
            const cartData = JSON.parse(cartDataString);
            // Only load if there are actually items in the cart
            if (cartData && cartData.length > 0) {
                loadCartItems(cartData);
            } else {
                // If empty array, remove it from localStorage
                localStorage.removeItem(customerId);
            }
        } catch (error) {
            console.error('Error parsing cart data:', error);
            // If data is invalid, remove it
            localStorage.removeItem(customerId);
        }
    }

    // Handle Customer ID / Name per tab
    let customerNameInput = document.getElementById('cust_id');
    let customerSelect = document.getElementById('user_id');
    let hiddenCustomerId = document.getElementById('customer_hidden_id');
    let currentLamountField = document.getElementById('current_lamount');
    let creditButton = document.getElementById('creditButton');
    let barcodeScanner = document.getElementById('barcodeScanner');

    // Check if this tab already has saved customer data
    let savedCustomerData = localStorage.getItem(customerId + '_customerData');

    if (savedCustomerData) {
        try {
            const customerData = JSON.parse(savedCustomerData);

            // If we have a selected customer from dropdown
            if (customerData.selectedId) {
                customerNameInput.value = customerData.selectedName;
                customerSelect.value = customerData.selectedId;
                hiddenCustomerId.value = customerData.selectedId;
                currentLamountField.value = customerData.currentLamount;
                creditButton.style.display = 'block';

                // Also save to the general fields (for backward compatibility)
                localStorage.setItem(customerId + '_name', customerData.selectedName);
                localStorage.setItem(customerId + '_select_id', customerData.selectedId);
                localStorage.setItem(customerId + '_current_lamount', customerData.currentLamount);
            }
            // If we have a generated ID
            else if (customerData.generatedId) {
                customerNameInput.value = customerData.generatedId;
                customerSelect.value = '';
                hiddenCustomerId.value = '';
                currentLamountField.value = '';
                creditButton.style.display = 'none';
            }
        } catch (e) {
            console.error('Error parsing customer data:', e);
            // Fallback to generating new ID if parsing fails
            generateNewCustomerId();
        }
    } else {
        // No saved data, generate new ID
        generateNewCustomerId();
    }
    // Update Cart
    updateCartTotal();
    showCartSummary();

    // Prevent auto-focus on barcode after switching customer
    // setTimeout(() => {
    //     barcodeScanner.focus();
    // }, 300);

    function generateNewCustomerId() {
            let randomId = 'CUST-' + Math.floor(10000 + Math.random() * 90000);
            customerNameInput.value = randomId;
            customerSelect.value = '';
            hiddenCustomerId.value = '';
            currentLamountField.value = '';
            creditButton.style.display = 'none';

            // Save the generated ID for this tab
            const customerData = {
                generatedId: randomId
            };
            localStorage.setItem(customerId + '_customerData', JSON.stringify(customerData));
        }
}


// ✅ Handle dropdown selection
function setCustomerDetails() {
    let customerNameInput = document.getElementById('cust_id');
    let customerSelect = document.getElementById('user_id');
    let hiddenCustomerId = document.getElementById('customer_hidden_id');
    let currentLamountField = document.getElementById('current_lamount');
    let creditButton = document.getElementById('creditButton');
    let barcodeScanner = document.getElementById('barcodeScanner');

    // ✅ Get the selected option
    let selectedOption = customerSelect.options[customerSelect.selectedIndex];
    let selectedName = selectedOption.getAttribute('data-name');
    let selectedId = selectedOption.value;
    let currentLamount = selectedOption.getAttribute('data-current_lamount');

    if (selectedId !== '') {
        // If dropdown is selected, use its value
        customerNameInput.value = selectedName;
        hiddenCustomerId.value = selectedId;
        currentLamountField.value = currentLamount;

        // Save all customer data together in a single object
        const customerData = {
            selectedId: selectedId,
            selectedName: selectedName,
            currentLamount: currentLamount
        };
        // localStorage.setItem(activeCustomer + '_customerData', JSON.stringify(customerData));

        // For backward compatibility, also save the individual fields
        // localStorage.setItem(activeCustomer + '_name', selectedName);
        // localStorage.setItem(activeCustomer + '_select_id', selectedId);
        // localStorage.setItem(activeCustomer + '_current_lamount', currentLamount);

        // Show Credit Button
        creditButton.style.display = 'block';
    } else {
        // If no customer selected, generate new ID
        let randomId = 'CUST-' + Math.floor(10000 + Math.random() * 90000);
        customerNameInput.value = randomId;
        hiddenCustomerId.value = '';
        currentLamountField.value = '';
        creditButton.style.display = 'none';

        // Save the generated ID
        const customerData = {
            generatedId: randomId
        };
        // localStorage.setItem(activeCustomer + '_customerData', JSON.stringify(customerData));

        // // Clear old individual fields
        // localStorage.removeItem(activeCustomer + '_name');
        // localStorage.removeItem(activeCustomer + '_select_id');
        // localStorage.removeItem(activeCustomer + '_current_lamount');
    }

    // ✅ Save cart data


    // ✅ Focus barcode after dropdown
    // setTimeout(() => {
    //     barcodeScanner.focus();
    // }, 200);
}

// ✅ Prevent barcode focus when clicking dropdown
document.getElementById('user_id').addEventListener('click', function (e) {
    e.stopPropagation();
    document.getElementById('barcodeScanner').blur();
});

// ✅ Prevent barcode focus when typing customer name
document.getElementById('cust_id').addEventListener('input', function () {
    document.getElementById('barcodeScanner').blur();
});

// ✅ Auto-focus and select barcode input
// document.getElementById('barcodeScanner').addEventListener('focus', function () {
//     this.select();
// });

// ✅ Handle credit button style
let button = document.getElementById('creditButton');
button.style = `
    background-color: white; color: #f59e0b;
    padding: 15px 20px; border-radius: 6px;
    border: 1px solid #f59e0b; font-size: 16px;
    cursor: pointer; width: 100%; transition: all 0.3s;
    display: none;
`;
button.onmouseover = function() {
    button.style.backgroundColor = '#f59e0b';
    button.style.color = 'white';
};
button.onmouseout = function() {
    button.style.backgroundColor = 'white';
    button.style.color = '#f59e0b';
};



    // Save cart data whenever quantity changes
    function saveCartAfterQuantityChange() {
        updateCartTotal();
        showCartSummary();

    }

    // Enhanced saveCartToLocalStorage function with error handling


    function submitInvoice(paymentType) {
    // Set the payment type
       if (paymentType === 3) {
        const billGrandTotal = parseFloat(document.getElementById('bill_grand_total').value) || 0;
        const currentLamount = parseFloat(document.getElementById('current_lamount').value);

        // Check if current_lamount exists and bill exceeds it
        if (currentLamount !== null && billGrandTotal > currentLamount) {
            alert("Warning: Bill amount exceeds customer's available credit limit!");
            return false; // Stop further execution and prevent submission
        }
    }
        document.getElementById('payment_type').value = paymentType;

    // Mark this form as submitted and store current state before submission
    localStorage.setItem('formSubmitted', 'true');
    localStorage.setItem('lastActiveCustomer', activeCustomer);

    // Save that we need to clear this customer's data after reload
    localStorage.setItem('clearAfterSubmit', activeCustomer);

    // Submit the form
    document.querySelector('form').submit();
    document.getElementById('user_id').selectedIndex = 0;
    document.getElementById('customer_hidden_id').value = '';
    let randomId = 'CUST-' + Math.floor(10000 + Math.random() * 90000);
    document.getElementById('cust_id').value = randomId;

    // Close the payment modal
}

// Function to open payment modal






 // Helper function to load customer data
function loadCustomerData(customerId) {
    // Load cart data if exists
    if (localStorage.getItem(customerId)) {
        try {
            const cartData = JSON.parse(localStorage.getItem(customerId));
            loadCartItems(cartData);
        } catch (error) {
            console.error('Error parsing cart data:', error);
        }
    }

    // Load saved customer name and ID if available
    const savedName = localStorage.getItem(customerId + '_name');
    const savedId = localStorage.getItem(customerId + '_select_id');
    const savedLamount = localStorage.getItem(customerId + '_current_lamount');

    const customerNameField = document.getElementById('cust_id');
    const customerSelect = document.getElementById('user_id');
    const hiddenCustomerId = document.getElementById('customer_hidden_id');
    const currentLamountField = document.getElementById('current_lamount');

    if (savedName && savedId) {
        if (customerNameField) customerNameField.value = savedName;
        if (customerSelect) customerSelect.value = savedId;
        if (hiddenCustomerId) hiddenCustomerId.value = savedId;
        if (currentLamountField) currentLamountField.value = savedLamount;
    } else {
        // Generate a new random ID if no saved data
        const randomId = 'CUST-' + Math.floor(10000 + Math.random() * 90000);
        if (customerNameField) customerNameField.value = randomId;
        if (hiddenCustomerId) hiddenCustomerId.value = '';
        if (currentLamountField) currentLamountField.value = '';
    }
}

// Main initialization
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're loading after a form submission
    if (localStorage.getItem('formSubmitted') === 'true') {
        console.log('Page loaded after form submission, clearing fields');

        // Get the customer that needs to be cleared
        const customerToClear = localStorage.getItem('clearAfterSubmit');

        // Clear the specific customer's data
        if (customerToClear) {
            localStorage.removeItem(customerToClear);
            localStorage.removeItem(customerToClear + '_name');
            localStorage.removeItem(customerToClear + '_select_id');
            localStorage.removeItem(customerToClear + '_total');
            localStorage.removeItem(customerToClear + '_current_lamount');

        }
        document.getElementById('current_lamount').value = '';

        // Reset form fields
        const customerSelect = document.getElementById('user_id');
        if (customerSelect) customerSelect.selectedIndex = 0;

        const customerHiddenId = document.getElementById('customer_hidden_id');
        if (customerHiddenId) customerHiddenId.value = '';

        // Generate new random ID
        const randomId = 'CUST-' + Math.floor(10000 + Math.random() * 90000);
        const customerNameField = document.getElementById('cust_id');
        if (customerNameField) customerNameField.value = randomId;

        // Clear the cart area
        const cartItems = document.getElementById('cart-items');
        if (cartItems) cartItems.innerHTML = '';

        // Reset the flags
        localStorage.removeItem('formSubmitted');
        localStorage.removeItem('clearAfterSubmit');

        console.log('Customer fields and cart cleared after submission');
    } else {
        // Get active customer
        activeCustomer = localStorage.getItem('activeCustomer') || 'customer_1';
        console.log('Document loaded, active customer: ' + activeCustomer);

        // Load customer data and cart
        loadCustomerData(activeCustomer);
    }
    updateCartTotal();
    showCartSummary();

    // Auto-focus barcode scanner after 300ms
    setTimeout(() => {
        const barcodeScanner = document.getElementById('barcodeScanner');
        if (barcodeScanner) barcodeScanner.focus();
    }, 300);
        // Setup autosave functionality
        setupRobustAutoSave();

        patchAddToCartFunction();

        // Highlight active customer tab
        document.querySelectorAll('.customer-tab').forEach(btn => {
            if (btn.getAttribute('data-customer-id') === activeCustomer) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
    }
    });

    document.querySelectorAll('.customer-tab').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const customerId = this.getAttribute('data-customer-id');
            if (customerId) {
                switchCustomer(customerId);
            }
            setTimeout(() => {
                document.activeElement.blur();
            }, 100);
        });
    });

    function setupRobustAutoSave() {
        const autoSaveInterval = setInterval(function() {

            updateCartTotal();
            showCartSummary();

        }, 2000);

        const cartItemsContainer = document.getElementById('cart-items');
        if (cartItemsContainer) {
            const observer = new MutationObserver(function() {

                updateCartTotal();

                showCartSummary();
            });

            observer.observe(cartItemsContainer, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['value']
            });
        }

        document.addEventListener('visibilitychange', function() {

            updateCartTotal();
            showCartSummary();

        });

        window.addEventListener('beforeunload', function() {

            updateCartTotal();
            showCartSummary();

        });

        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('quantity-input') ||
                event.target.name === 'quantity[]' ||
                event.target.name === 'total_amount[]') {

                updateCartTotal();
                showCartSummary();

            }
        });
        document.addEventListener('change', function() {

            updateCartTotal();
            showCartSummary();

        });
    }
    function onItemAddedToCart() {

        updateCartTotal();
        showCartSummary();
}

// Support functions for quantity changes if not defined elsewhere
if (typeof window.increaseQuantity !== 'function') {
    window.increaseQuantity = function(button) {
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        if (quantityInput) {
            let currentValue = parseFloat(quantityInput.value) || 0;
            quantityInput.value = (currentValue + 1).toString();

            // Trigger the oninput event to update related fields
            const event = new Event('input', { bubbles: true });
            quantityInput.dispatchEvent(event);
        }
    };
}

if (typeof window.decreaseQuantity !== 'function') {
    window.decreaseQuantity = function(button) {
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        if (quantityInput) {
            let currentValue = parseFloat(quantityInput.value) || 0;
            if (currentValue > 1) {
                quantityInput.value = (currentValue - 1).toString();

                // Trigger the oninput event to update related fields
                const event = new Event('input', { bubbles: true });
                quantityInput.dispatchEvent(event);
            }
        }
    };
}

function removeCartItem(button) {
    const cartItem = button.closest('div[id^="cart-item-"]');
    if (cartItem) {
        cartItem.remove();

        // Check if cart is now empty
        const remainingItems = document.querySelectorAll('#cart-items > div');
        if (remainingItems.length === 0) {
            // If cart is empty, remove the customer's cart data from localStorage
            localStorage.removeItem(activeCustomer);
            console.log('Cart emptied, removed data for ' + activeCustomer);

            // Reset the cart total
            const cartTotalElement = document.getElementById('cart-total');
            if (cartTotalElement) {
                const currencySymbol = cartTotalElement.textContent.split(' ')[0];
                cartTotalElement.textContent = `${currencySymbol} 0.00`;
            }

            // Update hidden input
            const billGrandTotal = document.getElementById('bill_grand_total');
            if (billGrandTotal) {
                billGrandTotal.value = '0.00';
            }
        } else {
            // Otherwise just save the remaining cart items

        }
        updateCartTotal();
        showCartSummary();
    }
}
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filter functionality
    const categoryButtons = document.querySelectorAll('.category-btn');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.dataset.categoryId;
            filterProductsByCategory(categoryId);
        });
    });

    function filterProductsByCategory(categoryId) {
        const allProducts = document.querySelectorAll('.product-item');

        allProducts.forEach(product => {
            if (categoryId === 'all') {
                product.style.display = 'flex'; // or 'block' depending on your layout
            } else {
                if (product.dataset.categoryId === categoryId) {
                    product.style.display = 'flex';
                } else {
                    product.style.display = 'none';
                }
            }
        });
    }

    // Make sure all products are visible initially
    filterProductsByCategory('all');
});
</script>
<script>

        window.clearCart = function() {
    if (confirm("Are you sure you want to clear the entire cart?")) {
        // Clear the cart items container
        document.getElementById('cart-items').innerHTML = '';

        // Reset discount
        document.getElementById('discountAmount').value = '';
        document.getElementById('totalDiscount').value = '0';
        document.getElementById('discountDisplay').style.display = 'none';

        // Update the cart total display
        updateCartTotal();

            // Focus back on barcode scanner
            focusBarcodeScannerIfAllowed();
        }
    };
</script>
<script>
      document.addEventListener('DOMContentLoaded', function() {
    // Get all category buttons
    const categoryButtons = document.querySelectorAll('.category-btn');

    // If there are categories available
    if (categoryButtons.length > 0) {
        // Remove active class from all buttons first
        categoryButtons.forEach(btn => btn.classList.remove('active'));

        // Find the first category button that's not "All Products"
        const firstCategoryBtn = Array.from(categoryButtons).find(btn => {
            return btn.dataset.categoryId !== 'all' && btn.dataset.categoryId !== undefined;
        });

        // If found, set it as active
        if (firstCategoryBtn) {
            firstCategoryBtn.classList.add('active');

            // Trigger a click event to load products for this category
            setTimeout(() => {
                firstCategoryBtn.click();
            }, 100);
        } else {
            // Fallback to "All Products" if no other categories exist
            const allProductsBtn = document.querySelector('.category-btn[data-category-id="all"]');
            if (allProductsBtn) {
                allProductsBtn.classList.add('active');
            }
        }
    }


});
</script>
<script>
    function toggleTakeaway() {
    const takeawayBtn = document.getElementById('takeawayBtn');
    const modeInput = document.getElementById('mode');

    if (modeInput.value === '0') {
        modeInput.value = '1';
        takeawayBtn.classList.add('active');
        takeawayBtn.textContent = 'Take Away (Selected)';
    } else {
        modeInput.value = '0';
        takeawayBtn.classList.remove('active');
        takeawayBtn.textContent = 'Take Away';
    }
}
function resetTakeawayMode() {
    const takeawayBtn = document.getElementById('takeawayBtn');
    const modeInput = document.getElementById('mode');

    modeInput.value = '0';
    takeawayBtn.classList.remove('active');
    takeawayBtn.textContent = 'Take Away';
}
</script>
<script>
 window.handleQuantityChange = function(input) {
    const cartItem = input.closest('.cart-item');
    const branch = @json($branch);

    // Validate input
    let quantity = parseInt(input.value);
    if (isNaN(quantity)) {
        input.value = 1;
        quantity = 1;
    } else if (quantity < 1) {
        input.value = 1;
        quantity = 1;
    }

    // Check stock for branch 1
    if (branch == 1) {
        const productId = cartItem.querySelector('input[name="product_id[]"]').value;
        const product = getProductById(productId);
        if (product && product.dataset.remainingStock !== undefined) {
            const remainingStock = parseInt(product.dataset.remainingStock);
            if (quantity > remainingStock) {
                alert(`Only ${remainingStock} items available in stock!`);
                input.value = remainingStock;
                quantity = remainingStock;
            }
        }
    }

    // Update all related fields
    const priceInput = cartItem.querySelector('.price-input');
    const hiddenQuantity = cartItem.querySelector('input[name="quantity[]"]');
    const totalAmountInput = cartItem.querySelector('input[name="total_amount[]"]');

    const price = parseFloat(priceInput.value) || 0;
    const totalAmount = (quantity * price).toFixed(2);

    hiddenQuantity.value = quantity;
    totalAmountInput.value = totalAmount;

    updateCartTotal();
    showCartSummary();
};

// Update the validateQuantityInput function
window.validateQuantityInput = function(input) {
    handleQuantityChange(input); // This will validate both the number and stock
    setTimeout(() => {
        if (document.activeElement !== input) {
            document.getElementById('barcodeScanner').focus();
        }
    }, 200);
};

// Update all related fields for a cart item
function updateCartItem(cartItem, newQuantity) {
    const priceInput = cartItem.querySelector('.price-input');
    const hiddenQuantity = cartItem.querySelector('input[name="quantity[]"]');
    const totalAmountInput = cartItem.querySelector('input[name="total_amount[]"]');

    const price = parseFloat(priceInput.value) || 0;
    const totalAmount = (newQuantity * price).toFixed(2);

    // Update all fields
    hiddenQuantity.value = newQuantity;
    totalAmountInput.value = totalAmount;

    // Update cart and save
    updateCartTotal();

}
</script>
<script>
const extraProducts = @json($extraitem);

window.addToCart = function (product) {
    const branch = @json($branch);
    if (branch == 1 && product.dataset.remainingStock !== undefined) {
        const remainingStock = parseInt(product.dataset.remainingStock);
        if (remainingStock <= 0) {
            alert('This product is out of stock!');
            return;
        }
    }
    const productId = product.dataset.productId;
    const productExtras = extraProducts.filter(ep => ep.product_id == productId);

    if (productExtras.length > 0) {
        showExtraProductsModal(product, productExtras);
    } else {
        addProductToCart(product); // No extras
    }
};

function showExtraProductsModal(product, extras) {
    const existingModal = document.getElementById('extraProductsModal');
    if (existingModal) existingModal.remove();

   const modalHTML = `
<div id="extraProductsModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.4); z-index: 50; display: flex; align-items: center; justify-content: center;">
  <div style="background-color: white; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 32rem; padding: 1.5rem;">
    <h3 style="text-align: center; font-size: 1.5rem; font-weight: 600; color: #1e40af; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
      Select Extra Product
    </h3>

    <div style="display: grid; gap: 0.75rem; max-height: 15rem; overflow-y: auto; margin-bottom: 1.5rem;">
      ${extras.map(extra => `
        <button
          onclick="addExtraProductToCart(${product.dataset.productId}, ${extra.id})"
          style="width: 100%; padding: 0.75rem 1rem; background-color: #fed7aa; color: #1f2937; font-weight: 500; border-radius: 0.5rem; text-align: left; cursor: pointer; border: none;"
          onmouseover="this.style.backgroundColor='#fdba74'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)'"
          onmouseout="this.style.backgroundColor='#fed7aa'; this.style.boxShadow='none'"
          onfocus="this.style.outline='none'; this.style.boxShadow='0 0 0 2px #fb923c'"
        >
          ${extra.product_name}
        </button>
      `).join('')}
    </div>

    <div style="display: flex; justify-content: space-between; gap: 1rem;">
      <button
        onclick="skipExtraAndAddOriginal('${product.dataset.productId}')"
        style="flex: 1; padding: 0.5rem; background-color: #1d4ed8; color: white; border-radius: 0.375rem; font-weight: 600; border: none; cursor: pointer;"
        onmouseover="this.style.backgroundColor='#1e40af'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)'"
        onmouseout="this.style.backgroundColor='#1d4ed8'; this.style.boxShadow='none'"
        onfocus="this.style.outline='none'; this.style.boxShadow='0 0 0 2px #60a5fa'"
      >
        Skip
      </button>

      <button
        onclick="closeExtraProductsModal()"
        style="flex: 1; padding: 0.5rem; background-color: #ef4444; color: white; border-radius: 0.375rem; font-weight: 600; border: none; cursor: pointer;"
        onmouseover="this.style.backgroundColor='#dc2626'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)'"
        onmouseout="this.style.backgroundColor='#ef4444'; this.style.boxShadow='none'"
        onfocus="this.style.outline='none'; this.style.boxShadow='0 0 0 2px #f87171'"
      >
        Cancel
      </button>
    </div>
  </div>
</div>
`;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.body.style.overflow = 'hidden';
}

function skipExtraAndAddOriginal(productId) {
    const originalProduct = getProductById(productId);
    if (originalProduct) {
        // Create a clean product object with extraId as '0' (no extra)
        const productToAdd = {
            dataset: {
                productId: originalProduct.dataset.productId,
                productName: originalProduct.dataset.productName,
                sellingCost: originalProduct.dataset.sellingCost,
                unit: originalProduct.dataset.unit,
                buyCost: originalProduct.dataset.buyCost,
                rate: originalProduct.dataset.rate,
                purchaseVat: originalProduct.dataset.purchaseVat,
                inclusiveRate: originalProduct.dataset.inclusiveRate,
                inclusiveVatAmount: originalProduct.dataset.inclusiveVatAmount,
                vat: originalProduct.dataset.vat,
                extraId: '0' // No extra product
            }
        };

        addProductToCart(productToAdd);
        closeExtraProductsModal();
    } else {
        console.error('Original product not found for ID:', productId);
        closeExtraProductsModal();
    }
}

function closeExtraProductsModal() {
    const modal = document.getElementById('extraProductsModal');
    if (modal) modal.remove();
    document.body.style.overflow = '';
}

function addExtraProductToCart(productId, extraId) {
    const product = getProductById(productId);
    const extra = extraProducts.find(ep => ep.id == extraId);

    if (product && extra) {
        const combinedProduct = {
            dataset: {
                productId: productId,
                extraId: extraId, // ✅ ADDED
                productName: `${product.dataset.productName} + ${extra.product_name}`,
                sellingCost: parseFloat(extra.selling_cost),
                unit: extra.unit || product.dataset.unit,
                buyCost: extra.buy_cost || product.dataset.buyCost,
                rate: extra.rate || product.dataset.rate,
                purchaseVat: extra.purchase_vat || product.dataset.purchaseVat,
                inclusiveRate: extra.inclusive_rate || product.dataset.inclusiveRate,
                inclusiveVatAmount: extra.inclusive_vat_amount || product.dataset.inclusiveVatAmount,
                vat: extra.vat || product.dataset.vat
            }
        };

        addProductToCart(combinedProduct);
        closeExtraProductsModal();
    }
}


function addProductToCart(product) {
    if (typeof product === 'number') {
        product = getProductById(product);
    } else if (typeof product === 'string') {
        try {
            product = JSON.parse(product);
        } catch (e) {
            console.error('Error parsing product:', e);
            return;
        }
    }

    const productId = product.dataset.productId;
    const extraId = product.dataset.extraId || '0';
    const cartKey = `${productId}-${extraId}`;
    const productName = product.dataset.productName;
    const sellingCost = parseFloat(product.dataset.sellingCost);
    const unit = product.dataset.unit;
    const buyCost = product.dataset.buyCost;
    const rate = product.dataset.rate;
    const purchaseVat = product.dataset.purchaseVat;
    const inclusiveRate = product.dataset.inclusiveRate;
    const inclusiveVatAmount = product.dataset.inclusiveVatAmount;
    const vat = product.dataset.vat;
    const remainingStock = product.dataset.remainingStock; // Get stock info

    const existingCartItem = document.querySelector(`#cart-item-${cartKey}`);

    if (existingCartItem) {
        const branch = @json($branch);
        if (branch == 1 && remainingStock !== undefined) {
            const currentQuantity = parseInt(existingCartItem.querySelector('.quantity-input').value);
            if (currentQuantity >= parseInt(remainingStock)) {
                alert('Cannot add more than available stock!');
                return;
            }
        }

        const quantityInput = existingCartItem.querySelector('.quantity-input');
        let newQuantity = parseInt(quantityInput.value) + 1;
        quantityInput.value = newQuantity;
        existingCartItem.querySelector('input[name="quantity[]"]').value = newQuantity;

        const priceInput = existingCartItem.querySelector('.price-input');
        const currentPrice = parseFloat(priceInput.value);
        existingCartItem.querySelector('input[name="total_amount[]"]').value = (newQuantity * currentPrice).toFixed(2);
        updateCartTotal();
    } else {
        const cartItem = document.createElement('div');
        cartItem.id = `cart-item-${cartKey}`;
        cartItem.className = "border p-2 rounded-md flex items-center justify-between";
        cartItem.dataset.remainingStock = remainingStock; // Store stock info in cart item

        const initialVatAmount = (sellingCost - parseFloat(inclusiveRate)).toFixed(3);

        cartItem.innerHTML = `
        <div class="cart-item" style="width:100%;">
            <div class="cart-item-header">
                <div class="cart-item-name">${productName}</div>
                <button type="button" onclick="removeCartItem(this)" class="remove-btn">✖</button>
            </div>

            <div class="cart-item-details">
                <div class="price-container">
                    <input type="text"
                        class="price-input"
                        value="${sellingCost.toFixed(2)}"
                        onclick="this.select(); event.stopPropagation();"
                        oninput="updateProductPrice(this, ${vat}, ${inclusiveRate}, ${sellingCost});"
                        onfocus="document.getElementById('barcodeScanner').blur();">
                </div>

                <div class="quantity-container">
                    <span class="quantity-control">
                        <button type="button" onclick="decreaseQuantity(this)" style="background-color: #f5f5f5; padding: 5px 10px; border: none; cursor: pointer;">➖</button>
                        <input type="text" class="quantity-input" value="1"
                            onclick="this.select(); event.stopPropagation();"
                            oninput="handleQuantityChange(this)"
                            onblur="validateQuantityInput(this)"
                            onfocus="document.getElementById('barcodeScanner').blur();">
                        <button type="button" onclick="increaseQuantity(this)" style="background-color: #f5f5f5; padding: 5px 10px; border: none; cursor: pointer;">➕</button>
                    </span>
                    <span class="unit-label">${unit}</span>
                    <input type="hidden" name="quantity[]" value="1">
                    <input type="hidden" name="total_amount[]" value="${sellingCost}">
                </div>
            </div>

            <input type="hidden" name="product_id[]" value="${productId}">
            <input type="hidden" name="extra_id[]" value="${extraId}">
            <input type="hidden" name="prounit[]" value="${unit}">
            <input type="hidden" name="productName[]" value="${productName}">
            <input type="hidden" name="buy_cost[]" value="${buyCost}">
            <input type="hidden" name="buycost_rate[]" value="${rate}">
            <input type="hidden" name="purchase_vat[]" value="${purchaseVat}">
            <input type="hidden" name="inclusive_rate[]" value="${inclusiveRate}">
            <input type="hidden" name="vat_amount[]" value="${initialVatAmount}">
            <input type="hidden" name="mrp[]" value="${sellingCost}">
            <input type="hidden" name="net_rate[]" value="${sellingCost}">
            <input type="hidden" name="fixed_vat[]" value="${vat}">
            <input type="hidden" name="original_inclusive_rate[]" value="${inclusiveRate}">
            <input type="hidden" name="original_selling_cost[]" value="${sellingCost}">
        `;

        document.getElementById('cart-items').appendChild(cartItem);
        document.getElementById('cart-summary').classList.remove('hidden');
        document.getElementById('payment-method').classList.remove('hidden');
    }

    updateCartTotal();
}

function getProductById(productId) {
    const productElement = document.querySelector(`.product-item[data-product-id="${productId}"]`);
    if (productElement) return productElement;

    const product = allProducts.find(p => p.id == productId);
    if (product) {
        return {
            dataset: {
                productId: product.id,
                productName: product.product_name,
                sellingCost: product.selling_cost,
                unit: product.unit,
                buyCost: product.buy_cost,
                rate: product.rate,
                purchaseVat: product.purchase_vat,
                inclusiveRate: product.inclusive_rate,
                inclusiveVatAmount: product.inclusive_vat_amount,
                vat: product.vat,
                extraId: '0' // ✅ ADDED default
            }
        };
    }

    return null;
}
</script>

