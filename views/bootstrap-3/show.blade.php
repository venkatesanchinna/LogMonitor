@extends('log-monitor::bootstrap-3._master')

@section('body')
@include("log-monitor::{$theme}.messages")

@include("log-monitor::{$theme}.log_view")
    
@endsection

@section('modals')
    {{-- DELETE MODAL --}}
    <div id="delete-log-modal-view" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form-view" action="{{ route('log-monitor::logs.delete') }}" method="POST" redirect="{{ route('log-monitor::logs.list') }}">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="date" value="{{ $log->date }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">@lang('log-monitor::log_monitor.delete_title')</h4>
                    </div>
                    <div class="modal-body">
                        <p>@lang('log-monitor::log_monitor.delete_confirm_msg', ['date' => $log->date])</p>
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="{{ asset('vendor/log-monitor/assets/js/logs.js') }}"></script>

    <script>
        $(function () {

            @unless (empty(log_styler()->toHighlight()))
                @php
                    $htmlHighlight = version_compare(PHP_VERSION, '7.4.0') >= 0
                        ? join('|', log_styler()->toHighlight())
                        : join(log_styler()->toHighlight(), '|');
                @endphp
                $('.stack-content').each(function() {
                    var $this = $(this);
                    var html = $this.html().trim()
                        .replace(/({!! $htmlHighlight !!})/gm, '<strong>$1</strong>');

                    $this.html(html);
                });
            @endunless
        });
    </script>
@endsection
