@extends('layouts.main')
@section('title', 'Users')
@section('content')

<!-- push external head elements to head -->
@push('head')
<link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Users" />

    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <x-filterbydate url="{{url('users/interface/by/date')}}" submit="Fetch Users" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>{{ __('Users')}}</h3></div>
                <div class="card-body">
                    <table id="data_table" class="table">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('Id')}}</th>
                                <th class="text-center">{{ __('Avatar')}}</th>
                                <th class="text-center">{{ __('Name')}}</th>
                                <th class="text-center">{{ __('Email')}}</th>
                                <th class="text-center">{{ __('Role')}}</th>
                                <th class="text-center">{{ __('Status')}}</th>
                                <th class="text-center">{{ __('Date Joined')}}</th>
                                <th class="text-right">{{ __('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 0; @endphp
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">{{ __(++$count)}}</td>
                                    <td class="text-center"><img src="{{$user->avatar ?? asset('default.png')}}" class="table-user-thumb" alt=""></td>
                                    <td class="text-center"><a href="/users/{{ __($user->unique_id)}}">{{ __($user->name)}}</a></td>
                                    <td class="text-center">{{ __($user->email)}}</td>
                                    <td class="text-center">{{ __($user->userRole->name ?? $user->role)}}</td>
                                    <td class="text-center">
                                        <span class="badge light badge-{{ $user->status == 'pending' ? 'warning':'success' }} ">
                                            {{ $user->status == 'pending' ? 'Blocked' : 'Confirmed' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ __($user->created_at->diffForHumans())}}</td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="/users/{{ __($user->unique_id)}}"><i class="ik ik-edit-2"></i></a>
                                            <a data-toggle="modal" data-target="#{{$user->status == 'pending' ? 'unBlockUser' : 'blockUser'}}{{$user->unique_id}}"><i class="ik ik-eye"></i></a>
                                            <a data-toggle="modal" data-target="#deleteUser{{$user->unique_id}}"><i class="ik ik-trash-2"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <x-modal call="unBlockUser{{$user->unique_id}}" header="Unblock User" message="Do you really want to un-block this user ?">
                                    <form action="{{url('activate/user/status')}}" method="POST">@csrf
                                        <input type="hidden" name="unique_id" value="{{$user->unique_id}}">
                                        <input type="hidden" name="status" value="unblock">
                                        <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                    </form>
                                </x-modal>
                                <x-modal call="blockUser{{$user->unique_id}}" header="Block User" message="Do you really want to block this user ?">
                                    <form action="{{url('activate/user/status')}}" method="POST">@csrf
                                        <input type="hidden" name="unique_id" value="{{$user->unique_id}}">
                                        <input type="hidden" name="status" value="block">
                                        <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                    </form>
                                </x-modal>
                                <x-modal call="deleteUser{{$user->unique_id}}" header="Delete User" message="Do you really want to delete this user ?">
                                    <form action="{{url('delete/user')}}" method="POST">@csrf
                                        <input type="hidden" name="unique_id" value="{{$user->unique_id}}">
                                        <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                    </form>
                                </x-modal>
                            @empty
                                <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- push external js -->
@push('script')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
@endpush

@endsection
