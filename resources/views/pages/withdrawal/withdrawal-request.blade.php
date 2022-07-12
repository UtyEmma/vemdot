@extends('layouts.main')
@section('title', 'Withdrawal Request')
@section('content')
@php  $segment = request()->segment(3); @endphp
<!-- push external head elements to head -->
@push('head')
    
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Withdrawal Request" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('withdrawal/interface/by/date')}}" submit="Fetch Request" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('withdrawal/interface/by/type')}}" header="Filter Withdrawal Request">
                <option>{{__('Vendor')}}</option>
                <option>{{__('Logistic')}}</option>
                <option>{{__('User')}}</option>
            </x-filterByType>
        </div>
        <div class="col-md-12">
             {{-- table section --}}
             <x-table header="Withdrawal Request">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Account Type')}}</th>
                    <th class="text-center">{{ __('User Name')}}</th>
                    <th class="text-center">{{ __('User Balance')}}</th>
                    <th class="text-center">{{ __('Requested Amount')}}</th>
                    <th class="text-center">{{ __('Bank Details')}}</th>
                    <th class="text-center">{{ __('Status')}}</th>
                    <th class="text-center">{{ __('Requested Date')}}</th>
                    <th class="text-center">{{ __('Payout')}}</th>
                    <th class="text-center">{{ __('Payout')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($withdrawals as $withdrawal)
                    @if(in_array($segment, ['Vendor', 'Logistic', 'User'])) 
                        @if($withdrawal->owner->userRole->name != $segment)
                            @continue
                        @endif
                    @endif
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center">{{ __($withdrawal->owner->userRole->name)}}</td>
                        <td class="text-center"><a href="/users/{{ __($withdrawal->owner->unique_id)}}">{{ __($withdrawal->owner->business_name ?? $withdrawal->owner->name )}}</a></td>
                        <td class="text-center">{{ __(number_format($withdrawal->owner->main_balance))}} {{auth()->user()->currency()}}</td>
                        <td class="text-center">{{ __(number_format($withdrawal->amount))}} {{auth()->user()->currency()}}</td>
                        <td class="text-center">
                            <a href="#viewDetails{{$withdrawal->unique_id}}" data-toggle="modal" data-target="#viewDetails{{$withdrawal->unique_id}}" class="btn btn-primary">View Details</a>
                        </td>
                        <td class="text-center">
                            <span class="badge light badge-{{$withdrawal->status == 'pending' ? 'warning':'success' }} ">
                                {{ $withdrawal->status }}
                            </span>
                        </td>
                        <td class="text-center">{{ __($withdrawal->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <button data-toggle="modal" data-target="#payOut{{$withdrawal->unique_id}}" class="btn btn-danger">Pay Out</button>
                        </td>
                        <td>
                            <button data-toggle="modal" data-target="#confirmRequest{{$withdrawal->unique_id}}" class="btn btn-warning">Confirm Request</button> 
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a data-toggle="modal" data-target="#declineWithdrawal{{$withdrawal->unique_id}}"><i class="ik ik-eye"></i></a>
                                <a data-toggle="modal" data-target="#deleteWithdrawal{{$withdrawal->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    {{-- modal section --}}
                    <x-modal call="payOut{{$withdrawal->unique_id}}" header="Payout Withdrawal Request">
                        <x-slot name="message">
                            <p class="alert alert-success">Withdrawal request is handledand settled by Paystack, please ensure to have enough balance on your paystack wallet dashboard before initiating performing this action </p>
                            <div class="form-group">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" readonly id="bank_name" value="{{$withdrawal->bankDetails->bank->name}}">
                            </div>
                            <div class="form-group">
                                <label for="name">Account Name</label>
                                <input type="text" class="form-control" readonly id="name" value="{{$withdrawal->bankDetails->account_name}}">
                            </div>
                            <div class="form-group">
                                <label for="number">Account Number</label>
                                <input type="number" class="form-control" readonly id="number" value="{{$withdrawal->bankDetails->account_no}}">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" readonly id="amount" value="{{$withdrawal->amount}}">
                            </div>
                        </x-slot>
                        <form action="{{url('withdrawal/payout')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$withdrawal->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="confirmRequest{{$withdrawal->unique_id}}" header="Confirm Withdrawal Request">
                        <x-slot name="message">
                            <p class="alert alert-warning">By confirming this request, ensure you have made payment to the account details provided above. </p>
                            <div class="form-group">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" readonly id="bank_name" value="{{$withdrawal->bankDetails->bank->name}}">
                            </div>
                            <div class="form-group">
                                <label for="name">Account Name</label>
                                <input type="text" class="form-control" readonly id="name" value="{{$withdrawal->bankDetails->account_name}}">
                            </div>
                            <div class="form-group">
                                <label for="number">Account Number</label>
                                <input type="number" class="form-control" readonly id="number" value="{{$withdrawal->bankDetails->account_no}}">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" readonly id="amount" value="{{$withdrawal->amount}}">
                            </div>
                        </x-slot>
                        <form action="{{url('withdrawal/confirm')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$withdrawal->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="declineWithdrawal{{$withdrawal->unique_id}}" header="Decline Withdrawal Request" message="Do you really want to decline this withdrawal request ?">
                        <form action="{{url('withdrawal/decline')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$withdrawal->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="deleteWithdrawal{{$withdrawal->unique_id}}" header="Delete Withdrawal Request" message="Do you really want to delete this withdrawal request ?">
                        <form action="{{url('withdrawal/delete')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$withdrawal->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-side-modal call="viewDetails{{$withdrawal->unique_id}}" header="View Bank Details">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body timeline">
                                    <div class="header bg-theme" style="background-image: url({{asset('img/placeholder/placeimg_400_200_nature.jpg')}})">
                                        <div class="color-overlay d-flex align-items-center">
                                            <h3 class="day-number text-center">{{$withdrawal->owner->business_name ?? $withdrawal->owner->name}} Account Details:</h3>
                                        </div>                                
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="bullet bg-pink"></div>
                                            <div class="time"></div>
                                            <div class="desc">
                                                <h3>Bank Name</h3>
                                                <h3>{{$withdrawal->bankDetails->bank->name}}</h3>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="bullet bg-pink"></div>
                                            <div class="time"></div>
                                            <div class="desc">
                                                <h3>Bank Code</h3>
                                                <h3>{{$withdrawal->bankDetails->bank->code}}</h3>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="bullet bg-green"></div>
                                            <div class="time"></div>
                                            <div class="desc">
                                                <h3>Account Name</h3>
                                                <h3>{{$withdrawal->bankDetails->account_name}}</h3>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="bullet bg-orange"></div>
                                            <div class="time"></div>
                                            <div class="desc">
                                                <h3>Account Number</h3>
                                                <h3>{{$withdrawal->bankDetails->account_no}}</h3>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <x-slot name="other">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWithdrawal{{$withdrawal->unique_id}}">{{ __('Aprrove Withdrawal')}}</button>
                        </x-slot>
                    </x-side-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $withdrawals->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')
    
@endpush

@endsection
