@if(session()->has('message'))
<div class="alert alert-success">
    <button class="close" data-dismiss="alert" type="button">
        <i class="fa fa-close">
        </i>
    </button>
    {{ session()->get('message') }}
</div>
@endif
@if(session()->has('error'))
<div class="alert alert-warning">
    <button class="close" data-dismiss="alert" type="button">
        <i class="fa fa-close">
        </i>
    </button>
    {{ session()->get('error') }}
</div>
@endif
@if(session()->has('success'))
<div class="alert alert-success">
    <button class="close" data-dismiss="alert" type="button">
        <i class="fa fa-close">
        </i>
    </button>
    {{ session()->get('success') }}
</div>
@endif
