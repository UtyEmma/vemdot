@extends('layouts.main')
@section('title', 'Order History')
@section('content')
@php  $segment = request()->segment(3); @endphp
<!-- push external head elements to head -->
@push('head')
    
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Order History" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('orders/history/interface/by/date')}}" submit="Fetch Order" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('orders/history/interface/by/type')}}" header="Filter Order">
                <option value="cancelled">{{__('Cancelled')}}</option>
                <option value="declined">{{__('Declined')}}</option>
                <option value="terminated">{{__('Terminated')}}</option>
                <option value="failed">{{__('Failed')}}</option>
                <option value="delivered">{{__('Delivered')}}</option>
            </x-filterByType>
        </div>
        <div class="col-md-12">
             {{-- table section --}}
             <x-table header="Order History">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('User Name')}}</th>
                    <th class="text-center">{{ __('Vendor Name')}}</th>
                    <th class="text-center">{{ __('Courier Name')}}</th>
                    <th class="text-center">{{ __('Amount')}}</th>
                    <th class="text-center">{{ __('Status')}}</th>
                    <th class="text-center">{{ __('Date')}}</th>
                    <th class="text-center">{{ __('View Meal')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($orders as $order)
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center"><a href="/users/{{ __($order->user->unique_id)}}">{{ __($order->user->business_name ?? $order->user->name )}}</a></td>
                        <td class="text-center"><a href="/users/{{ __($order->vendor->unique_id)}}">{{ __($order->vendor->business_name ?? $order->vendor->name )}}</a></td>
                        <td class="text-center"><a href="/users/{{ __($order->courier->unique_id)}}">{{ __($order->courier->business_name ?? $order->courier->name )}}</a></td>
                        <td class="text-center">{{ __(number_format($order->amount))}} {{auth()->user()->currency()}}</td>
                        @php $status = ['cancelled', 'declined', 'terminated', 'failed', 'delivered'] @endphp
                        @php $badge = ['primary', 'warning', 'danger', 'danger', 'success'] @endphp
                        <td class="text-center">
                        @foreach($status as $key => $item)
                            @if($item == $order->status)
                                <span class="badge light badge-{{$badge[$key] }} ">
                                    {{$item}}
                                </span>
                            @endif
                        @endforeach
                        </td>
                        <td class="text-center">{{ __($order->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <button data-toggle="modal" data-target="#viewOrder{{$order->unique_id}}" class="btn btn-primary">View Meal</button>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a data-toggle="modal" data-target="#deleteOrder{{$order->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    {{-- modal section --}}
                    <x-modal call="deleteOrder{{$order->unique_id}}" header="Delete Order History" message="Do you really want to delete this order ?">
                        <form action="{{url('orders/delete')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$order->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-side-modal call="viewOrder{{$order->unique_id}}" header="View Meals">
                        <div class="row">
                            <hr>
                            <div class="col-md-4 col-4"> <strong>{{ __('Meal Name')}}</strong>
                                <br>
                                <p class="text-muted">Food 1</p>
                            </div>
                            <div class="col-md-4 col-4"> <strong>{{ __('Quantity')}}</strong>
                                <br>
                                <p class="text-muted">2</p>
                            </div>
                            <div class="col-md-4 col-4"> <strong>{{ __('Price')}}</strong>
                                <br>
                                <p class="text-muted">100 NGN</p>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                            <div class="col-md-4 col-4"> <strong>{{ __('Meal Name')}}</strong>
                                <br>
                                <p class="text-muted">Food 2</p>
                            </div>
                            <div class="col-md-4 col-4"> <strong>{{ __('Quantity')}}</strong>
                                <br>
                                <p class="text-muted">6</p>
                            </div>
                            <div class="col-md-4 col-4"> <strong>{{ __('Price')}}</strong>
                                <br>
                                <p class="text-muted">8000 NGN</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12 text-center">
                                <hr>
                                <p><strong>Total Amount:</strong></p>
                                <h1 class="m-0 p-0">8100 NGN</h1>
                            </div>
                        </div>
                        <x-slot name="other">
                            <button class="btn btn-danger" data-toggle="modal" data-target="#terminateOrder{{$order->unique_id}}">Terminate Order</button>
                        </x-slot>
                    </x-side-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $orders->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')
    
@endpush

@endsection
