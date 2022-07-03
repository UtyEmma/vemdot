@extends('layouts.main')
@section('title', 'Edit Category')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="Edit Category">
        <li class="breadcrumb-item"><a href="{{url('view/categories')}}">{{ __('View Categories')}}</a></li>
        </x-pageHeader>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Edit Category')}}</h3></div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('update/category', $category->unique_id )}}" enctype="multipart/form-data">@csrf
                            <div class="form-group">
                                <label for="name">{{ __('Category Name')}} <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{$category->name}}" placeholder="Category Name" >
                            </div>
                            <div class="form-group">
                                <label for="thumbnail">{{ __('Category Thumbnail')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                            </div>
                            <div class="form-group">
                                <label for="description">{{ __('Description')}}</label>
                                <textarea class="form-control" id="description" name="description">{{$category->name}}</textarea>
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
