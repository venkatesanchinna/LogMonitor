
    <div class="page-header mb-4">
        <h1>@lang('log-monitor::log_monitor.title')</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                    <th scope="col" class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'date')
                            <span class="badge badge-info">{{ trans('log-monitor::log_monitor.log_title') }}</span>
                        @else
                            <span class="badge badge-level-{{ $key }}">
                                {{ log_styler()->icon($key) }} {{ $header }}
                            </span>
                        @endif
                    </th>
                    @endforeach
                    <th scope="col" class="text-right">@lang('log-monitor::log_monitor.actions')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $date => $row)
                    <tr>
                        @foreach($row as $key => $value)
                            <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                @if ($key == 'date')
                                @php
                                    $explode = explode("_", $value);
                                    array_pop($explode);
                                    $log_file_name       = $value == 'laravel.log' ? $value : ucfirst(implode("_", $explode));
                                    if(isset($log_file_results['log_folder_name']) && !empty($log_file_results['log_folder_name'])) {

                                        $log_file_name = str_replace(strtolower($log_file_results['log_folder_name']).'_',"", $value);
                                        $log_file_name = $log_file_name ? $log_file_name : 'laravel.log';
                                    }

                                @endphp
                                <a href="{{ route('log-monitor::logs.show', [$date, 'log_folder_name' => @$log_folder_name]) }}" class="badge badge-primary">
                                    {{ ucfirst($log_file_name) }}
                                </a>
                                    
                                @elseif ($value == 0)
                                    <span class="badge empty">{{ $value }}</span>
                                @else
                                    <a href="{{ route('log-monitor::logs.filter', [$date, $key, 'log_folder_name' => @$log_folder_name]) }}">
                                        <span class="badge badge-level-{{ $key }}">{{ $value }}</span>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-right">
                            <a href="{{ route('log-monitor::logs.show', [$date, 'log_folder_name' => @$log_folder_name]) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-search"></i>
                            </a>
                            <a href="{{ route('log-monitor::logs.download', [$date]) }}" class="btn btn-sm btn-success">
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-log-date="{{ $date }}" data-log_confirm_message="{{ __('log-monitor::log_monitor.delete_confirm_msg') }}">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            <span class="badge badge-secondary">@lang('log-monitor::log_monitor.no_logs_found')</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $rows->render() }}