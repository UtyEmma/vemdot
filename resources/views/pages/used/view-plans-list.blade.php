@extends('layouts.main')
@section('title', 'View Plans')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="View Plans" />

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
                                            <img src="{{$each_plan->thumbnail ?? asset('default.png')}}" class="table-user-thumb" alt="{{ $each_plan->name }}">
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
                            <x-modal call="deletePlan{{$each_plan->unique_id}}" header="Delete Plan" message="You are about to delete {{$each_plan->name}} plan, Note! This action won't go through if this plan is in use.">
                                <form action="{{url('delete/plan')}}" method="POST">@csrf
                                    <input type="hidden" name="unique_id" value="{{$each_plan->unique_id}}">
                                    <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                                </form>
                            </x-modal>
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
