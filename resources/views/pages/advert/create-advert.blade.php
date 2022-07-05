@extends('layouts.main')
@section('title', 'Add New Advert')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="Add New Advert" />

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Add New Advert')}}</h3></div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('advert/create')}}" enctype="multipart/form-data">@csrf
                            <div class="form-group">
                                <label for="caption">{{ __('Ad`s Caption')}}</label>
                                <input type="text" class="form-control" id="caption" name="caption" placeholder="Caption">
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('Email')}}</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                            </div>
                            <p class="alert alert-warning">Note! <br> This is to help the system send mails / notification to the user requesting advert</p>
                            <div class="form-group">
                                <label for="banner">{{ __('Banner')}} <small class="text-danger">*</small></label>
                                <input type="file" class="form-control" id="banner" name="banner" required>
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
