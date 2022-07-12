@extends('layouts.main')
@section('title', 'Meals History')
@section('content')
@php  $segment = request()->segment(3); @endphp
<!-- push external head elements to head -->
@push('head')
    
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Meals History" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('meals/interface/by/date')}}" submit="Fetch Meals" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('meals/interface/by/type')}}" header="Filter Meal">
                @foreach($categories as $category)
                    <option value="{{$category->unique_id}}">{{__($category->name)}}</option>
                @endforeach
            </x-filterByType>
        </div>
        <div class="col-md-12">
             {{-- table section --}}
             <x-table header="Meals History">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Vendor Name')}}</th>
                    <th class="text-center">{{ __('Category')}}</th>
                    <th class="text-center">{{ __('Meal Name')}}</th>
                    <th class="text-center">{{ __('Thumbnail')}}</th>
                    <th class="text-center">{{ __('Amount')}}</th>
                    <th class="text-center">{{ __('Discount')}}</th>
                    <th class="text-center">{{ __('Tax')}}</th>
                    <th class="text-center">{{ __('Ratings')}}</th>
                    <th class="text-center">{{ __('Ads Status')}}</th>
                    <th class="text-center">{{ __('Date')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($meals as $meal)
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center"><a href="/users/{{ __($meal->vendor->unique_id)}}">{{ __($meal->vendor->business_name ?? $meal->vendor->name )}}</a></td>
                        <td class="text-center">{{ __($meal->categories->name)}}</td>
                        <td class="text-center">{{ __($meal->name)}}</td>
                        <td class="text-center">
                            <img src="{{$meal->thumbnail ?? asset('default.png')}}" width="80" alt="{{env('APP_NAME')}}">
                        </td>
                        <td class="text-center">{{ __(number_format($meal->amount))}} {{auth()->user()->currency()}}</td>
                        <td class="text-center">{{ __(number_format($meal->discount))}}</td>
                        <td class="text-center">{{ __($meal->tax)}}</td>
                        <td class="text-center">{{ __($meal->rating)}}</td>
                        <td class="text-center"><span class="badge light badge-{{$meal->promoted == 'yes' ? 'primary' : 'danger'}} ">
                            {{$meal->promoted}}
                        </span></td>
                        <td class="text-center">{{ __($meal->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a data-toggle="modal" data-target="#deleteMeal{{$meal->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    {{-- modal section --}}
                    <x-modal call="deleteMeal{{$meal->unique_id}}" header="Delete Meal" message="Do you really want to delete this meal ?">
                        <form action="{{url('meals/delete')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$meal->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $meals->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')
    
@endpush

@endsection
