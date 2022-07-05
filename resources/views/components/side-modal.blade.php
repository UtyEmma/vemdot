<div class="modal fade edit-layout-modal" id="{{$call}}" tabindex="-1" role="dialog" aria-labelledby="{{$call}}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{$call}}Label">{{ __($header)}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                {{$slot}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
                {{$other}}
            </div>
        </div>
    </div>
</div>