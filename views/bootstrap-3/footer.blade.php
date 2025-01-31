  {{-- Footer --}}
<footer class="main-footer">
        <div class="container">
            <p class="text-muted pull-left">
                {{ $packageName }} - <span class="label label-info">version {{ log_monitor()->version() }}</span>
            </p>
            <p class="text-muted pull-right">
                Created with <i class="fa fa-heart"></i> by {{ $authorName }}  <sup>&copy;</sup>
            </p>
        </div>
    </footer>