<!DOCTYPE doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8"/>
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="{{ $packageName }}" name="description"/>
        <meta content="{{ $authorName }}" name="author"/>
        <title>
            {{ $packageName }} - {{ trans('log-monitor::log_monitor.created_by') }} {{ $authorName }}
        </title>
        @include("log-monitor::{$theme}.styles")
        @yield('styles')
        <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>
        {{-- Navbar --}}
        @include("log-monitor::{$theme}.header")
        {{-- Main container --}}
        <main class="container-fluid">
            @yield('body')
        </main>
        {{-- Footer --}}
        @include("log-monitor::{$theme}.footer")

        {{-- Scripts --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js">
        </script>
        @yield('modals')
    @yield('scripts')
    @yield('bottom-scripts')
    </body>
</html>
