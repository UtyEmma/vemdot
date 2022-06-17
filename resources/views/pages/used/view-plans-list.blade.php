@extends('layouts.main')
@section('title', 'View Plans')
@section('content')

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-edit bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('View Plans')}}</h5>
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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('View Plans')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="pricing">
            <div class="container">
                <div class="row">
                    @if (count($plans) > 0)
                        @php $counter = 1; @endphp
                        @foreach ($plans as $each_plan)
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <center>
                                            <img src="{{ ($each_plan->thumbnail == 'default.png') ? asset('default.png') : $each_plan->thumbnail }}" class="table-user-thumb" alt="{{ $each_plan->name }}">
                                        </center>
                                        <hr>
                                        <h1 class=" text-muted text-uppercase text-center">{{ $each_plan->name }}</h1>
                                        <h6 class="card-price text-center">${{ number_format($each_plan->amount) }}</h6>
                                        <ul class="fa-ul">
                                            <li>
                                                <span class="fa-li"><i class="fas fa-check"></i></span>
                                                <strong>Duration: </strong> {{ $each_plan->duration }} Days
                                            </li>
                                            <li>
                                                <span class="fa-li"><i class="fas fa-check"></i></span>
                                                <strong>Items: </strong> {{ $each_plan->items }}
                                            </li>
                                            <li>
                                                <span class="fa-li"><i class="fas fa-check"></i></span>
                                                <strong>Date: </strong> {{ $each_plan->created_at->diffForHumans() }}
                                            </li>
                                        </ul>
                                        <a href="{{url('edit/plan', $each_plan->unique_id )}}" class="btn btn-block btn-primary text-uppercase">Edit</a>
                                        <a data-toggle="modal" data-target="#deletePlan{{$each_plan->unique_id}}" class="btn btn-block btn-danger text-uppercase">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="deletePlan{{$each_plan->unique_id}}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="demoModalLabel">{{ __('Plan Delete')}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            You are about to delete {{$each_plan->name}} plan, <br> (<strong>Note!</strong> This action won't go through if this plan is in use.)
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
                                        <form action="{{url('delete/plan')}}" method="POST">@csrf
                                                <input type="hidden" name="unique_id" value="{{$each_plan->unique_id}}">
                                                <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $counter++ @endphp    
                        @endforeach
                    @else
                        <div class="col-md-12"><p class="text-center">No Data Available at this Moment</p></div>
                    @endif
                    <div class="col-md-12 text-right">
                        {{ $plans->render("pagination::bootstrap-4") }}                            
                    </div>
                </div>
            </div>
        </section>
    </div>

     <!-- push external js -->
     @push('script')  
        <script src="{{ asset('plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('js/tables.js') }}"></script>
    @endpush
@endsection
