<!-- Side Menu -->
<ul class="list-unstyled side-menu">

    @foreach($menus as $menu)
    <li @if (isset($menu['sub_menus']))class="dropdown"@endif>
        <a class="{{$menu['menu_style']}}" href="{{$menu['menu_href']}}">
            <span class="menu-item">{{$menu['menu_name']}}</span>
        </a>
        @if (isset($menu['sub_menus']))
        <ul class="list-unstyled menu-item">
            @foreach($menu['sub_menus'] as $subMenu)
                <li><a href="{{$subMenu['menu_href']}}">{{$subMenu['menu_name']}}</a></li>
            @endforeach
        </ul>
        @endif
    </li>
    @endforeach
    <li class="active">
        <a class="sa-side-home" href="index.html">
            <span class="menu-item">Dashboard2</span>
        </a>
    </li>
    <li>
        <a class="sa-side-typography" href="typography.html">
            <span class="menu-item">Typography</span>
        </a>
    </li>
    <li>
        <a class="sa-side-widget" href="content-widgets.html">
            <span class="menu-item">Widgets</span>
        </a>
    </li>
    <li>
        <a class="sa-side-table" href="tables.html">
            <span class="menu-item">Tables</span>
        </a>
    </li>
    <li class="dropdown">
        <a class="sa-side-form" href="">
            <span class="menu-item">Form</span>
        </a>
        <ul class="list-unstyled menu-item">
            <li><a href="form-elements.html">Basic Form Elements</a></li>
            <li><a href="form-components.html">Form Components</a></li>
            <li><a href="form-examples.html">Form Examples</a></li>
            <li><a href="form-validation.html">Form Validation</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a class="sa-side-ui" href="">
            <span class="menu-item">User Interface</span>
        </a>
        <ul class="list-unstyled menu-item">
            <li><a href="buttons.html">Buttons</a></li>
            <li><a href="labels.html">Labels</a></li>
            <li><a href="images-icons.html">Images &amp; Icons</a></li>
            <li><a href="alerts.html">Alerts</a></li>
            <li><a href="media.html">Media</a></li>
            <li><a href="components.html">Components</a></li>
            <li><a href="other-components.html">Others</a></li>
        </ul>
    </li>
    <li>
        <a class="sa-side-chart" href="charts.html">
            <span class="menu-item">Charts</span>
        </a>
    </li>
    <li>
        <a class="sa-side-folder" href="file-manager.html">
            <span class="menu-item">File Manager</span>
        </a>
    </li>
    <li>
        <a class="sa-side-calendar" href="calendar.html">
            <span class="menu-item">Calendar</span>
        </a>
    </li>
    <li class="dropdown">
        <a class="sa-side-page" href="">
            <span class="menu-item">Pages</span>
        </a>
        <ul class="list-unstyled menu-item">
            <li><a href="list-view.html">List View</a></li>
            <li><a href="profile-page.html">Profile Page</a></li>
            <li><a href="messages.html">Messages</a></li>
            <li><a href="login.html">Login</a></li>
            <li><a href="404.html">404 Error</a></li>
        </ul>
    </li>
</ul>

