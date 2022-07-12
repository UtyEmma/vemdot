@extends('layouts.main')
@section('title', 'Withdrawal History')
@section('content')
@php  $segment = request()->segment(4); @endphp
<!-- push external head elements to head -->
@push('head')
    
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Withdrawal History" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('withdrawal/histroy/interface/by/date')}}" submit="Fetch Withdrawals" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('withdrawal/histroy/interface/by/type')}}" header="Filter Withdrawal History">
                <option>{{__('Vendor')}}</option>
                <option>{{__('Logistic')}}</option>
                <option>{{__('User')}}</option>
            </x-filterByType>
        </div>
        <div class="col-md-12">
             {{-- table section --}}
             <x-table header="Withdrawal History">
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
                            <span class="badge light badge-{{$withdrawal->status == 'failed' ? 'warning':'success' }} ">
                                {{ $withdrawal->status }}
                            </span>
                        </td>
                        <td class="text-center">{{ __($withdrawal->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a data-toggle="modal" data-target="#deleteWithdrawal{{$withdrawal->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    {{-- modal section --}}
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
    <x-totalSummary>
        <x-slot name="count">
            <td>{{count($withdrawals)}}</td>
        </x-slot>
        <x-slot name="pending">
            <td>{{number_format($pendingAmount)}} {{auth()->user()->currency()}}</td>
        </x-slot>
        <x-slot name="failed">
            <td>{{number_format($failedAmount)}} {{auth()->user()->currency()}}</td>
        </x-slot>
        <x-slot name="confirmed">
            <td>{{number_format($confirmedAmount)}} {{auth()->user()->currency()}}</td>
        </x-slot>
        <x-slot name="total">
            <td>{{number_format($pendingAmount + $failedAmount + $confirmedAmount)}} {{auth()->user()->currency()}}</td>
        </x-slot>
    </x-totalSummary>
</div>

<!-- push external js -->
@push('script')
    
@endpush

@endsection
