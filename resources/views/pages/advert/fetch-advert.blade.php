@extends('layouts.main')
@section('title', 'View Adverts')
@section('content')

    <div class="container-fluid">
        {{-- page header section --}}
        <x-pageHeader header="View Adverts" />

        <div class="row">
            <div class="col-md-12">
                {{-- table section --}}
                <x-table header="View Adverts">
                    {{-- table header section --}}
                    <x-slot name="td">
                        <th class="text-center">{{ __('S/N')}}</th>
                        <th class="text-center">{{ __('Caption')}}</th>
                        <th class="text-center">{{ __('Banner')}}</th>
                        <th class="text-center">{{ __('Email')}}</th>
                        <th class="text-center">{{ __('Status')}}</th>
                        <th class="text-center">{{ __('Date Created')}}</th>
                        <th class="text-right">{{ __('Action')}}</th>
                    </x-slot>
                    {{-- table body section --}}
                    @php $count = 0; @endphp
                    @forelse($adverts as $advert)
                        <tr>
                            <td class="text-center">{{ __(++$count)}}</td>
                            <td class="text-center">
                                <a href="#editLayoutItem{{$advert->unique_id}}" data-toggle="modal" data-target="#editLayoutItem{{$advert->unique_id}}">{{ __($advert->caption ?? $advert->description)}}</a>
                            </td>
                            <td class="text-center">
                                <img src="{{$advert->banner ?? asset('default.png')}}" width="80" alt="{{env('APP_NAME')}}">
                            </td>
                            <td class="text-center">{{ __($advert->email ?? 'None Provided')}}</td>
                            <td class="text-center">
                                <span class="badge light badge-{{ $advert->status == 'pending' ? 'warning':'success' }} ">
                                    {{ $advert->status == 'pending' ? 'Disabled' : 'Enabled' }}
                                </span>
                            </td>
                            <td class="text-center">{{ __($advert->created_at->diffForHumans())}}</td>
                            <td class="text-center">
                                <div class="table-actions">
                                    <a href="#" data-toggle="modal" data-target="#editLayoutItem{{$advert->unique_id}}"><i class="ik ik-eye"></i></a>
                                    <a data-toggle="modal" data-target="#{{$advert->status == 'pending' ? 'enableAdvert' : 'disableAdvert'}}{{$advert->unique_id}}"><i class="ik ik-edit-2"></i></a>
                                    <a data-toggle="modal" data-target="#deleteAdvert{{$advert->unique_id}}"><i class="ik ik-trash-2"></i></a>
                                </div>
                            </td>
                        </tr>
                        <x-modal call="enableAdvert{{$advert->unique_id}}" header="Enable Advert" message="Do you really want to enable this advert ?">
                            <form action="{{url('advert/update/status')}}" method="POST">@csrf
                                <input type="hidden" name="unique_id" value="{{$advert->unique_id}}">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                            </form>
                        </x-modal>
                        <x-modal call="disableAdvert{{$advert->unique_id}}" header="Disable Advert" message="Do you really want to disable this advert ?">
                            <form action="{{url('advert/update/status')}}" method="POST">@csrf
                                <input type="hidden" name="unique_id" value="{{$advert->unique_id}}">
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                            </form>
                        </x-modal>
                        <x-modal call="deleteAdvert{{$advert->unique_id}}" header="Delete Advert" message="Do you really want to delete this advert ?">
                            <form action="{{url('advert/delete')}}" method="POST">@csrf
                                <input type="hidden" name="unique_id" value="{{$advert->unique_id}}">
                                <button type="submit" class="btn btn-primary">{{ __('Continue')}}</button>
                            </form>
                        </x-modal>
                        {{-- modal section --}}
                        <div class="modal fade edit-layout-modal" id="editLayoutItem{{$advert->unique_id}}" tabindex="-1" role="dialog" aria-labelledby="editLayoutItem{{$advert->unique_id}}Label" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editLayoutItem{{$advert->unique_id}}Label">{{ __($advert->caption ?? $advert->description)}}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <img src="{{$advert->banner ?? asset('default.png')}}" class="img-fluid" alt="{{env('APP_NAME')}}">
                                            </div>
                                        </div>
                                        <p>{{ __($advert->description)}}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAdvert{{$advert->unique_id}}">{{ __('Delete')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                    @endforelse
                    {{-- table pagination section --}}
                    <x-slot name="pagination">
                        {{ $adverts->render("pagination::bootstrap-4") }} 
                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/dist/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('js/layouts.js') }}"></script>
    @endpush
@endsection
