<h1 class="page-header">@lang('log-monitor::log_monitor.title') [{{ @$log_file_results['log_folder_name'] ? str_replace("_", " ", $log_file_results['log_folder_name']).' : '.$log_file_results['filename']   : $log_file_results['filename'] }}]</h1>

<ul class="nav nav-tabs mb-2">
        @if(isset($log_file_results['log_folder_log_files']) && !empty($log_file_results['log_folder_log_files']))
            @foreach($log_file_results['log_folder_log_files'] as $log_file => $log_name)
                <li class="nav-item {{ $log->date ==  $log_file ? 'active' : '' }}">
                    <a href="{{ route('log-monitor::logs.show', [$log_file, 'log_folder_name' => @$log_folder_name]) }}" class="nav-link {{ $log->date ==  $log_file ? 'active' : '' }}">
                        {{ $log_name }}
                    </a>
                </li>
            @endforeach
        @endif
        
    </ul>

    <div class="row">
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-fw fa-flag"></i> @lang('log-monitor::log_monitor.levels')</div>
                <ul class="list-group">
                    @foreach($log->menu() as $levelKey => $item)
                        @if ($item['count'] === 0)
                            <a href="#" class="list-group-item disabled">
                                <span class="badge">
                                    {{ $item['count'] }}
                                </span>
                                {!! $item['icon'] !!} {{ $item['name'] }}
                            </a>
                        @else
                            <a href="{{ $item['url'] }}" class="list-group-item {{ $levelKey }}">
                                <span class="badge level-{{ $levelKey }}">
                                    {{ $item['count'] }}
                                </span>
                                <span class="level level-{{ $levelKey }}">
                                    {!! $item['icon'] !!} {{ $item['name'] }}
                                </span>
                            </a>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-10">
            {{-- Log Details --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('og-monitor::log_monitor.log_info') :

                    <div class="group-btns pull-right">
                        <a href="{{ route('log-monitor::logs.download', [$log->date]) }}" class="btn btn-xs btn-success">
                            <i class="fa fa-download"></i> @lang('log-monitor::log_monitor.download')
                        </a>
                        <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-toggle="modal">
                            <i class="fa fa-trash-o"></i> @lang('log-monitor::log_monitor.delete')
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <td>@lang('log-monitor::log_monitor.file_path') :</td>
                                <td colspan="5">{{ $log->getPath() }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>@lang('log-monitor::log_monitor.log_entries') :</td>
                                <td>
                                    <span class="label label-primary">{{ $entries->total() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.size') :</td>
                                <td>
                                    <span class="label label-primary">{{ $log->size() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.created_at') :</td>
                                <td>
                                    <span class="label label-primary">{{ $log->createdAt() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.updated_at') :</td>
                                <td>
                                    <span class="label label-primary">{{ $log->updatedAt() }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    {{-- Search --}}
                    <form action="{{ route('log-monitor::logs.search', [$log->date, $level, 'log_folder_name' => @$log_folder_name]) }}" method="GET">
                        <div class="form-group">
                            <div class="input-group">
                                <input id="query" name="query" class="form-control" value="{{ $query }}" placeholder="@lang('Type here to search')">
                                <span class="input-group-btn">
                                    @unless (is_null($query))
                                        <a href="{{ route('log-monitor::logs.show', [$log->date, 'log_folder_name' => @$log_folder_name]) }}" class="btn btn-default">
                                            (@lang('log-monitor::log_monitor.count_results', ['count' => $entries->count()])) <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    @endunless
                                    <button id="search-btn" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Log Entries --}}
            <div class="panel panel-default">
                @if ($entries->hasPages())
                    <div class="panel-heading">
                        {{ $entries->appends(compact('query'))->render() }}

                        <span class="label label-info pull-right">
                            {{ __('log-monitor::log_monitor.pagination_text', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()]) }}
                        </span>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="entries" class="table table-condensed">
                        <thead>
                            <tr>
                                <th>@lang('log-monitor::log_monitor.env')</th>
                                <th style="width: 120px;">@lang('log-monitor::log_monitor.level')</th>
                                <th style="width: 65px;">@lang('log-monitor::log_monitor.time')</th>
                                <th>@lang('log-monitor::log_monitor.header')</th>
                                <th class="text-right">@lang('log-monitor::log_monitor.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $key => $entry)
                                <?php /** @var  Venkatesanchinna\LogMonitor\Entities\LogEntry  $entry */ ?>
                                <tr>
                                    <td>
                                        <span class="label label-env">{{ $entry->env }}</span>
                                    </td>
                                    <td>
                                        <span class="level level-{{ $entry->level }}">{!! $entry->level() !!}</span>
                                    </td>
                                    <td>
                                        <span class="label label-default">
                                            {{ $entry->datetime->format('d.m.Y H:i:s') }}
                                        </span>
                                    </td>
                                    <td>
                                        <p>{{ $entry->header }}</p>
                                    </td>
                                    <td class="text-right">
                                        @if ($entry->hasStack())
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse"
                                           href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> @lang('log-monitor::log_monitor.stack')
                                        </a>
                                        @endif

                                        @if ($entry->hasContext())
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse"
                                           href="#log-context-{{ $key }}" aria-expanded="false" aria-controls="log-context-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> @lang('log-monitor::log_monitor.context')
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @if ($entry->hasStack() || $entry->hasContext())
                                    <tr>
                                        <td colspan="5" class="stack">
                                            @if ($entry->hasStack())
                                            <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                                {!! $entry->stack() !!}
                                            </div>
                                            @endif

                                            @if ($entry->hasContext())
                                            <div class="stack-content collapse" id="log-context-{{ $key }}">
                                                <pre>{{ $entry->context() }}</pre>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <span class="label label-default">@lang('log-monitor::log_monitor.no_logs_found')</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($entries->hasPages())
                    <div class="panel-footer">
                        {!! $entries->appends(compact('query'))->render() !!}

                        <span class="label label-info pull-right">
                            @lang('Page :current of :last', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()])
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>