<div class="card-body">
    <form class="form-horizontal" method="POST" action="{{url('users/update')}}">@csrf
        <input type="hidden" name="user_id" value="{{$user->unique_id}}">
        <div class="form-group">
            <label for="name">{{ __('Full Name')}}</label>
            <input type="text" class="form-control" name="name" id="name" value="{{$user->name}}" required>
        </div>
        <div class="form-group">
            <label for="phone">{{ __('Phone')}}</label>
            <input type="text" class="form-control" name="phone" id="phone" value="{{$user->phone}}" required>
        </div>
        <div class="form-group">
            <label for="gender">{{ __('Select Gender')}}</label>
            <select name="gender" id="gender" class="form-control">
                <option {{$user->gender == 'Male' ? 'selected' : ''}}>{{ __('Male')}}</option>
                <option {{$user->gender == 'Female' ? 'selected' : ''}}>{{ __('Female')}}</option>
                <option {{$user->gender == 'Prefer Not Say' ? 'selected' : ''}}>{{ __('Prefer Not Say')}}</option>
            </select>
        </div>
        <div class="form-group">
            <label for="country">{{ __('Select Country')}}</label>
            <select name="country" id="country" class="form-control">
                @forelse($country as $each_country)
                    <option {{$each_country->unique_id == $user->country ? 'selected' : ''}} value="{{$each_country->unique_id}}">{{ __($each_country->name)}}</option>
                @empty
                    <option value="">{{ __('No Data')}}</option>
                @endforelse
            </select>
        </div>
        <div class="form-group">
            <label for="business_name">{{ __('Business Name')}}</label>
            <input type="text" class="form-control" name="business_name" id="business_name" value="{{$user->business_name}}">
        </div>
        <div class="form-group">
            <label for="city">{{ __('City')}}</label>
            <input type="text" class="form-control" name="city" id="city" value="{{$user->city}}">
        </div> 
        <div class="form-group">
            <label for="state">{{ __('State')}}</label>
            <input type="text" class="form-control" name="state" id="state" value="{{$user->state}}">
        </div>
        <div class="form-group">
            <label for="address">{{ __('Address')}}</label>
            <textarea name="address" name="address"  class="form-control">{{$user->address}}</textarea>
        </div>
        <button class="btn btn-success" type="submit">Update Profile</button>
    </form>
</div>
