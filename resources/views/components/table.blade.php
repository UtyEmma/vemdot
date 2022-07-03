<div class="card">
    <div class="card-header">
        <h3>{{ __($header)}}</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{$td}}
                </tr>
            </thead>
            <tbody>
                {{$slot}}
            </tbody>
        </table>
    </div>
    <div class="card-footer text-right">
        {{$pagination}}                          
    </div>
</div>