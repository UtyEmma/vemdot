@extends('layouts.main')
@section('title', 'Users')
@section('content')

<!-- push external head elements to head -->
@push('head')
<link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
@endpush



<div class="container-fluid">
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="ik ik-inbox bg-blue"></i>
                <div class="d-inline">
                    <h5>{{ __('Users')}}</h5>
                    <span>{{ __('lorem ipsum dolor sit amet, consectetur adipisicing elit')}}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <nav class="breadcrumb-container" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>{{ __('Users')}}</h3></div>
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
                        {{$count = 0;}}
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ __(++$count)}}</td>
                                <td><img src="{{$user->avatar ?? '../img/users/1.jpg'}}" class="table-user-thumb" alt=""></td>
                                <td><a href="/users/{{ __($user->unique_id)}}">{{ __($user->name)}}</a></td>
                                <td>{{ __($user->email)}}</td>
                                <td>{{ __($user->userRole->name ?? $user->role)}}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="#"><i class="ik ik-eye"></i></a>
                                        <a href="#"><i class="ik ik-edit-2"></i></a>
                                        <a href="#"><i class="ik ik-trash-2"></i></a>
                                    </div>
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
