@extends('layouts.main')
@section('title', 'Create Category')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="Create Category" />

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Create Category')}}</h3></div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('create/category')}}" enctype="multipart/form-data">@csrf
                            <div class="form-group">
                                <label for="name">{{ __('Category Name')}} <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Category Name" required>
                            </div>
                            <div class="form-group">
                                <label for="thumbnail">{{ __('Category Thumbnail')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
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
