@extends('layouts.main')
@section('title', 'Profile')
@section('content')


<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Profile" />
    
    <div class="row">
        <div class="col-lg-4 col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{asset($user->avatar ?? asset('default.png'))}}" style="object-fit: cover;" class="rounded-circle" width="150" />
                        <h4 class="card-title mt-10">{{ __($user->name)}}</h4>
                        <p class="card-subtitle">{{ __($user->userRole->name)}}</p>
                        <div class="row text-center justify-content-md-center">
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="ik ik-user"></i> <font class="font-medium">254</font></a></div>
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="ik ik-image"></i> <font class="font-medium">54</font></a></div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 text-center">
                            <h6>Account Status</h6>
                            <p class="card-subtitle">{{ __($user->status)}}</p>
                        </div>

                        <div class="col-md-6 text-center">
                            <h6>KYC Status</h6>
                            <p class="card-subtitle">{{ __($user->kyc_status)}}</p>
                        </div>
                    </div>
                </div>
                <hr class="mb-0">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('Email address')}} </small>
                    <h6>{{$user->email}}</h6>
                    <small class="text-muted d-block pt-10">{{ __('Phone')}}</small>
                    <h6>{{$user->phone}}</h6>
                    <small class="text-muted d-block pt-10">{{ __('Address')}}</small>
                    <h6>{{$user->business_name}}</h6>
                    <div class="map-box">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d248849.886539092!2d77.49085452149588!3d12.953959988118836!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1670c9b44e6d%3A0xf8dfc3e8517e4fe0!2sBengaluru%2C+Karnataka!5e0!3m2!1sen!2sin!4v1542005497600" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                    <small class="text-muted d-block pt-30">{{ __('Social Profile')}}</small>
                    <br/>
                    <button class="btn btn-icon btn-facebook"><i class="fab fa-facebook-f"></i></button>
                    <button class="btn btn-icon btn-twitter"><i class="fab fa-twitter"></i></button>
                    <button class="btn btn-icon btn-instagram"><i class="fab fa-instagram"></i></button>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#last-month" role="tab" aria-controls="pills-profile" aria-selected="false">{{ __('Profile')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-timeline-tab" data-toggle="pill" href="#current-month" role="tab" aria-controls="pills-timeline" aria-selected="true">{{ __('Edit')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-kyc-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-kyc" aria-selected="false">{{ __('KYC')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-setting-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-setting" aria-selected="false">{{ __('Setting')}}</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="last-month" role="tabpanel" aria-labelledby="pills-profile-tab">
                        @include('pages.users.components.edit')
                    </div>
                    <div class="tab-pane fade" id="current-month" role="tabpanel" aria-labelledby="pills-timeline-tab">
                        @include('pages.users.components.settings')
                    </div>
                    <div class="tab-pane fade" id="previous-month" role="tabpanel" aria-labelledby="pills-setting-tab">
                        @include('pages.users.components.details')
                    </div>
                    <div class="tab-pane fade" id="previous-month" role="tabpanel" aria-labelledby="pills-kyc-tab">
                        @include('pages.users.components.settings')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
