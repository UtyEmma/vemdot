<h4>{{__($header)}}</h4>
<form action="{{$url}}" method="POST" class="row">@csrf
    <div class="form-group col-lg-8 col-md-8">
        <label for="user_type">Type</label>
        <select id="user_type" name="user_type" class="form-control" required>
            <option>{{__('Vendor')}}</option>
            <option>{{__('Logistic')}}</option>
            <option>{{__('User')}}</option>
        </select>
    </div>
    <div class="form-group col-lg-4 col-md-4">
        <button class="btn btn-primary mt-25" type="submit">Proceed</button>
    </div>
</form>