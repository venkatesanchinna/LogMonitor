<!DOCTYPE doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8"/>
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
        <meta content="{{ $packageName }}" name="description"/>
        <meta content="{{ $authorName }}" name="author"/>
        <title>
            {{ $packageName }} - {{ trans('log-monitor::log_monitor.created_by') }} {{ $authorName }}
        </title>
        @yield('styles')
        @include("log-monitor::{$theme}.styles")
        <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>
        @include("log-monitor::{$theme}.header")
        <div class="container-fluid">
            <main class="pt-3" role="main">
                @yield('body')
            </main>
        </div>
        @include("log-monitor::{$theme}.footer")
        {{-- Scripts --}}
        <script crossorigin="anonymous" src="https://code.jquery.com/jquery-3.2.1.min.js">
        </script>
        @yield('modals')
        @yield('scripts')
        @yield('bottom-scripts')
    </body>
</html>
