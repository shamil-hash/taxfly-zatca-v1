<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Profit & Loss Statement</title>
    
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        
        #content {
            padding: 20px;
            background: #fff;
        }
        
        h2 {
            color: #187f6a;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .formcss {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: #187f6a;
            border-color: #187f6a;
        }
        
        .btn-primary:hover {
            background-color: #136a58;
            border-color: #136a58;
        }
        
        .pandl-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .pandl-table th, 
        .pandl-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .pandl-table th {
            background-color: #187f6a;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .debit-side {
            border-right: 2px solid #333;
        }
        
        .amount {
            text-align: right;
        }
        
        .bold-line {
            font-weight: bold;
            border-bottom: 2px solid #333 !important;
        }
        
       
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .grand-total {
            font-weight: bold;
            background-color: #e9ecef;
            font-size: 1.1em;
        }
        
        .positive {
            color: #28a745;
        }
        
        .negative {
            color: #dc3545;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .report-period {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .section-header {
            background-color: #f1f1f1;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="content">
        <!-- Company Header -->
       
        
        <h2>PROFIT & LOSS STATEMENT</h2>
        
        @if(!$start_date)
        <div class="report-period">
        As of {{ now()->format('F j, Y') }} (Current Date)
        </div>
        @else
        <div class="report-period">
            For the period from {{ $start_date }} to {{ $end_date }}
        </div>
        @endif
        
        <!-- Filter Form -->
        <form class="formcss" action="/filterpandl" method="get" onsubmit="return validateDates()">
            <div class="row">
                <div class="col-md-12">
                    <h4>SELECT DATE RANGE</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="start_date">From</label>
                            <input type="date" class="form-control" value="{{ $start_date }}" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">To</label>
                            <input type="date" class="form-control" value="{{ $end_date }}" name="end_date" id="end_date">
                        </div>

                        @if (Session('adminuser'))
                        <div class="col-md-3">
                            <label for="location">Location</label>
                            <select class="form-control" name="location" id="location">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}"
                                        @if (isset($selectedLocation) && $selectedLocation == $location->id) selected @endif>
                                        {{ $location->location }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" value="" name="branch" id="branch">
                        </div>
                        @elseif(Session('softwareuser'))
                        <input type="hidden" value="{{$branchId}}" name="branches" id="branches">
                        @endif
                        
                        <div class="col-md-3" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary" style="margin-right: 10px;margin-top:25px;">Filter</button>
                            <a href="/exportpandl/{{ $start_date }}/{{ $end_date }}/" 
                               class="btn btn-primary export-link" 
                               title="Download Profit & Loss Report"  style="margin-top:25px;">
                                Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- P&L Table with Debit/Credit Columns -->
        <table class="pandl-table">
            <thead>
                <tr>
                    <th colspan="3" class="debit-side">DEBIT</th>
                    <th colspan="3">CREDIT</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Stock -->
                <tr>
                    <td colspan="2">Opening Stock</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($opening_stock, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
                
                <!-- Purchases Section -->
                <tr class="section-header">
                    <td colspan="3" class="debit-side">PURCHASES</td>
                    <td colspan="3">SALES</td>
                </tr>
                
                <tr>
                    <td colspan="2">Purchases</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($purchase_amount, 2) }}</td>
                    <td colspan="2">Sales</td>
                    <td class="amount">{{ $currency }} {{ number_format($soldstock_value, 2) }}</td>
                </tr>
                
                <tr>
                    <td colspan="2">Less: Purchase Returns</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($purchaseReturn, 2) }}</td>
                    <td colspan="2">Less: Sales Returns</td>
                    <td class="amount">{{ $currency }} {{ number_format($salesReturn, 2) }}</td>
                </tr>
                
                <tr class="bold-line">
                    <td colspan="2">Net Purchases</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($purchase_amount - $purchaseReturn, 2) }}</td>
                    <td colspan="2">Net Sales</td>
                    <td class="amount">{{ $currency }} {{ number_format($soldstock_value - $salesReturn, 2) }}</td>
                </tr>
                
                <tr style="display:none;">
                    <td colspan="2"></td>
                    <td class="debit-side"></td>
                    <td colspan="2">Less: Credit Notes</td>
                    <td class="amount">{{ $currency }} {{ number_format($total_credit_note, 2) }}</td>
                </tr>
                
                <tr class="double-line">
                    <td colspan="2">Cost of Goods Available</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($opening_stock + ($purchase_amount - $purchaseReturn), 2) }}</td>
                    <td colspan="2">Closing Stock</td>
                    <td class="amount">{{ $currency }} {{ number_format($closing_stock, 2) }}</td>
                </tr>
                
                <!-- Gross Profit/Loss -->
                <tr class="grand-total">
                    <td colspan="2">
                        @if (($soldstock_value - $salesReturn - $total_credit_note + $closing_stock) > ($opening_stock + ($purchase_amount - $purchaseReturn)))
                            GROSS LOSS
                        @else
                            GROSS PROFIT
                        @endif
                    </td>
                    <td class="amount debit-side">
                        @php
                            $gross_profit = ($soldstock_value - $salesReturn - $total_credit_note + $closing_stock) - ($opening_stock + ($purchase_amount - $purchaseReturn));
                        @endphp
                        @if ($gross_profit < 0)
                            <span class="negative">{{ $currency }} {{ number_format(abs($gross_profit), 2) }}</span>
                        @endif
                    </td>
                    <td colspan="2">
                        @if (($soldstock_value - $salesReturn - $total_credit_note + $closing_stock) > ($opening_stock + ($purchase_amount - $purchaseReturn)))
                            GROSS PROFIT
                        @else
                            GROSS LOSS
                        @endif
                    </td>
                    <td class="amount">
                        @if ($gross_profit > 0)
                            <span class="positive">{{ $currency }} {{ number_format(abs($gross_profit), 2) }}</span>
                        @endif
                    </td>
                </tr>
                
                <!-- Services Section -->
                <tr class="section-header" style="display:none;">
                    <td colspan="3" class="debit-side">SERVICE EXPENSES</td>
                    <td colspan="3">SERVICE INCOME</td>
                </tr>
                
                <tr style="display:none;">
                    <td colspan="2">Service Purchases</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($purchaseservice - $returnpurchaseservice, 2) }}</td>
                    <td colspan="2">Service Income</td>
                    <td class="amount">{{ $currency }} {{ number_format($service_cost + $onlyservice_cost, 2) }}</td>
                </tr>
                
                <!-- Net Service Profit/Loss -->
                <tr class="f">
                    <td colspan="2">
                        @if (($service_cost + $onlyservice_cost) < ($purchaseservice - $returnpurchaseservice))
                            NET SERVICE LOSS
                        @endif
                    </td>
                    <td class="amount debit-side">
                        @php
                            $service_profit = ($service_cost + $onlyservice_cost) - ($purchaseservice - $returnpurchaseservice);
                        @endphp
                        @if ($service_profit < 0)
                            <span class="negative">{{ $currency }} {{ number_format(abs($service_profit), 2) }}</span>
                        @endif
                    </td>
                    <td colspan="2">
                        @if (($service_cost + $onlyservice_cost) > ($purchaseservice - $returnpurchaseservice))
                            NET SERVICE PROFIT
                        @endif
                    </td>
                    <td class="amount">
                        @if ($service_profit > 0)
                            <span class="positive">{{ $currency }} {{ number_format(abs($service_profit), 2) }}</span>
                        @endif
                    </td>
                </tr>
                
                <!-- Direct Expenses/Income Section -->
                <tr class="section-header">
                    <td colspan="3" class="debit-side">DIRECT EXPENSES</td>
                    <td colspan="3">DIRECT INCOME</td>
                </tr>
                
                <!-- Direct Expenses -->
                @php
                    $max_direct = max(count($direct_expense), count($direct_income));
                @endphp
                
                @for($i = 0; $i < $max_direct; $i++)
                <tr>
                    @if(isset($direct_expense[$i]))
                    <td colspan="2">{{ $direct_expense[$i]->direct_expense }}</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($direct_expense[$i]->amount, 2) }}</td>
                    @else
                    <td colspan="2"></td>
                    <td class="debit-side"></td>
                    @endif
                    
                    @if(isset($direct_income[$i]))
                    <td colspan="2">{{ $direct_income[$i]->direct_income }}</td>
                    <td class="amount">{{ $currency }} {{ number_format($direct_income[$i]->amount, 2) }}</td>
                    @else
                    <td colspan="2"></td>
                    <td></td>
                    @endif
                </tr>
                @endfor
                
                <!-- Total Direct Expenses/Income -->
                <tr class="total-row">
                    <td colspan="2">Total Direct Expenses</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($total_direct_expense, 2) }}</td>
                    <td colspan="2">Total Direct Income</td>
                    <td class="amount">{{ $currency }} {{ number_format($total_direct_income, 2) }}</td>
                </tr>
                
                <!-- Indirect Expenses/Income Section -->
                <tr class="section-header">
                    <td colspan="3" class="debit-side">INDIRECT EXPENSES</td>
                    <td colspan="3">INDIRECT INCOME</td>
                </tr>
                
                <!-- Indirect Expenses -->
                @php
                    $max_indirect = max(count($indirect_expense), count($indirect_incomes));
                @endphp
                
                @for($i = 0; $i < $max_indirect; $i++)
                <tr>
                    @if(isset($indirect_expense[$i]))
                    <td colspan="2">{{ $indirect_expense[$i]->indirect_expense }}</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($indirect_expense[$i]->amount, 2) }}</td>
                    @else
                    <td colspan="2"></td>
                    <td class="debit-side"></td>
                    @endif
                    
                    @if(isset($indirect_incomes[$i]))
                    <td colspan="2">{{ $indirect_incomes[$i]->indirect_income }}</td>
                    <td class="amount">{{ $currency }} {{ number_format($indirect_incomes[$i]->amount, 2) }}</td>
                    @else
                    <td colspan="2"></td>
                    <td></td>
                    @endif
                </tr>
                @endfor
                
                <!-- Discounts -->
                <tr>
                    <td colspan="2">Sales Discount</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($discount - $return_discount, 2) }}</td>
                    <td colspan="2">Purchase Discount</td>
                    <td class="amount">{{ $currency }} {{ number_format($discountpurchase - $purchase_return_discount, 2) }}</td>
                </tr>
                
                <!-- Total Indirect Expenses/Income -->
                <tr class="total-row">
                    <td colspan="2">Total Indirect Expenses</td>
                    <td class="amount debit-side">{{ $currency }} {{ number_format($total_indirect_expense + ($discount - $return_discount), 2) }}</td>
                    <td colspan="2">Total Indirect Income</td>
                    <td class="amount">{{ $currency }} {{ number_format($total_indirect_income + ($discountpurchase - $purchase_return_discount), 2) }}</td>
                </tr>
                
                <!-- Net Profit/Loss Section -->
                <tr class="section-header">
                    <td colspan="3" class="debit-side">NET LOSS</td>
                    <td colspan="3">NET PROFIT</td>
                </tr>
                
                @php
                    $total_income = $gross_profit + $service_profit + $total_direct_income + $total_indirect_income + ($discountpurchase - $purchase_return_discount);
                    $total_expense = $total_direct_expense + $total_indirect_expense + ($discount - $return_discount);
                    $net_profit_loss = $total_income - $total_expense;
                @endphp
                
                <tr class="grand-total">
                    <td colspan="2">
                        @if ($net_profit_loss < 0)
                            NET LOSS FOR THE PERIOD
                        @endif
                    </td>
                    <td class="amount debit-side">
                        @if ($net_profit_loss < 0)
                            <span class="negative">{{ $currency }} {{ number_format(abs($net_profit_loss), 2) }}</span>
                        @endif
                    </td>
                    <td colspan="2">
                        @if ($net_profit_loss > 0)
                            NET PROFIT FOR THE PERIOD
                        @endif
                    </td>
                    <td class="amount">
                        @if ($net_profit_loss > 0)
                            <span class="positive">{{ $currency }} {{ number_format(abs($net_profit_loss), 2) }}</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var branch = '';

            // For adminuser session, use the hidden branch input
            @if (Session('adminuser'))
                branch = $('#location').val();
                console.log('Branch (Admin User): ' + branch);
            @elseif(Session('softwareuser'))
                branch = $('#branches').val();
                console.log('Branch (Software User): ' + branch);
            @endif

            var start_date = $('input[name="start_date"]').val();
            var end_date = $('input[name="end_date"]').val();

            updateExportLink(start_date, end_date, branch);

            // Event listener for location change (Admin user)
            $('#location').change(function() {
                branch = $('#location').val();
                console.log('Branch Changed (Admin User): ' + branch);
                $('#branch').val(branch);
                updateExportLink(start_date, end_date, branch);
            });

            // Event listener for branches change (Software user)
            $('#branches').change(function() {
                branch = $('#branches').val();
                console.log('Branch Changed (Software User): ' + branch);
                updateExportLink(start_date, end_date, branch);
            });

            function updateExportLink(start_date, end_date, branch) {
                var currentDate = new Date().toISOString().slice(0, 10);

                if (start_date === '' || end_date === '') {
                    start_date = currentDate;
                    end_date = currentDate;
                }

                var exportUrl = '/exportpandl/' + start_date + '/' + end_date + '/' + branch;
                $('a.export-link').attr('href', exportUrl);

                console.log('Export URL: ' + exportUrl);
            }
        });

        function validateDates() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (startDate && !endDate) {
                alert("Please select the 'To' date.");
                return false;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after the end date.');
                return false;
            }
            
            return true;
        }

        document.addEventListener("DOMContentLoaded", function () {
            const locationSelect = document.getElementById("location");
            const branchInput = document.getElementById("branch");

            if (locationSelect) {
                locationSelect.addEventListener("change", function () {
                    branchInput.value = this.value;
                });

                branchInput.value = locationSelect.value;
            }
        });
    </script>
</body>
</html>