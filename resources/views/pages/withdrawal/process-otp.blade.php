<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{ __('Authenticate OTP |') }} {{env('APP_NAME')}}</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="{{ asset('favicon.png')}}" type="image/x-icon" />

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/ionicons/dist/css/ionicons.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/icon-kit/dist/css/iconkit.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}">
        <link rel="stylesheet" href="{{ asset('dist/css/theme.min.css')}}">
        <link rel="stylesheet" href="{{ asset('dist/css/style.css')}}">
        <link rel="stylesheet" href="{{ asset('dist/css/theme-image.css')}}">
        <script src="{{ asset('src/js/vendor/modernizr-2.8.3.min.js')}}"></script>
    </head>

    <body>
        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-12 col-lg-12 col-md-12 p-0 ">
                        <div class="lavalite-bg" >
                            <div class="lavalite-overlay">
                                <div class="authentication-form mx-auto">
                                    <div class="logo-centered">
                                        <a href="">
                                            <img width="150"  src="{{ asset('img/logo.png')}}" alt="">
                                        </a>
                                    </div>
                                    <h3 class="text-center text-white">{{ __('AUTHENTICATE OTP') }}</h3>
                                    <form method="POST" action="{{ url('withdrawal/process-otp') }}" class="row">@csrf
                                        <div class="form-group col-lg-6 offset-lg-3">
                                            <input type="number" class="form-control" placeholder="Enter OPT" name="otp" required>
                                            <i class="ik ik-code"></i>
                                        </div>
                                        <input type="hidden" class="form-control" value="{{$transfer_code}}" name="transfer_code" >
                                        <input type="hidden" class="form-control" value="{{$uniqueId}}" name="uniqueId" >
                                        <div class="sign-btn text-center col-lg-6 offset-lg-3">
                                            <button class="btn btn-theme">{{ __('Continue') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="{{ asset('src/js/vendor/jquery-3.3.1.min.js')}}"></script>
        <script src="{{ asset('plugins/popper.js')}}/dist/umd/popper.min.js')}}"></script>
        <script src="{{ asset('plugins/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
        <script src="{{ asset('plugins/screenfull/dist/screenfull.js')}}"></script>
        @include('sweetalert::alert')
    </body>
</html>
