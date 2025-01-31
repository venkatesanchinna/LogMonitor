

 <nav class="navbar navbar-expand-md navbar-dark sticky-top bg-dark p-0">
        <a href="{{ route('log-monitor::dashboard') }}" class="navbar-brand mr-0">
            <i class="fa fa-fw fa-book"></i>@lang('log-monitor::log_monitor.package_name')
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item {{ Route::is('log-monitor::dashboard') ? 'active' : '' }}">
                    <a href="{{ route('log-monitor::dashboard', ['log_folder_name' => @$log_folder_name]) }}" class="nav-link">
                        <i class="fa fa-dashboard"></i> @lang('log-monitor::log_monitor.dashboard')
                    </a>
                </li>
                <li class="nav-item {{ Route::is('log-monitor::logs.list') ? 'active' : '' }}">
                    <a href="{{ route('log-monitor::logs.list', ['log_folder_name' => @$log_folder_name]) }}" class="nav-link">
                        <i class="fa fa-archive"></i> @lang('log-monitor::log_monitor.title')
                    </a>
                </li>
            </ul>
        </div>
    </nav>
