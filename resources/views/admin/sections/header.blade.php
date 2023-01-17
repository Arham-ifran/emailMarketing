<nav class="navbar navbar-default navbar-fixed-top admin-pannel-styling">
    <div class="brand logo-sty">
        <a href="{{url('/')}}">
            <img src="{{ asset('images/admin-logo.png') }}" alt="logo" class="img-responsive logo">
        </a>
        <div id="tour-fullwidth" class="navbar-btn-togl">
            <button type="button" class="btn-toggle-fullwidth"><i class="ti-arrow-circle-left"></i></button>
        </div>
    </div>
    <div class="right-menu-bar">
        <div id="navbar-menu" class="navbar-menu head-sec-des">
            <div class="heading hidden-xs">
                <h1 class="page-title">@yield('title')</h1>
                <p class="page-subtitle">@yield('sub-title')</p>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle user-status-div" data-toggle="dropdown">
                        <div class="user-name-sty">
                            <span>{{ Auth::user()->name }}</span>
                            <!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="13.2px" height="8.399px" viewBox="0 0 13.2 8.399" enable-background="new 0 0 13.2 8.399" xml:space="preserve">
							<polygon fill="#A4AFB7" points="6.601,8.399 0,1.729 1.711,0 6.601,4.94 11.489,0 13.2,1.729 "></polygon>
							</svg> -->
                        </div>
                        <div class="user-img-sty">
                            <img src="{{checkImage(asset('storage/admins/profile-images/' . Auth::user()->profile_image),'user.png')}}" alt="Avatar">
                        </div>

                    </a>
                    <ul class="dropdown-menu logged-user-menu">
                        <!-- <li>
                            <a href="{{ url('/') }}">
                                <i class="ti-home"></i> <span>Home</span>
                            </a>
                        </li> -->
                        @if(have_right(9))<li><a href="{{ route('admin.profile') }}"><i class="ti-user"></i> <span>My
                                    Profile</span></a></li>@endif
                        <li>
                            <a href="{{ route('admin.auth.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="ti-power-off"></i> <span>Logout</span>
                            </a>
                        </li>

                        <form id="logout-form" action="{{ route('admin.auth.logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </ul>
                </li>
                <li class="xs-visi-btn">
                    <button class="navicon navbar-toggler btn-toggle-fullwidth" type="button" id="tour-fullwidth">
                        <div class="navicon__holder">
                            <div style="display:inline-block">
                                <div class="navicon__line"></div>
                                <div class="navicon__line"></div>
                                <div class="navicon__line"></div>
                            </div>
                        </div>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>