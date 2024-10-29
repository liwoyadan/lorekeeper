@if (View::exists($viewName . '._sidebar'))
    <div class="dropdown breadcrumb-menu">
        <button class="btn btn-sm d-none d-lg-block dropdown-toggle mr-2" type="button" data-toggle="dropdown" aria-expanded="false" data-display="static">
            <i class="fas fa-bars mr-1" aria-hidden="true"></i>
            Menu
        </button>
        <div class="dropdown-menu">
            @includeIf($viewName . '._sidebar')
        </div>
    </div>
@endif
