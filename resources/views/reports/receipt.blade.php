<div id="receiptContent">
    <div class="invoice-POS">

        <!--Print Section -->

        <div class="printed_content">

            <center id="top">
                <div class="logo"><img src="{{ asset('build/assets/logo2.png') }}" alt="Logo2" style="width: 60px; height: 60px; border-radius: 50%;"></div>
                <div class="info"></div>
                <h2>Imperia Asia POS System</h2>
            </center>
        </div>

        <div class="mid">
            <div class="info">
                <h2>Contact Us</h2>
                <p>
                    <b>Address :</b> Wisma Imperia Asia <br> No 20 Jalan Klang Sentral 2/KU5 <br> Klang Sentral <br> 41050 Klang <br> Selangor <br> Malaysia
                    <br><b>Email :</b> hi@imperiaasia.com
                    <br><b>Phone :</b> 03-3341 9822
                </p>
            </div>

        </div>
        <!--End Of Receipt Mid-->

        <div class="bot">
            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item" style="width: 45%;">
                            <h2>Item</h2>
                        </td>
                        <td class="Hour" style="width: 10%;">
                            <h2>Qty</h2>
                        </td>
                        <td class="Rate" style="width: 20%;">
                            <h2>Price Unit</h2>
                        </td>
                        <td class="Rate" style="width: 20%;">
                            <h2>Discount</h2>
                        </td>
                        <td class="Rate" style="width: 20%;">
                            <h2>Sub Total</h2>
                        </td>
                    </tr>

                    @foreach ($order_receipt as $receipt)

                    <tr class="service">
                        <td class="tableitem">
                            <p class="itemtext">{{ $receipt->product->product_name }}</p>
                        </td><!--product name-->
                        <td class="tableitem">
                            <p class="itemtext">{{ $receipt->quantity }}</p>
                        </td><!--quantity-->
                        <td class="tableitem">
                            <p class="itemtext">{{ number_format($receipt->unitprice, 2) }}</p>
                        </td> <!--unit price-->
                        <td class="tableitem">
                            <p class="itemtext">{{ $receipt->discount ? $receipt->discount . '%' : '0%' }}</p>
                        </td> <!--discount-->
                        <td class="tableitem">
                            @php
                            // calculate line total after discount
                            $unit = floatval($receipt->unitprice);
                            $qty = intval($receipt->quantity);
                            $disc = floatval($receipt->discount);
                            $finalUnit = $unit - ($unit * $disc / 100);
                            $lineTotal = $finalUnit * $qty;
                            @endphp
                            <p class="itemtext">{{ number_format($lineTotal, 2) }}</p> <!-- sub total-->
                        </td>
                    </tr>
                    @endforeach

                    @php
                    $subTotal = 0;
                    foreach ($order_receipt as $receipt) {
                    $unit = floatval($receipt->unitprice);
                    $qty = intval($receipt->quantity);
                    $disc = floatval($receipt->discount);
                    $finalUnit = $unit - ($unit * $disc / 100);
                    $subTotal += floatval( $finalUnit * $qty);
                    }

                    // If you ever apply tax, just change here
                    $taxRate = 0; // e.g. 6 for 6%
                    $taxAmount = ($subTotal * $taxRate) / 100;
                    $grandTotal = $subTotal + $taxAmount;
                    @endphp

                    <!--sub total-->
                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">
                            <p class="itemtext">Sub Total</p>
                        </td>
                        <td class="Payment">
                            <p class="itemtext">{{ number_format($subTotal, 2) }}</p>
                        </td>
                    </tr>

                    <!--tax-->
                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">
                            <p class="itemtext">Tax</p>
                        </td>
                        <td class="Payment">
                            <p class="itemtext">{{ number_format($taxAmount, 2) }}</p>
                        </td>
                    </tr>

                    <!--Overall Total-->
                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">Total</td>
                        <td class="Payment">{{ number_format($grandTotal, 2) }}</td>
                    </tr>

                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">
                            <p class="itemtext">Paid Amount</p>
                        </td>
                        <td class="Payment">
                            <p class="itemtext">
                                {{ isset($transaction) ? number_format($transaction->paid_amount, 2) : '0.00' }}
                            </p>
                        </td>
                    </tr>


                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">
                            <p class="itemtext">Balance</p>
                        </td>
                        <td class="Payment">
                            <p class="itemtext">
                                {{ number_format((float)$transaction->paid_amount - (float)$grandTotal, 2) }}
                            </p>
                        </td>
                    </tr>




                </table>

                <div class="legalcopy">
                    <p class="legal">
                        <strong> ** Thank You For Visiting And Purchasing With Us**</strong>
                        <br>
                        The Good Which Are Subject To Tax, Prices Included Tax
                    </p>
                </div>
                <div class="serial-number">
                    Serial : <span class="serial">
                        {{ str_pad($orders->first()->id, 5, '0', STR_PAD_LEFT) }}{{ now()->format('YmdHis') }}
                    </span>
                    <span>
                        {{ $orders->last()->created_at->setTimezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .invoice-POS {
        box-shadow: 0 0 1in -0.25in rgb(0, 0, 0.5);
        padding: 2mm;
        margin: 0 auto;
        width: 58mm;
        background: #fff;
    }

    .invoice-POS::selection {
        background: #34495E;
        color: #fff;
    }

    .invoice-POS ::-moz-selection {
        background: #34495E;
        color: #fff;
    }

    .invoice-POS h1 {
        font-size: 1.5em;
        color: #222;
    }

    .invoice-POS h2 {
        font-size: 0.5em;

    }

    .invoice-POS h3 {
        font-size: 1.2em;
        font-weight: 300;
        line-height: 2em;
    }

    .invoice-POS p {
        font-size: 0.7em;
        line-height: 1.2em;
        color: #666;
    }

    .invoice-POS .top .invoice-POS .mid .invoice-POS .bot {
        border-bottom: 1px solid #eee;
    }

    .invoice-POS .top {
        min-height: 100px;
    }

    .invoice-POS .mid {
        min-height: 80px;
    }

    .invoice-POS .bot {
        min-height: 50px;
    }

    .invoice-POS .top .logo {
        height: 60px;
        width: 60px;
        background-image: url() no-repeat;
        background-size: 60px 60px;
        border-radius: 50px;
    }

    .invoice-POS .info {
        display: block;
        margin-left: 0;
        text-align: center;
    }

    .invoice-POS .title {
        float: right;
    }

    .invoice-POS .title p {
        text-align: right;
    }

    .invoice-POS table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-POS .tabletitle {
        font-size: 0, 5em;
        background: #eee;
    }

    .invoice-POS .service {
        border-bottom: 1px solid #eee;
    }

    .invoice-POS .item {
        width: 24mm;
    }

    .invoice-POS .itemtext {
        font-size: 0.5em;
    }

    .invoice-POS .legalcopy {
        margin-top: 5mm;
        text-align: center;
    }

    .serial-number {
        margin-top: 5mm;
        margin-bottom: 2mm;
        text-align: center;
        font-size: 12px;
    }

    .serial {
        font-size: 10px !important;
    }
</style>