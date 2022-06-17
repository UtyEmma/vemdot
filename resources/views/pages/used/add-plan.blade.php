@extends('layouts.main')
@section('title', 'Create Subscription Plan')
@section('content')

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-edit bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Create Subscription Plan')}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">{{ __('Subscription Plan')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Create Subscription Plan')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Create Subscription Plan')}}</h3></div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('create/plan')}}" enctype="multipart/form-data">@csrf
                            <div class="form-group">
                                <label for="name">{{ __('Plan Name')}} <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Plan Name" required>
                            </div>
                            <div class="form-group">
                                <label for="amount">{{ __('Plan Amount')}}<small class="text-danger">*</small>(USD)</label>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="Plan Amount (USD)" required >
                            </div>
                            <div class="form-group">
                                <label for="duration">{{ __('Duration')}} <small class="text-danger">*</small>(Days)</label>
                                <input type="number" class="form-control" id="duration" name="duration" placeholder="Duration"  required >
                            </div>
                            <div class="form-group">
                                <label for="thumbnail">{{ __('Plan Thumbnail')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                            </div>
                            <div class="form-group">
                                <label for="no_of_item">{{ __('No Of Items')}} <small class="text-danger">*</small></label>
                                <input type="number" class="form-control" id="no_of_item" name="no_of_item" placeholder="No Of Items" required >
                            </div>
                            <div class="form-group">
                                <label for="description">{{ __('Description')}}</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
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
