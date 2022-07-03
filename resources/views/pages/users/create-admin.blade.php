@extends('layouts.main')
@section('title', 'Create Admin')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="Create Admin" />

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Create Admin')}}</h3></div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('admin/create')}}">@csrf
                            <div class="form-group">
                                <label for="name">{{ __('Full Name')}} <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('Email')}} <small class="text-danger">*</small></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">{{ __('Phone')}} <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                            </div>
                            <div class="form-group">
                                <label for="country">{{ __('Country')}}</label>
                                <select class="form-control" id="country" name="country">
                                    @forelse($country as $each_country)
                                        <option {{$each_country->name == 'Nigeria' ? 'selected' : null}} value="{{$each_country->unique_id}}">{{$each_country->name}}</option>
                                    @empty
                                        <option value="">No Data</option>    
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">{{ __('Password')}} <small class="text-danger">*</small></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">{{ __('Continue')}}</button>
                        </form>
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
