@extends('layouts.main')
@section('title', 'Admin')
@section('content')

<!-- push external head elements to head -->
@push('head')

@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Admin" />

    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <x-filterbydate url="{{url('admin/interface/by/date')}}" submit="Fetch Admin" />
            </div>
        </div>
        <div class="col-md-12">
            {{-- table section --}}
            <x-table header="Admins">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Avatar')}}</th>
                    <th class="text-center">{{ __('Name')}}</th>
                    <th class="text-center">{{ __('Email')}}</th>
                    <th class="text-center">{{ __('Phone')}}</th>
                    <th class="text-center">{{ __('Role')}}</th>
                    <th class="text-center">{{ __('Status')}}</th>
                    <th class="text-center">{{ __('Date Joined')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($admin as $each_admin)
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center">
                            <img src="{{$each_admin->avatar ?? asset('default.png')}}" class="table-user-thumb" alt="">
                        </td>
                        <td class="text-center"><a href="/users/{{ __($each_admin->unique_id)}}">{{ __($each_admin->name)}}</a></td>
                        <td class="text-center">{{ __($each_admin->email)}}</td>
                        <td class="text-center">{{ __($each_admin->phone)}}</td>
                        <td class="text-center">{{ __($each_admin->userRole->name ?? $each_admin->role)}}</td>
                        <td class="text-center">
                            <span class="badge light badge-{{ $each_admin->status == 'pending' ? 'warning':'success' }} ">
                                {{ $each_admin->status == 'pending' ? 'Blocked' : 'Confirmed' }}
                            </span>
                        </td>
                        <td class="text-center">{{ __($each_admin->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="/users/{{ __($each_admin->unique_id)}}"><i class="ik ik-edit-2"></i></a>
                                <a data-toggle="modal" data-target="#{{$each_admin->status == 'pending' ? 'unBlockAdmin' : 'blockAdmin'}}{{$each_admin->unique_id}}"><i class="ik ik-eye"></i></a>
                                <a data-toggle="modal" data-target="#deleteAdmin{{$each_admin->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    <x-modal call="unBlockAdmin{{$each_admin->unique_id}}" header="Unblock Admin" message="Do you really want to un-block this admin ?">
                        <form action="{{url('activate/user/status')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_admin->unique_id}}">
                            <input type="hidden" name="status" value="unblock">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="blockAdmin{{$each_admin->unique_id}}" header="Block Admin" message="Do you really want to block this admin ?">
                        <form action="{{url('activate/user/status')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_admin->unique_id}}">
                            <input type="hidden" name="status" value="block">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                    <x-modal call="deleteAdmin{{$each_admin->unique_id}}" header="Delete Admin" message="Do you really want to delete this admin ?">
                        <form action="{{url('delete/user')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_admin->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $admin->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')

@endpush

@endsection
