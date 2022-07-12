@extends('layouts.main')
@section('title', 'Wallet Fund Transaction History')
@section('content')
@php  $segment = request()->segment(4); @endphp
<!-- push external head elements to head -->
@push('head')

@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Wallet Fund Transaction History" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('transaction/fundwallet/by/date')}}" submit="Fetch Transaction" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('transaction/fundwallet/by/type')}}" header="Filter Transaction">
                <option>{{__('Vendor')}}</option>
                <option>{{__('Logistic')}}</option>
                <option>{{__('User')}}</option>
            </x-filterByType>
        </div>
        <div class="col-md-12">
             {{-- table section --}}
             <x-table header="Wallet Fund Transaction History">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Reference')}}</th>
                    <th class="text-center">{{ __('Business Name')}}</th>
                    <th class="text-center">{{ __('Email')}}</th>
                    <th class="text-center">{{ __('Account Type')}}</th>
                    <th class="text-center">{{ __('Amount')}}</th>
                    <th class="text-center">{{ __('Payment Method')}}</th>
                    <th class="text-center">{{ __('Status')}}</th>
                    <th class="text-center">{{ __('Transacted Date')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($transactions as $transaction)
                    @if(in_array($segment, ['Vendor', 'Logistic', 'User'])) 
                        @if($transaction->owner->userRole->name != $segment)
                            @continue
                        @endif
                    @endif
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center">{{ __($transaction->reference)}}</td>
                        <td class="text-center"><a href="/users/{{ __($transaction->owner->unique_id)}}">{{ __($transaction->owner->business_name ?? $transaction->owner->name )}}</a></td>
                        <td class="text-center">{{ __($transaction->owner->email)}}</td>
                        <td class="text-center">{{ __($transaction->owner->userRole->name)}}</td>
                        <td class="text-center">{{ __(number_format($transaction->amount))}} {{auth()->user()->currency()}}</td>
                        <td class="text-center">{{ __($transaction->channel)}}</td>
                        <td class="text-center">
                            <span class="badge light badge-{{$transaction->status == 'pending' ? 'warning':'success' }} ">
                                {{ $transaction->status }}
                            </span>
                        </td>
                        <td class="text-center">{{ __($transaction->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a data-toggle="modal" data-target="#deleteTransaction{{$transaction->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    <x-modal call="deleteTransaction{{$transaction->unique_id}}" header="Delete Transaction" message="Do you really want to delete this transaction ?">
                        <form action="{{url('transaction/delete')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$transaction->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $transactions->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
    <x-totalSummary>
        <x-slot name="count">
            <td>{{count($transactions)}}</td>
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
