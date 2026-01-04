<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <table class="table" style="font-weight: bold;">
        <thead>
            <tr>
                <th colspan="6">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3">&nbsp;Dr</td>
                <td colspan="3">&nbsp;Cr</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="2">Opening Stock</td>
                <td> {{ $opening_stock }}</td>
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="3">Purchase Accounts</td>
                <td colspan="3">Sales Accounts</td>
            </tr>

            <tr>
                <td>Purchases</td>
                <td>{{ number_format($purchase_amount, 3) }}</td>
                <td>&nbsp;</td>

                <td>Sales</td>
                <td>{{ number_format($soldstock_value, 3) }}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Purchase Return</td>
                <td>{{ number_format($purchaseReturn, 3) }}</td>
                <td>&nbsp;</td>

                <td>Sales Return</td>
                <td>{{ number_format($salesReturn, 3) }}</td>
                <td></td>
            </tr>

            <tr>
                <td colspan="2">Total</td>
                <td>
                    {{ number_format($purchase_amount - $purchaseReturn, 3) }}
                </td>

                <td colspan="2">Total</td>
                <td>{{ number_format($soldstock_value - $salesReturn, 3) }}</td>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td colspan="2"><b>Closing Stock</b></td>
                <td>{{ $closing_stock }}</td>
            </tr>

            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="2"><b>Grand Total</b></td>
                <td> {{ number_format($half_Dr = $opening_stock + ($purchase_amount - $purchaseReturn), 3) }}</td>

                <td colspan="2"><b>Grand Total</b></td>
                <td>{{ number_format($half_Cr = $soldstock_value - $salesReturn + $closing_stock, 3) }}</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><b>Service</b></td>
                <td>  {{ number_format($purchaseservice - $returnpurchaseservice, 3) }}</td>

                <td colspan="2"><b>Service</b></td>
                <td> {{number_format($service_cost + $onlyservice_cost,3)}}</td>
            </tr>

            <tr>
                @php
                    $gross_loss = 0;
                    $gross_profit = 0;
                @endphp
                <td colspan="2">
                    @if ($half_Dr > $half_Cr)
                        <b>Gross Loss</b>
                    @endif
                </td>
                <td>
                    @if ($half_Dr > $half_Cr)
                        <b>{{ $currency }}</b> {{ $gross_loss += $half_Dr - $half_Cr }}
                    @endif
                </td>
                <td colspan="{{ $half_Dr < $half_Cr ? '2' : '3' }}">
                    @if ($half_Dr < $half_Cr)
                        <b>Gross Profit</b>
                    @endif
                </td>
                <td>
                    @if ($half_Dr < $half_Cr)
                        <b>{{ $currency }}</b> {{ $gross_profit += $half_Cr - $half_Dr }}
                    @endif
                </td>

            </tr>


            <tr>
                @php
                    $gross_profit_bd = 0;
                @endphp
                <td colspan="3">&nbsp;</td>
                <td colspan="{{ $half_Dr < $half_Cr ? '2' : '3' }}">
                    @if ($half_Dr < $half_Cr)
                        <b>Gross Profit B/D</b>
                    @endif
                </td>
                <td>
                    @if ($half_Dr < $half_Cr)
                        <b>{{ $currency }}</b> {{ $gross_profit_bd += $half_Cr - $half_Dr }}
                    @endif
                </td>
            </tr>

            <tr>
                @php
                    $gross_loss_bd = 0;
                @endphp
                <td colspan="2">
                    @if ($half_Dr > $half_Cr)
                        <b>Gross Loss B/D</b>
                    @endif
                </td>
                <td>
                    @if ($half_Dr > $half_Cr)
                        <b>{{ $currency }}</b> {{ $gross_loss_bd += $half_Dr - $half_Cr }}
                    @endif
                </td>
                <td colspan="{{ $half_Dr < $half_Cr ? '2' : '3' }}">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>


            <tr>
                <td colspan="3"><b>Direct Expenses</b></td>
                <td colspan="3"><b>Direct Incomes</b></td>
            </tr>

            @php
                $maxRows = max(count($direct_expense), count($direct_income));
            @endphp

            @for ($i = 0; $i < $maxRows; $i++)
            <tr>
                <td>
                    @if (isset($direct_expense[$i]))
                        {{ $direct_expense[$i]->direct_expense }}
                    @endif
                </td>
                <td>
                    @if (isset($direct_expense[$i]))
                        {{ $direct_expense[$i]->amount }}
                    @endif
                </td>
                <td>&nbsp;</td>
                <td>
                    @if (isset($direct_income[$i]))
                        {{ $direct_income[$i]->direct_income }}
                    @endif
                </td>
                <td>
                    @if (isset($direct_income[$i]))
                        {{ $direct_income[$i]->amount }}
                    @endif
                </td>

            </tr>

            @endfor

            <tr>
                <td width="10%"><b>Total Direct Expenses</b></td>
                <td width="10%"></td>
                <td width="10%">{{ $total_direct_expense }}</td>
                <td width="10%"><b>Total Direct Incomes</b></td>
                <td width="10%"></td>
                <td width="10%">{{ $total_direct_income }}</td>
            </tr>

            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>Indirect Expenses</b></td>
                <td colspan="3"><b>Indirect Incomes</b></td>
            </tr>

            @php
                $maxRows = max(count($indirect_expense), count($indirect_incomes));
            @endphp

            @for ($i = 0; $i < $maxRows; $i++)
            <tr>
                <td>
                    @if (isset($indirect_expense[$i]))
                        {{ $indirect_expense[$i]->indirect_expense }}
                    @endif
                </td>
                <td>
                    @if (isset($indirect_expense[$i]))
                        {{ $indirect_expense[$i]->amount }}
                    @endif
                </td>
                <td>&nbsp;</td>
                <td>
                    @if (isset($indirect_incomes[$i]))
                        {{ $indirect_incomes[$i]->indirect_income }}
                    @endif
                </td>
                <td>
                    @if (isset($indirect_incomes[$i]))
                        {{ $indirect_incomes[$i]->amount }}
                    @endif
                </td>
            </tr>
            @endfor

            <tr>
                <td>Discount</td>
                <td>{{number_format($totaldiscountsale=$discount - $return_discount,3)}}</td>
                <td ></td>
                <td>Discount</td>
                <td>{{number_format($totaldiscountpurchase=$discountpurchase - $purchase_return_discount,0)}}</td>


            </tr>
            <tr>
                <td width="10%"><b>Total Direct Expenses</b></td>
                <td width="10%"></td>
                <td width="10%">{{ round($total_indirect_expense + $totaldiscountsale,2) }}</td>
                <td width="10%"><b>Total Direct Incomes</b></td>
                <td width="10%"></td>
                <td width="10%">{{ round($total_indirect_income + $totaldiscountpurchase,2)}}</td>
            </tr>

            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>


            <tr>
                <td colspan="2">Total Expense</td>
                <td>
                    {{ number_format(round($Drr=$total_direct_expense + $total_indirect_expense + $totaldiscountsale + $gross_loss, 3)) }}
                </td>
                <td colspan="2">Total Income</td>
                <td>
                    {{ number_format(round($Crr=$total_direct_income + $total_indirect_income + $totaldiscountpurchase + $gross_profit,3)) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">Grand Total</td>
                <td>
                    {{ number_format(round($Dr=$total_direct_expense + $total_indirect_expense + $totaldiscountsale + $half_Dr, 3)) }}
                </td>
                <td colspan="2">Grand Total</td>
                <td>
                    {{ number_format(round($Cr=$total_direct_income + $total_indirect_income + $totaldiscountpurchase + $half_Cr,3)) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">Net Loss</td>
                <td>
                    @if ($Dr > $Cr)
                        {{round( $Dr - $Cr,2) }}
                    @endif
                </td>

                <td colspan="2">Net Profit</td>
                <td>
                    @if ($Cr > $Dr)
                        {{round( $Cr - $Dr,2) }}
                    @endif

                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
