@extends('layouts.main')
@section('title', 'KYC Requests')
@section('content')

<!-- push external head elements to head -->
@push('head')
<link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="KYC Requests" />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>{{ __('KYC Request')}}</h3></div>
                <div class="card-body">
                    <table id="data_table" class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Id')}}</th>
                                <th class="nosort">{{ __('Avatar')}}</th>
                                <th>{{ __('Name')}}</th>
                                <th>{{ __('Email')}}</th>
                                <th>{{ __('Role')}}</th>
                                <th class="nosort">{{ __('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{$count = 0}}
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ __(++$count)}}</td>
                                    <td><img src="{{$user->avatar ?? asset('default.png')}}" class="table-user-thumb" alt=""></td>
                                    <td>{{ __($user->name)}}</td>
                                    <td>{{ __($user->email)}}</td>
                                    <td>{{ __($user->userRole->name)}}</td>
                                    <td>
                                        <a href="{{route('users.kyc.update', ['status' => 'confirmed', 'user_id' => $user->unique_id])}}" class="btn btn-success">Confirm</a>
                                        <a href="{{route('users.kyc.update', ['status' => 'declined', 'user_id' => $user->unique_id])}}" class="btn btn-danger">Decline</a>
                                    </td>
                                </tr>
                            @empty
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
