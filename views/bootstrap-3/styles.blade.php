  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700|Source+Sans+Pro:400,600' rel='stylesheet' type='text/css'>
    <link href="{{ asset('vendor/log-monitor/assets/css/'.$theme.'_log.css') }}" rel="stylesheet">
<style>
        

        .badge.level-all, .level.level-all, .info-box.level-all {
            background-color: {{ log_styler()->color('all') }};
        }

        .badge.level-emergency, .level.level-emergency, .info-box.level-emergency {
            background-color: {{ log_styler()->color('emergency') }};
        }

        .badge.level-alert, .level.level-alert, .info-box.level-alert  {
            background-color: {{ log_styler()->color('alert') }};
        }

        .badge.level-critical, .level.level-critical, .info-box.level-critical {
            background-color: {{ log_styler()->color('critical') }};
        }

        .badge.level-error, .level.level-error, .info-box.level-error {
            background-color: {{ log_styler()->color('error') }};
        }

        .badge.level-warning, .level.level-warning, .info-box.level-warning {
            background-color: {{ log_styler()->color('warning') }};
        }

        .badge.level-notice, .level.level-notice, .info-box.level-notice {
            background-color: {{ log_styler()->color('notice') }};
        }

        .badge.level-info, .level.level-info, .info-box.level-info {
            background-color: {{ log_styler()->color('info') }};
        }

        .badge.level-debug, .level.level-debug, .info-box.level-debug {
            background-color: {{ log_styler()->color('debug') }};
        }

        .badge.level-empty, .level.level-empty {
            background-color: {{ log_styler()->color('empty') }};
        }

       
    </style>