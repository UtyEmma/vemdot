@extends('layouts.main')
@section('title', 'Reply Ticket')
@section('content')
@php  $segment = request()->segment(3); @endphp
<!-- push external head elements to head -->
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jvectormap/jquery-jvectormap.css') }}">
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Reply Ticket">
        <li class="breadcrumb-item"><a href="{{url('tickets/interface')}}">Tickets</a></li>
    </x-pageHeader>
    
   
    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Recent Ticket</h3>
                    <div class="card-header-right">
                        <ul class="list-unstyled card-option">
                            <li><i class="ik ik-chevron-left action-toggle"></i></li>
                            <li><i class="ik ik-minus minimize-card"></i></li>
                            <li><i class="ik ik-x close-card"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chat-box scrollable card-300" >
                    <ul class="chat-list">
                        @forelse($tickets as $ticket)
                            @if($ticket->send_status == 'user')
                                <li class="chat-item">
                                    <div class="chat-img"><img src="{{$ticket->user->avatar ?? asset('default.png')}}" alt="user"></div>
                                    <div class="chat-content">
                                        <h6 class="font-medium">{{ __($ticket->user->business_name ?? $ticket->user->name )}}</h6>
                                        <div class="box bg-light-info">{{$ticket->message}}</div>
                                    </div>
                                    <div class="chat-time">{{ __($ticket->created_at->diffForHumans())}}</div>
                                </li>
                            @else
                                <li class="odd chat-item">
                                    <div class="chat-content">
                                        <div class="box bg-light-inverse">{{$ticket->message}}</div>
                                        <br>
                                    </div>
                                </li>
                            @endif
                        @empty
                            <li class="chat-item">
                                <div class="chat-content">
                                    <h4 class="box bg-light-inverse">No ticket started yet.</h4>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer chat-footer">
                    <form action="{{url('tickets/reply')}}" method="POST">@csrf 
                        <div class="input-wrap">
                            <input type="text" placeholder="Type and enter" class="form-control" name="message" required>
                            <input type="hidden" name="unique_id" value="{{$unique_id}}">
                        </div>
                        <button type="submit" class="btn btn-icon btn-theme"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- push external js -->
@push('script')
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

    <script src="{{ asset('js/widgets.js') }}"></script>
@endpush

@endsection
