<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
</head>
<body>


    <style>
        body {
        background-color: #ffe8d2;
        font-family: 'Montserrat', sans-serif
        }
        .card {
        border: none
        }
        .logo {
        background-color: #eeeeeea8
        }
        .totals tr td {
        font-size: 13px
        }
        .footer {
        background-color: #eeeeeea8
        }
        .footer span {
        font-size: 12px
        }
        .product-qty span {
        font-size: 12px;
        color: #dedbdb
        }
    </style>

    <div class="container mt-5 mb-5">
        <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="text-center logo p-2 px-5">
                    <img src="https://i.imgur.com/2zDU056.png" width="50">
                </div>
                <div class="invoice p-5">
                    <h5>Your order has been confirmed!</h5>
                    <span class="font-weight-bold d-block mt-4">Hello, {{$user->name}}</span>
                    <span>You order from {{$vendor->business_name}} has been confirmed and is being processed!</span>

                    <div class="payment border-top mt-3 mb-3 border-bottom table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="py-2">
                                            <span class="d-block text-muted">Order Date</span>
                                            <span>{{$date}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <span class="d-block text-muted">Order No</span>
                                            <span>VM-{{$order->reference}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <span class="d-block text-muted">Expected Time</span>
                                            <span>
                                                {{$avg_time}}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <span class="d-block text-muted">Shiping Address</span>
                                            <span>{{$order->receiver_location}}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="product border-bottom table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td width="20%">
                                        <img src="https://i.imgur.com/u11K1qd.jpg" width="90">
                                    </td>
                                    <td width="60%">
                                        <span class="font-weight-bold">Men's Sports cap</span>
                                        <div class="product-qty">
                                            <span class="d-block">Quantity:1</span>
                                            <span>Color:Dark</span>
                                        </div>
                                    </td>
                                    <td width="20%">
                                        <div class="text-right">
                                            <span class="font-weight-bold">$67.50</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">
                                        <img src="https://i.imgur.com/SmBOua9.jpg" width="70">
                                    </td>
                                    <td width="60%">
                                        <span class="font-weight-bold">Men's Collar T-shirt</span>
                                        <div class="product-qty">
                                            <span class="d-block">Quantity:1</span>
                                            <span>Color:Orange</span>
                                        </div>
                                    </td>
                                    <td width="20%">
                                        <div class="text-right">
                                            <span class="font-weight-bold">$77.50</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row d-flex justify-content-end">
                        <div class="col-md-5">
                            <table class="table table-borderless">
                                <tbody class="totals">
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Subtotal</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span>$168.50</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Shipping Fee</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span>$22</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Tax Fee</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span>$7.65</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Discount</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span class="text-success">$168.50</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-top border-bottom">
                                        <td>
                                            <div class="text-left">
                                                <span class="font-weight-bold">Subtotal</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span class="font-weight-bold">$238.50</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p>We will be sending shipping confirmation email when the item shipped successfully!</p>
                    <p class="font-weight-bold mb-0">Thanks for shopping with us!</p> <span>Nike Team</span>
                    </div>
                    <div class="d-flex justify-content-between footer p-3">
                        <span>Need Help? visit our <a href="#"> help center</a></span>
                        <span>12 June, 2020</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

