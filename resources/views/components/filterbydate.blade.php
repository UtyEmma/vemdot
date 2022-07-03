<h4>{{__('Filter By Date')}}</h4>
<form action="{{$url}}" method="POST" class="row">@csrf
    <div class="form-group col-lg-4 col-md-4">
        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" name="start_date" class="form-control" required>
    </div>
    <div class="form-group col-lg-4 col-md-4">
        <label for="end_date">End Date</label>
        <input type="date" id="end_date" name="end_date" class="form-control" required>
    </div>
    <div class="form-group col-lg-4 col-md-4">
        <button class="btn btn-primary mt-25" type="submit">{{$submit}}</button>
    </div>
</form>