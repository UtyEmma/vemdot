<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
	<title>@yield('title','') | {{env('APP_NAME')}} </title>
	<!-- initiate head with meta tags, css and script -->
	@include('include.head')

</head>
<body id="app" >
    <div class="wrapper">
    	<!-- initiate header-->
    	@include('include.header')
    	<div class="page-wrap">
	    	<!-- initiate sidebar-->
	    	@include('include.sidebar')

	    	<div class="main-content">
	    		<!-- yeild contents here -->
	    		@yield('content')
	    	</div>

	    	<!-- initiate chat section-->
	    	@include('include.chat')


	    	<!-- initiate footer section-->
	    	@include('include.footer')

    	</div>
    </div>

	<x-modal call="logoutUser" header="Logout Notification" message="Do you really wish to logout from {{env('APP_NAME')}} ?">
		<a href="{{ url('logout') }}" class="btn btn-primary">{{ __('Continue')}}</a>
	</x-modal>

	<!-- initiate modal menu section-->
	@include('include.modalmenu')

	<!-- initiate scripts-->
	@include('include.script')

	@include('sweetalert::alert')
</body>
</html>
