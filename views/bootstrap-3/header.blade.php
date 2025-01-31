<nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">@lang('Toggle navigation')</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{ route('log-monitor::dashboard') }}" class="navbar-brand">
                    <i class="fa fa-fw fa-book"></i> @lang('log-monitor::log_monitor.package_name')
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav">
                    <li class="{{ Route::is('log-monitor::dashboard') ? 'active' : '' }}">
                        <a href="{{ route('log-monitor::dashboard', ['log_folder_name' => @$log_folder_name]) }}">
                            <i class="fa fa-dashboard"></i> @lang('log-monitor::log_monitor.dashboard')
                        </a>
                    </li>
                    <li class="{{ Route::is('log-monitor::logs.list') ? 'active' : '' }}">
                        <a href="{{ route('log-monitor::logs.list', ['log_folder_name' => @$log_folder_name]) }}">
                            <i class="fa-light fa-archive"></i> @lang('log-monitor::log_monitor.title')
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>