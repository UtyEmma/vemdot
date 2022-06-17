@extends('layouts.main')
@section('title', 'View Categories')
@section('content')

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-edit bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('View Categories')}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">{{ __('Meal Category')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('View Categories')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-block">
                        <h3>{{ __('View Categories')}}</h3>
                    </div>
                    <div class="card-body p-0 table-border-style">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">S/N</th>
                                        <th class="text-center">{{ __('Category Name')}}</th>
                                        <th class="text-center">{{ __('Thumbnail')}}</th>
                                        <th class="text-center">{{ __('Status')}}</th>
                                        <th class="text-center">{{ __('Date')}}</th>
                                        <th class="text-right">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($category) > 0)
                                        @php $counter = 1; @endphp
                                        @foreach ($category as $each_category)
                                        <tr>
                                            <th class="text-center" scope="row">{{ $counter }}</th>
                                            <td class="text-center">{{ $each_category->name }}</td>
                                            <td class="text-center">
                                                <img src="{{ ($each_category->thumbnail == 'default.png') ? asset('default.png') : $each_category->thumbnail }}" class="table-user-thumb" alt="{{ $each_category->name }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="badge light badge-{{ ($each_category->status == 'pending')?'warning':'success' }} ">
                                                    {{ $each_category->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $each_category->created_at->diffForHumans() }}</td>
                                            <td class="text-center">
                                                <div class="table-actions">
                                                    <a href="{{url('edit/category', $each_category->unique_id )}}"><i class="ik ik-edit-2"></i></a>
                                                    <a data-toggle="modal" data-target="#deleteCategory{{$each_category->unique_id}}"><i class="ik ik-trash-2"></i></a>
                                                </div>
                                            </td>
                                        </tr> 
                                        <div class="modal fade" id="deleteCategory{{$each_category->unique_id}}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="demoModalLabel">{{ __('Category Delete')}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        You are about to delete {{$each_category->name}} category, <br> (<strong>Note!</strong> This action won't go through if this category has been used to add a Meal.)
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
                                                       <form action="{{url('delete/category')}}" method="POST">@csrf
                                                            <input type="hidden" name="unique_id" value="{{$each_category->unique_id}}">
                                                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php $counter++ @endphp    
                                        @endforeach
                                    @else
                                        <tr><td colspan="12" class="text-center">No Data Available at this Moment</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{ $category->render("pagination::bootstrap-4") }}                            
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- push external js -->
     @push('script')  
        <script src="{{ asset('plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('js/tables.js') }}"></script>
    @endpush
@endsection
