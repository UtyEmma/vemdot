@extends('layouts.main')
@section('title', 'Riders')
@section('content')

<!-- push external head elements to head -->
@push('head')

@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Riders">
        <li class="breadcrumb-item"><a href="{{url('logistic/interface')}}">Logistic</a></li>
    </x-pageHeader>
    
    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <x-filterbydate url="{{url('riders/interface/by/date')}}" submit="Fetch Riders" />
            </div>
        </div>
        <div class="col-md-12">
            {{-- table section --}}
            <x-table header="Riders">
                {{-- table header section --}}
                <x-slot name="td">
                    <th class="text-center">{{ __('S/N')}}</th>
                    <th class="text-center">{{ __('Bike Image')}}</th>
                    <th class="text-center">{{ __('Rider Name')}}</th>
                    <th class="text-center">{{ __('Bike Number')}}</th>
                    <th class="text-center">{{ __('Phone')}}</th>
                    <th class="text-center">{{ __('Avaliability')}}</th>
                    <th class="text-center">{{ __('Date Added')}}</th>
                    <th class="text-right">{{ __('Action')}}</th>
                </x-slot>
                {{-- table body section --}}
                @php $count = 0; @endphp
                @forelse ($rider as $each_rider)
                    <tr>
                        <td class="text-center">{{ __(++$count)}}</td>
                        <td class="text-center">
                            <img src="{{$each_rider->logo ?? asset('default.png')}}" class="table-user-thumb" alt="">
                        </td>
                        <td class="text-center"><a href="/users/{{ __($each_rider->unique_id)}}">{{ __($each_rider->name )}}</a></td>
                        <td class="text-center">{{ __($each_rider->id_number)}}</td>
                        <td class="text-center">{{ __($each_rider->phone)}}</td>
                        <td class="text-center">
                            <span class="badge light badge-{{ $each_rider->availability == 'no' ? 'warning':'success' }} ">
                                {{ $each_rider->availability == 'no' ? 'NO' : 'Yes' }}
                            </span>
                        </td>
                        <td class="text-center">{{ __($each_rider->created_at->diffForHumans())}}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="/users/{{ __($each_rider->unique_id)}}"><i class="ik ik-edit-2"></i></a>
                                <a data-toggle="modal" data-target="#deleteRider{{$each_rider->unique_id}}"><i class="ik ik-trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    <x-modal call="deleteRider{{$each_rider->unique_id}}" header="Delete Rider" message="Do you really want to delete this rider ?">
                        <form action="{{url('delete/user')}}" method="POST">@csrf
                            <input type="hidden" name="unique_id" value="{{$each_rider->unique_id}}">
                            <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                @endforelse
                {{-- table pagination section --}}
                <x-slot name="pagination">
                    {{ $rider->render("pagination::bootstrap-4") }} 
                </x-slot>
            </x-table>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')

@endpush

@endsection
