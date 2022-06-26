<div class="card-body">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="example-name">{{ __('Full Name')}}</label>
            <input type="text" placeholder="Johnathan Doe" class="form-control" name="example-name" id="example-name">
        </div>
        <div class="form-group">
            <label for="example-email">{{ __('Email')}}</label>
            <input type="email" placeholder="johnathan@admin.com" class="form-control" name="example-email" id="example-email">
        </div>
        <div class="form-group">
            <label for="example-password">{{ __('Password')}}</label>
            <input type="password" value="password" class="form-control" name="example-password" id="example-password">
        </div>
        <div class="form-group">
            <label for="example-phone">{{ __('Phone No')}}</label>
            <input type="text" placeholder="123 456 7890" id="example-phone" name="example-phone" class="form-control">
        </div>
        <div class="form-group">
            <label for="example-message">{{ __('Message')}}</label>
            <textarea name="example-message" name="example-message" rows="5" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="example-country">{{ __('Select Country')}}</label>
            <select name="example-message" id="example-message" class="form-control">
                <option>{{ __('London')}}</option>
                <option>{{ __('India')}}</option>
                <option>{{ __('Usa')}}</option>
                <option>{{ __('Canada')}}</option>
                <option>{{ __('Thailand')}}</option>
            </select>
        </div>
        <button class="btn btn-success" type="submit">Update Profile</button>
    </form>
</div>
