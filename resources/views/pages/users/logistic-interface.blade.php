@extends('layouts.main')
@section('title', 'Logistic')
@section('content')

<!-- push external head elements to head -->
@push('head')

@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Logistic" />
    
    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <x-filterbydate url="{{url('logistic/interface/by/date')}}" submit="Fetch Logistic" />
            </div>
        </div>
        <div class="col-md-12">
            {{-- table section --}}
            <x-table header="Logistics">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Logo')}}</th>
                    <th class="text-center">{{ __('Business Name')}}</th>
                    <th class="text-center">{{ __('Email')}}</th>
                    <th class="text-center">{{ __('Phone')}}</th>
                    <th class="text-center">{{ __('Role')}}</th>
                    <th class="text-center">{{ __('Status')}}</th>
                    <th class="text-center">{{ __('Riders')}}</th>
                    <th class="text-center">{{ __('Date Joined')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($logistic as $each_logistic)
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center">
                            <img src="{{$each_logistic->logo ?? asset('default.png')}}" class="table-user-thumb" alt="">
                        </td>
                        <td class="text-center"><a href="/users/{{ __($each_logistic->unique_id)}}">{{ __($each_logistic->business_name ?? $each_logistic->name )}}</a></td>
                        <td class="text-center">{{ __($each_logistic->email)}}</td>
                        <td class="text-center">{{ __($each_logistic->phone)}}</td>
                        <td class="text-center">{{ __($each_logistic->userRole->name ?? $each_logistic->role)}}</td>
                        <td class="text-center">
                            <span class="badge light badge-{{ $each_logistic->status == 'pending' ? 'warning':'success' }} ">
                                {{ $each_logistic->status == 'pending' ? 'Blocked' : 'Confirmed' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/riders/interface/{{$each_logistic->unique_id}}" class="btn btn-primary">View Riders</a>
                        </td>
                        <td class="text-center">{{ __($each_logistic->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="/users/{{ __($each_logistic->unique_id)}}"><i class="ik ik-edit-2"></i></a>
                                <a data-toggle="modal" data-target="#{{$each_logistic->status == 'pending' ? 'unBlockVendor' : 'blockVendor'}}{{$each_logistic->unique_id}}"><i class="ik ik-eye"></i></a>
                                <a data-toggle="modal" data-target="#deleteVendor{{$each_logistic->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    <x-modal call="unBlockVendor{{$each_logistic->unique_id}}" header="Unblock Logistic" message="Do you really want to un-block this logistic ?">
                        <form action="{{url('activate/user/status')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_logistic->unique_id}}">
                            <input type="hidden" name="status" value="unblock">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="blockVendor{{$each_logistic->unique_id}}" header="Block Logistic" message="Do you really want to block this logistic ?">
                        <form action="{{url('activate/user/status')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_logistic->unique_id}}">
                            <input type="hidden" name="status" value="block">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="deleteVendor{{$each_logistic->unique_id}}" header="Delete Logistic" message="Do you really want to delete this logistic ?">
                        <form action="{{url('delete/user')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_logistic->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $logistic->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')

@endpush

@endsection
