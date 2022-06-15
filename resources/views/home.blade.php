<!doctype html>
<html class="no-js" lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Radmin - Laravel Admin Starter</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon" />

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- themekit admin template asstes -->
        <link rel="stylesheet" href="{{ asset('all.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/theme.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/icon-kit/dist/css/iconkit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/ionicons/dist/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-12 m-5 text-center">
		        	<a href="http://rakibul.dev">
		            	<img height="40" src="{{ asset('img/logo.png') }}">
		            </a>
		        </div>
		        <div class="col-md-12 m-5 mt-0 text-center">
		            <h6>Hello <span class="text-danger">Artisan</span>,</h6>
		            <h1 class="m-5">This is your homepage!</h1>
		            <a href="{{url('login')}}" class="btn btn-success">Go to Admin</a>
		            <a href="http://radmin.rakibhstu.com/docs/" class="btn btn-primary">Docs</a>
		            <a href="https://documenter.getpostman.com/view/11223504/Szmh1vqc?version=latest" class="btn btn-danger">API Endpoint</a>
		            <br>
		            <br>
		            <br>
		            <hr>
		            <p>Need more help?</p>
                    Rakib<br>
		            Email: rakib1708@gmail.com <br>
		            Skype: rakib1708  <br>
		            <div class="card-body template-demo">
                        <a href="https://github.com/rakibdevs" class="btn social-btn text-white btn-facebook "><i class="fab fa-github"></i></a>
                        <a href="https://twitter.com/rakibul_dev" class="btn social-btn text-white btn-twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/rakibhstu/" class="btn social-btn text-white btn-linkedin"><i class="fab fa-linkedin"></i></a>
                    </div>
		            
		        </div>

		        </div>
		    </div>
		</div>
		<script src="{{ asset('all.js') }}"></script>
        
    </body>
</html>

