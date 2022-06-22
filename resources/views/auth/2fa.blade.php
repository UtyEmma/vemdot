<!doctype html>
<html class="no-js" lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login | Laravel Admin Starter Kit - Radmin</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon" />

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/ionicons/dist/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/icon-kit/dist/css/iconkit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/theme.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script src="{{ asset('src/js/vendor/modernizr-2.8.3.min.js') }}"></script>
    </head>

    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="logo-centered">
                            <a href="http://radmin.rakibhstu.com"><img height="40" src="{{ asset('img/logo.png') }}" alt="RADMIN" ></a>
                        </div>
                        <div class="card-header">2FA Verification</div>
          
                        <div class="card-body">
                            <form method="POST" action="{{ route('2fa.post') }}">
                                @csrf
          
                                <p class="text-center">We sent code to your phone : {{ substr(auth()->user()->phone, 0, 5) . '******' . substr(auth()->user()->phone,  -2) }}</p>
          
                                @if ($message = Session::get('success'))
                                    <div class="row">
                                      <div class="col-md-12">
                                          <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button> 
                                              <strong>{{ $message }}</strong>
                                          </div>
                                      </div>
                                    </div>
                                @endif
          
                                @if ($message = Session::get('error'))
                                    <div class="row">
                                      <div class="col-md-12">
                                          <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button> 
                                              <strong>{{ $message }}</strong>
                                          </div>
                                      </div>
                                    </div>
                                @endif
          
                                <div class="form-group row">
                                    <label for="code" class="col-md-4 col-form-label text-md-right">Code</label>
          
                                    <div class="col-md-6">
                                        <input id="code" type="number" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>
          
                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
          
                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <a class="btn btn-link" href="{{ route('2fa.resend') }}">Resend Code?</a>
                                    </div>
                                </div>
          
                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
          
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="{{ asset('src/js/vendor/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('plugins/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('plugins/screenfull/dist/screenfull.js') }}"></script>
        
    </body>
</html>
