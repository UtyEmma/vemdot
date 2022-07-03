@extends('layouts.main')
@section('title', 'View Categories')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="View Categories" />
        
        <div class="row">
            <div class="col-md-12">
                {{-- table section --}}
                <x-table header="View Categories">
                    {{-- table header section --}}
                    <x-slot name="td">
                        <th class="text-center">S/N</th>
                        <th class="text-center">{{ __('Category Name')}}</th>
                        <th class="text-center">{{ __('Thumbnail')}}</th>
                        <th class="text-center">{{ __('Status')}}</th>
                        <th class="text-center">{{ __('Date')}}</th>
                        <th class="text-right">{{ __('Action')}}</th>
                    </x-slot>
                    {{-- table body section --}}
                    @php $count = 0; @endphp
                    @forelse ($category as $each_category)
                        <tr>
                            <td class="text-center" scope="row">{{ __(++$count)}}</td>
                            <td class="text-center">{{ $each_category->name }}</td>
                            <td class="text-center">
                                <img src="{{$each_category->thumbnail ?? asset('default.png')}}" class="table-user-thumb" alt="{{ $each_category->name }}">
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
                        <x-modal call="deleteCategory{{$each_category->unique_id}}" header="Delete Category" message=" You are about to delete {{$each_category->name}} category, Note! This action won't go through if this category has been used to add a Meal.">
                            <form action="{{url('delete/category')}}" method="POST">@csrf
                                <input type="hidden" name="unique_id" value="{{$each_category->unique_id}}">
                                <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                            </form>
                        </x-modal>
                    @empty
                        <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                    @endforelse
                    {{-- table pagination section --}}
                    <x-slot name="pagination">
                        {{ $category->render("pagination::bootstrap-4") }} 
                    </x-slot>
                </x-table>
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
