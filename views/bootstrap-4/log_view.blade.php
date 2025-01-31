
    <div class="page-header mb-4">
       
        <h1>@lang('log-monitor::log_monitor.title') [{{ @$log_file_results['log_folder_name'] ? str_replace("_", " ", $log_file_results['log_folder_name']).' : '.$log_file_results['filename']   : $log_file_results['filename'] }}]</h1>
    </div>

    <ul class="nav nav-tabs mb-2">
        @if(isset($log_file_results['log_folder_log_files']) && !empty($log_file_results['log_folder_log_files']))
            @foreach($log_file_results['log_folder_log_files'] as $log_file => $log_name)
                <li class="nav-item">
                    <a href="{{ route('log-monitor::logs.show', [$log_file, 'log_folder_name' => @$log_folder_name]) }}" class="nav-link {{ $log->date ==  $log_file ? 'active' : '' }}">
                        {{ $log_name }}
                    </a>
                </li>
            @endforeach
        @endif
        
    </ul>
    <div class="row">
        <div class="col-lg-2">
            {{-- Log Menu --}}
            <div class="card mb-4">
                <div class="card-header"><i class="fa fa-fw fa-flag"></i> @lang('log-monitor::log_monitor.levels')</div>
                <div class="list-group list-group-flush log-menu">
                    @foreach($log->menu() as $levelKey => $item)
                        @if ($item['count'] === 0)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                                <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                <span class="badge empty">{{ $item['count'] }}</span>
                            </a>
                        @else
                            <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center level-{{ $levelKey }}{{ $level === $levelKey ? ' active' : ''}}">
                                <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                <span class="badge badge-level-{{ $levelKey }}">{{ $item['count'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            {{-- Log Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    @lang('log-monitor::log_monitor.log_info') :
                    <div class="group-btns pull-right">
                        <a href="{{ route('log-monitor::logs.download', [$log->date]) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> @lang('log-monitor::log_monitor.download')
                        </a>
                        <a href="#delete-log-modal-view" class="btn btn-sm btn-danger delete_log" data-toggle="modal" data-is_tenant="0">
                            <i class="fa fa-trash-o"></i> @lang('log-monitor::log_monitor.delete')
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed mb-0">
                        <tbody>
                            <tr>
                                <td>@lang('log-monitor::log_monitor.file_path') :</td>
                                <td colspan="7">{{ $log->getPath() }}</td>
                            </tr>
                            <tr>
                                <td>@lang('log-monitor::log_monitor.log_entries') :</td>
                                <td>
                                    <span class="badge badge-primary">{{ $entries->total() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.size') :</td>
                                <td>
                                    <span class="badge badge-primary">{{ $log->size() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.created_at') :</td>
                                <td>
                                    <span class="badge badge-primary">{{ $log->createdAt() }}</span>
                                </td>
                                <td>@lang('log-monitor::log_monitor.updated_at') :</td>
                                <td>
                                    <span class="badge badge-primary">{{ $log->updatedAt() }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{-- Search --}}
                    <form action="{{ route('log-monitor::logs.search', [$log->date, $level, 'log_folder_name' => @$log_folder_name]) }}" method="GET">
                        <div class="form-group">
                            <div class="input-group">
                                <input id="query" name="query" class="form-control" value="{{ $query }}" placeholder="@lang('log-monitor::log_monitor.type_here_search')">
                                <div class="input-group-append">
                                    @unless (is_null($query))
                                        <a href="{{ route('log-monitor::logs.show', [$log->date, 'log_folder_name' => @$log_folder_name]) }}" class="btn btn-secondary">
                                            (@lang('log-monitor::log_monitor.count_results', ['count' => $entries->count()])) <i class="fa fa-fw fa-times"></i>
                                        </a>
                                    @endunless
                                    <button id="search-btn" class="btn btn-primary">
                                        <span class="fa fa-fw fa-search"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Log Entries --}}
             
            <div class="card mb-4">
                @if ($entries->hasPages())
                    <div class="card-header" id="log_view_div">
                        {!! $entries->appends(compact('query'))->render() !!}
                        <span class="badge badge-info float-right">
                            {{ __('log-monitor::log_monitor.pagination_text', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()]) }}
                        </span>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="entries" class="table mb-0">
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
                                        <span class="badge badge-env">{{ $entry->env }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-level-{{ $entry->level }}">
                                            {!! $entry->level() !!}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $entry->datetime->format('d.m.Y H:i:s') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $entry->header }}
                                    </td>
                                    <td class="text-right">
                                        @if ($entry->hasStack())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse"
                                           href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> @lang('log-monitor::log_monitor.stack')
                                        </a>
                                        @endif

                                        @if ($entry->hasContext())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse"
                                           href="#log-context-{{ $key }}" aria-expanded="false" aria-controls="log-context-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> @lang('log-monitor::log_monitor.context')
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @if ($entry->hasStack() || $entry->hasContext())
                                    <tr>
                                        <td colspan="5" class="stack py-0">
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
                                        <span class="badge badge-secondary">@lang('log-monitor::log_monitor.no_logs_found')</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {!! $entries->appends(compact('query'))->render() !!}
        </div>
    </div>
