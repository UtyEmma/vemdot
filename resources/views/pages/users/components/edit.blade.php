<div class="card-body">
    <div class="row">
        <div class="col-md-3 col-6"> <strong>{{ __('Full Name')}}</strong>
            <br>
            <p class="text-muted">{{$user->name}}</p>
        </div>
        <div class="col-md-3 col-6"> <strong>{{ __('Mobile')}}</strong>
            <br>
            <p class="text-muted">{{$user->phone}}</p>
        </div>
        <div class="col-md-3 col-6"> <strong>{{ __('Email')}}</strong>
            <br>
            <p class="text-muted">{{$user->email}}</p>
        </div>
        <div class="col-md-3 col-6"> <strong>{{ __('Country')}}</strong>
            <br>
            <p class="text-muted">{{$user->countries->name}}</p>
        </div>
    </div>
    <hr>
    <p class="mt-30">{{$user->city}}, {{$user->state}}, {{$user->address}}, {{$user->countries->name}} </p>
    @if(in_array($user->userRole->name, ['Vendor', 'Logistic', 'Rider', 'Super Admin']))
        <h4 class="mt-30">{{ __('Skill Set')}}</h4>
        <hr>
        <h6 class="mt-30">{{ __('Business Name')}}</h6>
        <div class="">
           <h4>{{$user->business_name ?? 'None Provided'}}</h4>
        </div>
        <h6 class="mt-30">{{ __('Open Hours')}}</h6>
        <div class="">
           <h4>{{$user->avg_time ?? 'None Provided'}}</h4>
        </div>
        <h6 class="mt-30">{{ __('Business Address')}}</h6>
        <div class="">
           <h4>{{$user->address ?? 'None Provided'}}</h4>
        </div>
        <h6 class="mt-30">{{ __('Availability Status')}}</h6>
        <div class="">
           <h4>{{$user->availability}}</h4>
        </div>
    @endif
</div>
