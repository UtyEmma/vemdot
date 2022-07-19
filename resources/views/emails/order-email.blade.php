<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Awesome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <style>
        body {
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

    <div class="">
        <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="text-center logo p-2 px-5">
                    <img src="{{asset('img/logo.png')}}" width="50">
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
                                            <small class="d-block text-muted">Order Date</small>
                                            <span>{{$date}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <small class="d-block text-muted">Order No</small>
                                            <span>VM-{{$order->reference}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <small class="d-block text-muted">Expected Time</small>
                                            <span>{{$avg_time}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="py-2">
                                            <small class="d-block text-muted">Shiping Address</small>
                                            <span>{{$order->receiver_location}}</s>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="product border-bottom table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                @forelse ($order->meals as $meal)
                                    <tr>
                                        <td width="20%">
                                            <img src="{{$meal['thumbnail']}}" width="90">
                                        </td>
                                        <td width="60%">
                                            <span class="font-weight-bold">{{$meal['name']}}</span>
                                            <div class="product-qty">
                                                <span class="d-block">Quantity: {{$meal['qty']}}</span>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <div class="text-right">
                                                <span class="font-weight-bold">&#x20A6; {{number_format($meal['unit_price'])}} * {{$meal['qty']}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
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
                                                <span>&#x20A6; {{number_format($order->amount)}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Delivery Fee</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span>&#x20A6; {{number_format($order->delivery_fee)}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Tax Fee</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span>&#x20A6; {{$tax}}</span>
                                            </div>
                                        </td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="text-muted">Discount</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span class="text-success">&#x20A6; {{$order->}}</span>
                                            </div>
                                        </td>
                                    </tr> --}}
                                    <tr class="border-top border-bottom">
                                        <td>
                                            <div class="text-left">
                                                <span class="font-weight-bold">Total</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-right">
                                                <span class="font-weight-bold">&#x20A6; {{number_format($order->amount + $order->delivery_fee)}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p>We will be sending shipping confirmation email when the item shipped successfully!</p>
                    <p class="font-weight-bold mb-0">Thanks for shopping with us!</p> <span>{{env('APP_NAME')}} Team</span>
                    </div>
                    <div class="d-flex justify-content-between footer p-3">
                        <span>Need Help? Visit our <a href="#"> help center</a></span>
                        <span>{{now()->format('jS F, Y')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

