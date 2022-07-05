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
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#last-month" role="tab" aria-controls="pills-profile" aria-selected="false">{{ __('Payment and Orders Settings')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="current-month" role="tabpanel" aria-labelledby="pills-timeline-tab">
                            <div class="card-body">
                                <form class="form-horizontal row" method="POST" action="{{url('update/site/settings')}}" enctype="multipart/form-data">
                                    @csrf
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
                                            <option {{($appSettings->account_verification == 'yes')?'selected':''}} value="yes">{{ __('Yes')}}</option>
                                            <option {{($appSettings->account_verification == 'no')?'selected':''}} value="no">{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="login_alert">{{ __('Login Alert')}}</label>
                                        <select name="login_alert" id="login_alert" class="form-control">
                                            <option {{($appSettings->login_alert == 'yes')?'selected':''}} value="yes">{{ __('Yes')}}</option>
                                            <option {{($appSettings->login_alert == 'no')?'selected':''}} value="no">{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="welcome_message">{{ __('Welcome Message')}}</label>
                                        <select name="welcome_message" id="welcome_message" class="form-control">
                                            <option {{($appSettings->welcome_message == 'yes')?'selected':''}} value="yes">{{ __('Yes')}}</option>
                                            <option {{($appSettings->welcome_message == 'no')?'selected':''}} value="no">{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="send_basic_emails">{{ __('Send Basic Emails')}}</label>
                                        <select name="send_basic_emails" id="send_basic_emails" class="form-control">
                                            <option {{($appSettings->send_basic_emails == 'yes')?'selected':''}} value="yes">{{ __('Yes')}}</option>
                                            <option {{($appSettings->send_basic_emails == 'no')?'selected':''}} value="no">{{ __('No')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <button class="btn btn-success" type="submit">Update Settings</button>
                                    </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="last-month" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_name">{{ __('Delivery Fee (Naira Per Kilometer)')}}</label>
                                        <input type="number" placeholder="Delivery Fee (Per Kilometer)" class="form-control" name="delivery_fee" value="{{$appSettings->delivery_fee}}" id="delivery_fee">
                                    </div>

                                    <div class="col-12">
                                        <strong>Service Charges</strong>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_name">{{ __('Vendor Service Charge - Charged from Earnings in Percentage')}}</label>
                                        <input type="number" placeholder="Vendor Service Charge (Per Kilometer)" class="form-control" name="vendor_service_charge" value="{{$appSettings->vendor_service_charge}}" id="vendor_service_charge">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6">
                                        <label for="site_name">{{ __('Courier Service Charge - Charged from Delivery Fee Earnings in Percentage')}}</label>
                                        <input type="number" placeholder="Service Charge (Per Kilometer)" class="form-control" name="logistics_service_charge" value="{{$appSettings->logistics_service_charge}}" id="logistics_service_charge">
                                    </div>

                                    <div class="col-12">
                                        <strong>Order Cancellations</strong>
                                        <p>If enabled users will be charged the set amount when they cancel an order and a vendor when they terminate an order they accepted</p>
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="site_name">{{ __('Enable Order Cancellations Fee')}}</label>
                                        <select name="charge_cancellations" id="charge_cancellations" class="form-control">
                                            <option {{($appSettings->charge_cancellations == 'yes')?'selected':''}} value="yes">{{ __('Yes')}}</option>
                                            <option {{($appSettings->charge_cancellations == 'no')?'selected':''}} value="no">{{ __('No')}}</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="referral_bonus">{{ __('Order Cancellation Fee (in %)')}}</label>
                                        <input type="number" placeholder="Cancellation Fee" class="form-control" name="cancellation_fee" value="{{$appSettings->cancellation_fee}}" id="cancellation_fee">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="referral_bonus">{{ __('Order Cancellation Limit (Daily)')}}</label>
                                        <input type="number" placeholder="Order Cancellation Charge" class="form-control" name="cancellation_limit" value="{{$appSettings->cancellation_limit}}" id="referral_bonus">
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <button class="btn btn-success" type="submit">Update Settings</button>
                                    </div>
                                </div>
                            </form>
                            </div>
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
