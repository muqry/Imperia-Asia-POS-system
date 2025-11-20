@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @livewire('order')


    <div class="modal">
        <div id="print">
            @include('reports.receipt')
        </div>
    </div>


    <!--Style is here-->
    <style>
        .modal.right .modal-dialog {
            /*position: absolute*/
            top: 0;
            right: 0;
            margin-right: 19vh;
        }

        .modal.fade:not(.in).right .modal-dialog {
            -webkit-transform: translate3d(25%, 0, 0);
            transform: translate3d(25%, 0, 0);
        }

        .payment-methods {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .radio-item input[type="radio"] {
            visibility: hidden;
            width: 20px;
            height: 20px;
            margin: 0 5px 0 5px;
            padding: 0;
            cursor: pointer;
        }

        /* before style*/
        .radio-item input[type="radio"]:before {
            position: relative;
            margin: 4px -25px 4px 0;
            display: inline-block;
            visibility: visible;
            width: 20px;
            height: 20px;
            border-radius: 10px;
            border: 2px inset rgb(150, 150, 150, 0.75);
            background: radial-gradient(ellipse at top left, rgb(255, 255, 255) 0%, rgb(250, 250, 250) 5%, rgb(230, 230, 230) 95%, rgb(255, 255, 255) 100%);
            content: '';
            cursor: pointer;
        }

        /**checked After style */
        .radio-item input[type="radio"]:checked:after {
            position: relative;
            top: -8px;
            left: 9px;
            display: inline-block;
            border-radius: 6px;
            visibility: visible;
            width: 12px;
            height: 12px;
            background: radial-gradient(ellipse at top left, rgb(240, 255, 220) 0%, rgb(225, 250, 100) 5%, rgb(75, 75, 0) 95%, rgb(25, 100, 0) 100%);
            content: '';
            cursor: pointer;
        }

        /**After Checked */
        .radio-item input[type="radio"].true:checked::after {
            background: radial-gradient(ellipse at top left, rgb(240, 255, 220) 0%, rgb(225, 250, 100) 5%, rgb(75, 75, 0) 95%, rgb(25, 100, 0) 100%);
        }

        .radio-item input[type="radio"].false:checked::after {
            background: radial-gradient(ellipse at top left, rgb(255, 255, 255) 0%, rgb(250, 250, 250) 5%, rgb(230, 230, 230) 95%, rgb(255, 255, 255) 100%);
        }


        .radio-item label {
            display: inline-block;
            margin: 0;
            padding: 0;
            line-height: 25px;
            height: 25px;
            cursor: pointer;
        }
    </style>

    @endsection

    @section('script')
    <script>
        //$(document).ready(function(){

        //})
        $('.add_more').on('click', function() {
            var product = $('.product_id').html();
            var numberofrow = ($('.addMoreProduct tr').length - 0) + 1;
            var tr = '<tr><td class="no">' + numberofrow + '</td>' +
                '<td><select class="form-control product_id" name="product_id[]">' + product +
                '</select></td>' +
                '<td><input type="number" name="quantity[]" class="form-control quantity" value="1" min="1"></td>' +
                '<td><input type="number" name="price[]" class="form-control price"></td>' +
                '<td><input type="number" name="discount[]" class="form-control discount"></td>' +
                '<td><input type="number" name="total_amount[]" class="form-control total_amount"></td>' +
                '<td><a class="btn btn-danger btn-sm delete rounded-circle"><i class="fa fa-times"></i></a></td>'
            $('.addMoreProduct').append(tr);
        });

        //delete a row
        $('.addMoreProduct').delegate('.delete', 'click', function() {
            $(this).parent().parent().remove();
            TotalAmount();
        })

        function TotalAmount() {
            //make all the logics here
            var total = 0;
            $('.total_amount').each(function(i, e) {
                var amount = $(this).val() - 0;
                total += amount;
            });

            $('.total').html(total);
        }


        $('.addMoreProduct').delegate('.product_id', 'change', function() {
            var tr = $(this).parent().parent();
            var price = tr.find('.product_id option:selected').attr('data-price');
            tr.find('.price').val(price);
            var quantity = tr.find('.quantity').val() - 0;
            var discount = tr.find('.discount').val() - 0;
            var price = tr.find('.price').val() - 0;
            var total_amount = (quantity * price) - ((quantity * price * discount) / 100);
            tr.find('.total_amount').val(total_amount);
            TotalAmount();
        })

        $('.addMoreProduct').delegate('.quantity , .discount', 'input', function() {
            var tr = $(this).parent().parent();
            var quantity = tr.find('.quantity').val() - 0;
            var discount = tr.find('.discount').val() - 0;
            var price = tr.find('.price').val() - 0;
            var total_amount = (quantity * price) - ((quantity * price * discount) / 100);
            tr.find('.total_amount').val(total_amount);
            TotalAmount();
        })

        $('#paid_amount').keyup(function() {
            //alert(1)
            var total = $('.total').html();
            var paid_amount = $(this).val();
            var tot = paid_amount - total;
            $('#balance').val(tot).toFixed(2);
        })



        //ORIGINALLLLLLLLLLLLLLLLL
        // Print Section
        function PrintReceiptContent(el) {
            const receiptContent = document.getElementById(el).innerHTML;

            const styleLinks = `
            <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Adjust path if needed -->
                <style>
                    @media print {
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
                `;

            const htmlContent = `
            <html>
                <head>
                    <title>Print Receipt</title>
                    ${styleLinks}
                </head>
            <body>
                <input
                type="button"
                id="printPageButton"
                style="display: block; width: 100%; border: none; background-color: #008B8B; color: #fff; padding: 14px 28px; font-size: 16px; cursor: pointer; text-align: center;"
                value="Print Receipt"
                onclick="window.print()"
                />
                ${receiptContent}
            </body>
            </html>
            `;

            const myReceipt = window.open("", "myWin", "left=150, top=130, width=400, height=600");
            myReceipt.document.write(htmlContent);
            myReceipt.document.close(); // Important for rendering
            myReceipt.focus();

            /*setTimeout(() => {
                myReceipt.close();
            }, 8000);*/
        }




        // dah ngam tapi print dua kali pulakkkkkkk
        /*function PrintReceiptContent(el) {
            var receiptContent = document.getElementById(el).innerHTML;

            // Manually define the styles from your partial
            var styles = `
            @include('reports.receipt')
        `;

            var myReceipt = window.open("", "myWin", "left=150,top=130,width=400,height=600");

            myReceipt.document.write(`
            <html>
                <head>
                    <title>Print Receipt</title>
                    ${styles}
                </head>
                <body onload="window.print()">
                    ${receiptContent}
                </body>
            </html>
        `);

            myReceipt.document.close();
            myReceipt.focus();
        }*/
    </script>

    @endsection