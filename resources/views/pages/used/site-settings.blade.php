@extends('layouts.main')
@section('title', 'Site Settings')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="Site Settings" />

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-timeline-tab" data-toggle="pill" href="#current-month" role="tab" aria-controls="pills-timeline" aria-selected="true">{{ __('Basic Settings')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#last-month" role="tab" aria-controls="pills-profile" aria-selected="false">{{ __('Advance Settings')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="current-month" role="tabpanel" aria-labelledby="pills-timeline-tab">
                            <div class="card-body">
                                <form class="form-horizontal row" method="POST" action="{{url('update/site/settings')}}" enctype="multipart/form-data">@csrf
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_name">{{ __('Site Name')}}</label>
                                        <input type="text" placeholder="Site Name" class="form-control" name="site_name" value="{{$appSettings->site_name}}" id="site_name">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_email">{{ __('Site Email')}}</label>
                                        <input type="email" placeholder="Site Email" class="form-control" name="site_email" value="{{$appSettings->site_email}}" id="site_email">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_phone">{{ __('Site Phone')}}</label>
                                        <input type="text" placeholder="Site Phone" class="form-control" name="site_phone" value="{{$appSettings->site_phone}}" id="site_phone">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_domain">{{ __('Site Domain')}}</label>
                                        <input type="url" placeholder="Site Domain" class="form-control" name="site_domain" value="{{$appSettings->site_domain}}" id="site_domain">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="referral_bonus">{{ __('Referral Bonus')}}</label>
                                        <input type="number" placeholder="Referral Bonus" class="form-control" name="referral_bonus" value="{{$appSettings->referral_bonus}}" id="referral_bonus">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="token_length">{{ __('Token Length')}}</label>
                                        <input type="number" placeholder="Token Length" class="form-control" name="token_length" value="{{$appSettings->token_length}}" id="token_length">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_address">{{ __('Site Address')}}</label>
                                        <input type="text" placeholder="Referral Bonus" class="form-control" name="site_address" value="{{$appSettings->site_address}}" id="site_address">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="thumbnail">{{ __('Site Logo')}}</label>
                                        <input type="file"  class="form-control" name="thumbnail" id="thumbnail">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="account_verification">{{ __('Account Verification')}}</label>
                                        <select name="account_verification" id="account_verification" class="form-control">
                                            <option {{($appSettings->account_verification == 'yes')?'selected':''}}>{{ __('Yes')}}</option>
                                            <option {{($appSettings->account_verification == 'no')?'selected':''}}>{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="login_alert">{{ __('Login Alert')}}</label>
                                        <select name="login_alert" id="login_alert" class="form-control">
                                            <option {{($appSettings->login_alert == 'yes')?'selected':''}}>{{ __('Yes')}}</option>
                                            <option {{($appSettings->login_alert == 'no')?'selected':''}}>{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="welcome_message">{{ __('Welcome Message')}}</label>
                                        <select name="welcome_message" id="welcome_message" class="form-control">
                                            <option {{($appSettings->welcome_message == 'yes')?'selected':''}}>{{ __('Yes')}}</option>
                                            <option {{($appSettings->welcome_message == 'no')?'selected':''}}>{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="send_basic_emails">{{ __('Send Basic Emails')}}</label>
                                        <select name="send_basic_emails" id="send_basic_emails" class="form-control">
                                            <option {{($appSettings->send_basic_emails == 'yes')?'selected':''}}>{{ __('Yes')}}</option>
                                            <option {{($appSettings->send_basic_emails == 'no')?'selected':''}}>{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <button class="btn btn-success" type="submit">Update Settings</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="last-month" role="tabpanel" aria-labelledby="pills-profile-tab">
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/form-components.js') }}"></script>
    @endpush
@endsection
