@extends('log-monitor::bootstrap-3._master')


@section('body')
    @include("log-monitor::{$theme}.messages")
    @include("log-monitor::{$theme}.dashboard_logs")
@endsection

@section('modals')
    {{-- DELETE MODAL --}}
    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="{{ route('log-monitor::logs.delete') }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="date" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">@lang('log-monitor::log_monitor.delete_title')</h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal">@lang('log-monitor::log_monitor.cancel')</button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="@lang('log-monitor::log_monitor.loading')&hellip;">@lang('log-monitor::log_monitor.delete')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/log-monitor/assets/js/logs.js') }}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    
@endsection
