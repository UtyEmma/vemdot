@extends('layouts.main')
@section('title', 'Tickets')
@section('content')
@php  $segment = request()->segment(3); @endphp
<!-- push external head elements to head -->
@push('head')
    
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Tickets" />
    
    <div class="row">
        <div class="col-md-6">
            <x-filterbydate url="{{url('tickets/interface/by/date')}}" submit="Fetch Tickets" />
        </div>
        <div class="col-md-6">
            <x-filterByType url="{{url('tickets/interface/by/type')}}" header="Filter Tickets">
                <option value="unread">{{__('Un-read')}}</option>
                <option value="read">{{__('Read')}}</option>
            </x-filterByType>
        </div>

        <div class="col-md-12">
            {{-- table section --}}
            <x-table header="Tickets">
               {{-- table header section --}}
               <x-slot name="td">
                   <th class="text-center">{{ __('S/N')}}</th>
                   <th class="text-center">{{ __('User Name')}}</th>
                   <th class="text-center">{{ __('Message')}}</th>
                   <th class="text-center">{{ __('Status')}}</th>
                   <th class="text-center">{{ __('Date')}}</th>
                   <th class="text-center">{{ __('Reply Ticket')}}</th>
                   <th class="text-center">{{ __('Mark As Read')}}</th>
               </x-slot>
               {{-- table body section --}}
               @php $count = 0; @endphp
               @forelse ($tickets as $ticket)
                   <tr>
                       <td class="text-center">{{ __(++$count)}}</td>
                       <td class="text-center"><a href="/users/{{ __($ticket->user->unique_id)}}">{{ __($ticket->user->business_name ?? $ticket->user->name )}}</a></td>
                  
                       <td class="text-center">{!! \Illuminate\Support\Str::words($ticket->message, 6,'....')  !!}</td>
                       <td class="text-center">
                            <span class="badge light badge-{{$ticket->status == 'unread' ? 'warning' : 'success'}} ">
                                {{$ticket->status }}
                            </span>
                       </td> 
                       <td class="text-center">{{ __($ticket->created_at->diffForHumans())}}</td>
                       <td class="text-center"><a href="{{url('tickets/reply-ticket', $ticket->unique_id)}}" class="btn btn-primary">Reply</a></td>
                       <td class="text-center"><a href="{{url('tickets/mark-read', $ticket->unique_id)}}" class="btn btn-danger"><i class="ik ik-edit"></i></a></td>
                   </tr>
               @empty
                   <tr><td colspan="12" class="text-center">No Data Available</td></tr>
               @endforelse
               {{-- table pagination section --}}
               <x-slot name="pagination">
                   {{ $tickets->render("pagination::bootstrap-4") }} 
               </x-slot>
           </x-table>
       </div>
    </div>
</div>

<!-- push external js -->
@push('script')
    
@endpush

@endsection
