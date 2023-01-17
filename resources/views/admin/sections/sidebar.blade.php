<?php
$segment_2 = Request::segment(2);
$segment_3 = Request::segment(3);
?>

<div id="sidebar-nav" class="sidebar ad-pannel-sdbar-sty">
    <nav>
        <ul class="nav" id="sidebar-nav-menu">
            <li>
                <a href="{{route('admin.dashboard')}}" class="{{($segment_2 == 'dashboard') ? 'active' : ''}}">
                    <i class="fa fa-pie-chart"></i><span class="title">Dashboard</span>
                </a>
            </li>
            <?php
            if ($segment_2 == 'roles' || $segment_2 == 'admins' || $segment_2 == 'users' || $segment_2 == 'lawful-interception') {
                $users_active_class = 'active';
                $users_aria_expanded = 'true';
                $users_div_height = '';
                $users_div_collapse_class = 'collapse in';
            } else {
                $users_active_class = 'collapsed';
                $users_aria_expanded = 'false';
                $users_div_height = 'height: 0px';
                $users_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(1) || have_right(5) || have_right(11))
            <li class="panel">
                <a href="#users" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $users_active_class }} drop-menu-links" aria-expanded="{{ $users_aria_expanded }}">
                    <i class="fa fa-users"></i><span class="title">Users</span>
                </a>

                <div id="users" class="{{ $users_div_collapse_class }}" aria-expanded="{{ $users_aria_expanded }}" style="{{ $users_div_height }}">
                    <ul class="submenu">
                        @if(have_right(1))
                        <li>
                            <a href="{{ url('admin/roles') }}" class="{{($segment_2 == 'roles') ? 'active' : ''}}">
                                <i class="fa fa-user-secret"></i><span class="title">Roles</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(5))
                        <li>
                            <a href="{{ url('admin/admins') }}" class="{{($segment_2 == 'admins') ? 'active' : ''}}">
                                <i class="fa fa-user"></i> <span class="title">Admin Users</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(11))
                        <li>
                            <a href="{{ url('admin/users') }}" class="{{($segment_2 == 'users') ? 'active' : ''}}">
                                <i class="fa fa-users"></i><span class="title">Users</span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ url('admin/lawful-interception') }}" class="{{($segment_2 == 'lawful-interception') ? 'active' : ''}}">
                                <i class="fa fa-user-secret"></i><span class="title">Lawful Interception</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            @if(have_right(61))
            <li>
                <a href="{{ url('admin/countries') }}" class="{{($segment_2 == 'countries') ? 'active' : ''}}">
                    <i class="fa fa-globe"></i><span class="title">Countries</span>
                </a>
            </li>
            @endif

            <?php
            if ($segment_2 == 'email-campaigns' || $segment_2 == 'sms-campaigns' || $segment_2 == 'split-campaigns') {
                $campaigns_active_class = 'active';
                $campaigns_aria_expanded = 'true';
                $campaigns_div_height = '';
                $campaigns_div_collapse_class = 'collapse in';
            } else {
                $campaigns_active_class = 'collapsed';
                $campaigns_aria_expanded = 'false';
                $campaigns_div_height = 'height: 0px';
                $campaigns_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(33) || have_right(35) || have_right(97))
            <li class="panel">
                <a href="#campaigns" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $campaigns_active_class }} drop-menu-links" aria-expanded="{{ $campaigns_aria_expanded }}">
                    <i class="fa fa-bullhorn"></i><span class="title">Campaigns</span>
                </a>

                <div id="campaigns" class="{{ $campaigns_div_collapse_class }}" aria-expanded="{{ $campaigns_aria_expanded }}" style="{{ $campaigns_div_height }}">
                    <ul class="submenu">
                        @if(have_right(35))
                        <li>
                            <a href="{{ url('admin/email-campaigns') }}" class="{{($segment_2 == 'email-campaigns') ? 'active' : ''}}">
                                <i class="fa fa-envelope"></i><span class="title">Email Campaigns</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(33))
                        <li>
                            <a href="{{ url('admin/sms-campaigns') }}" class="{{($segment_2 == 'sms-campaigns') ? 'active' : ''}}">
                                <i class="fa fa-comments"></i><span class="title">SMS Campaigns</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(97))
                        <li>
                            <a href="{{ url('admin/split-campaigns') }}" class="{{($segment_2 == 'split-campaigns') ? 'active' : ''}}">
                                <i class="fa fa-envelope"></i><span class="title">Split Testing Campaigns</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(have_right(82))
            <li>
                <a href="{{ url('admin/email-campaign-templates') }}" class="{{($segment_2 == 'email-campaign-templates') ? 'active' : ''}}">
                    <i class="fa fa-envelope"></i><span class="title">Email Campaign Templates</span>
                </a>
            </li>
            @endif

            <!-- <li>
                <a href="{{ url('admin/email-campaign-template-labels') }}" class="{{($segment_2 == 'email-campaign-template-labels') ? 'active' : ''}}">
                    <i class="fa fa-envelope"></i><span class="title">Email Campaign Template Labels</span>
                </a>
            </li> -->

            <?php
            if ($segment_2 == 'email-templates' || $segment_2 == 'email-template-labels') {
                $email_templates_active_class = 'active';
                $email_templates_aria_expanded = 'true';
                $email_templates_div_height = '';
                $email_templates_div_collapse_class = 'collapse in';
            } else {
                $email_templates_active_class = 'collapsed';
                $email_templates_aria_expanded = 'false';
                $email_templates_div_height = 'height: 0px';
                $email_templates_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(86) || have_right(90))
            <li class="panel">
                <a href="#email-temp" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $email_templates_active_class }} drop-menu-links" aria-expanded="{{ $email_templates_aria_expanded }}">
                    <i class="fa fa-envelope"></i><span class="title">Email Templates</span>
                </a>

                <div id="email-temp" class="{{ $email_templates_div_collapse_class }}" aria-expanded="{{ $email_templates_aria_expanded }}" style="{{ $email_templates_div_height }}">
                    <ul class="submenu">
                        @if(have_right(86))
                        <li>
                            <a href="{{  url('admin/email-templates') }}" class="{{($segment_2 == 'email-templates') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Email Templates Listing</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(90))
                        <li>
                            <a href="{{ url('admin/email-template-labels') }}" class="{{($segment_2 == 'email-template-labels') ? 'active' : ''}}">
                                <i class="fa fa-tags" aria-hidden="true"></i><span class="title">Email Template Labels</span>
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </li>
            @endif

            <?php
            if ($segment_2 == 'cms-pages' || $segment_2 == 'cms-page-labels') {
                $cms_page_active_class = 'active';
                $cms_page_aria_expanded = 'true';
                $cms_page_div_height = '';
                $cms_page_div_collapse_class = 'collapse in';
            } else {
                $cms_page_active_class = 'collapsed';
                $cms_page_aria_expanded = 'false';
                $cms_page_div_height = 'height: 0px';
                $cms_page_div_collapse_class = 'collapse';
            }
            ?>

            <hr>

            @if(have_right(57) || have_right(122))
            <li class="panel">
                <a href="#cms-page" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $cms_page_active_class }} drop-menu-links" aria-expanded="{{ $cms_page_aria_expanded }}">
                    <i class="fa fa-file-text-o"></i><span class="title">CMS Pages</span>
                </a>

                <div id="cms-page" class="{{ $cms_page_div_collapse_class }}" aria-expanded="{{ $cms_page_aria_expanded }}" style="{{ $cms_page_div_height }}">
                    <ul class="submenu">
                        @if(have_right(57))
                        <li>
                            <a href="{{ url('admin/cms-pages') }}" class="{{($segment_2 == 'cms-pages') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">CMS Pages Listing</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(122))
                        <li>
                            <a href="{{ url('admin/cms-page-labels') }}" class="{{($segment_2 == 'cms-page-labels') ? 'active' : ''}}">
                                <i class="fa fa-tags" aria-hidden="true"></i><span class="title">CMS Page Labels</span>
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </li>
            @endif


            <?php
            if ($segment_2 == 'home-contents' || $segment_2 == 'home-content-labels' || $segment_2 == 'services' || $segment_2 == 'service-labels') {
                $home_contents_active_class = 'active';
                $home_contents_aria_expanded = 'true';
                $home_contents_div_height = '';
                $home_contents_div_collapse_class = 'collapse in';
            } else {
                $home_contents_active_class = 'collapsed';
                $home_contents_aria_expanded = 'false';
                $home_contents_div_height = 'height: 0px';
                $home_contents_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(70) || have_right(74) || have_right(66) || have_right(114))
            <li class="panel">
                <a href="#home-contents" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $home_contents_active_class }} drop-menu-links" aria-expanded="{{ $home_contents_aria_expanded }}">
                    <i class="fa fa-file-o"></i><span class="title">Home Contents</span>
                </a>

                <div id="home-contents" class="{{ $home_contents_div_collapse_class }}" aria-expanded="{{ $home_contents_aria_expanded }}" style="{{ $home_contents_div_height }}">
                    <ul class="submenu">
                        @if(have_right(70))
                        <li>
                            <a href="{{  url('admin/home-contents') }}" class="{{($segment_2 == 'home-contents') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Home Contents Listing</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(74))
                        <li>
                            <a href="{{ url('admin/home-content-labels') }}" class="{{($segment_2 == 'home-content-labels') ? 'active' : ''}}">
                                <i class="fa fa-tags" aria-hidden="true"></i><span class="title">Home Content Labels</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(66))
                        <li>
                            <a href="{{ url('admin/services') }}" class="{{($segment_2 == 'services') ? 'active' : ''}}">
                                <i class="fa fa-server"></i><span class="title">Services</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(114))
                        <li>
                            <a href="{{ url('admin/service-labels') }}" class="{{($segment_2 == 'service-labels') ? 'active' : ''}}">
                                <i class="fa fa-server"></i><span class="title">Service Labels</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif


            <?php
            if ($segment_2 == 'languages' || $segment_2 == 'language-translations' || $segment_2 == 'label-translations' || $segment_2 == 'language-modules' || $segment_2 == 'text-translations') {
                $languages_active_class = 'active';
                $languages_aria_expanded = 'true';
                $languages_div_height = '';
                $languages_div_collapse_class = 'collapse in';
            } else {
                $languages_active_class = 'collapsed';
                $languages_aria_expanded = 'false';
                $languages_div_height = 'height: 0px';
                $languages_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(99) || have_right(103) || have_right(109) || have_right(100))
            <li class="panel">
                <a href="#languages" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $languages_active_class }} drop-menu-links" aria-expanded="{{ $languages_aria_expanded }}">
                    <i class="fa fa-language"></i><span class="title">Languages</span>
                </a>

                <div id="languages" class="{{ $languages_div_collapse_class }}" aria-expanded="{{ $languages_aria_expanded }}" style="{{ $languages_div_height }}">
                    <ul class="submenu">
                        @if(have_right(99))
                        <li>
                            <a href="{{ url('admin/languages') }}" class="{{($segment_2 == 'languages') ? 'active' : ''}}">
                                <i class="fa fa-language"></i><span class="title">Languages</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(103))
                        <li>
                            <a href="{{ url('admin/language-translations') }}" class="{{($segment_2 == 'language-translations') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Language Translations</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(109))
                        <li>
                            <a href="{{ url('admin/label-translations') }}" class="{{($segment_2 == 'label-translations') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Label Translations</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(100))
                        <li>
                            <a href="{{ url('admin/language-modules') }}" class="{{($segment_2 == 'language-modules') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Language Modules</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(100))
                        <li>
                            <a href="{{ url('admin/text-translations') }}" class="{{($segment_2 == 'text-translations') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Text Translations</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif


            @if(have_right(29))
            <li>
                <a href="{{ url('admin/faqs') }}" class="{{($segment_2 == 'faqs') ? 'active' : ''}}">
                    <i class="fa fa-question"></i><span class="title">FAQs</span>
                </a>
            </li>
            @endif

            <?php
            if ($segment_2 == 'features' || $segment_2 == 'feature-labels') {
                $features_page_active_class = 'active';
                $features_page_aria_expanded = 'true';
                $features_page_div_height = '';
                $features_page_div_collapse_class = 'collapse in';
            } else {
                $features_page_active_class = 'collapsed';
                $features_page_aria_expanded = 'false';
                $features_page_div_height = 'height: 0px';
                $features_page_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(50) || have_right(126))
            <li class="panel">
                <a href="#features-page" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $features_page_active_class }} drop-menu-links" aria-expanded="{{ $features_page_aria_expanded }}">
                    <i class="fa fa-file-o"></i><span class="title">Features Page</span>
                </a>

                <div id="features-page" class="{{ $features_page_div_collapse_class }}" aria-expanded="{{ $features_page_aria_expanded }}" style="{{ $features_page_div_height }}">
                    <ul class="submenu">
                        @if(have_right(50))
                        <li>
                            <a href="{{ url('admin/features') }}" class="{{($segment_2 == 'features') ? 'active' : ''}}">
                                <i class="fa fa-file-o"></i><span class="title">Features Content</span>
                            </a>
                        </li>
                        @endif

                        @if(have_right(126))
                        <li>
                            <a href="{{ url('admin/feature-labels') }}" class="{{($segment_2 == 'feature-labels') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Feature Labels</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            <?php
            if ($segment_2 == 'about-us-page' || $segment_2 == 'about-us-testimonials' || $segment_2 == 'about-us-labels') {
                $about_us_page_active_class = 'active';
                $about_us_page_aria_expanded = 'true';
                $about_us_page_div_height = '';
                $about_us_page_div_collapse_class = 'collapse in';
            } else {
                $about_us_page_active_class = 'collapsed';
                $about_us_page_aria_expanded = 'false';
                $about_us_page_div_height = 'height: 0px';
                $about_us_page_div_collapse_class = 'collapse';
            }
            ?>

            @if(have_right(38) || have_right(39) || have_right(118))
            <li class="panel">
                <a href="#about-us-page" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $about_us_page_active_class }} drop-menu-links" aria-expanded="{{ $about_us_page_aria_expanded }}">
                    <i class="fa fa-file-o"></i><span class="title">About Us</span>
                </a>

                <div id="about-us-page" class="{{ $about_us_page_div_collapse_class }}" aria-expanded="{{ $about_us_page_aria_expanded }}" style="{{ $about_us_page_div_height }}">
                    <ul class="submenu">
                        @if(have_right(38))
                        <li>
                            <a href="{{ url('admin/about-us-page') }}" class="{{($segment_2 == 'about-us-page') ? 'active' : ''}}">
                                <i class="fa fa-file-o"></i><span class="title">About Us Content</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(118))
                        <li>
                            <a href="{{ url('admin/about-us-labels') }}" class="{{($segment_2 == 'about-us-labels') ? 'active' : ''}}">
                                <i class="fa fa-file-o" aria-hidden="true"></i><span class="title">About Us Labels</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(39))
                        <li>
                            <a href="{{ url('admin/about-us-testimonials') }}" class="{{($segment_2 == 'about-us-testimonials') ? 'active' : ''}}">
                                <i class="fa fa-comments" aria-hidden="true"></i><span class="title">Testimonials</span>
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </li>
            @endif

            @if(have_right(94))
            <li>
                <a href="{{ url('admin/contact-us-queries') }}" class="{{($segment_2 == 'contact-us-queries') ? 'active' : ''}}">
                    <i class="fa fa-phone"></i><span class="title">Contact Us Queries</span>
                </a>
            </li>
            @endif

            <?php
            if ($segment_2 == 'packages' || $segment_2 == 'package-features' || $segment_2 == 'package-settings') {
                $packages_active_class = 'active';
                $packages_aria_expanded = 'true';
                $packages_div_height = '';
                $packages_div_collapse_class = 'collapse in';
            } else {
                $packages_active_class = 'collapsed';
                $packages_aria_expanded = 'false';
                $packages_div_height = 'height: 0px';
                $packages_div_collapse_class = 'collapse';
            }
            ?>
            @if(have_right(21) || have_right(15) || have_right(110))
            <li class="panel">
                <a href="#packages" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $packages_active_class }} drop-menu-links" aria-expanded="{{ $packages_aria_expanded }}">
                    <i class="fa fa-clipboard"></i><span class="title">Packages</span>
                </a>

                <div id="packages" class="{{ $packages_div_collapse_class }}" aria-expanded="{{ $packages_aria_expanded }}" style="{{ $packages_div_height }}">
                    <ul class="submenu">
                        @if(have_right(21))
                        <li>
                            <a href="{{ url('admin/package-features') }}" class="{{($segment_2 == 'package-features') ? 'active' : ''}}">
                                <i class="fa fa-list"></i><span class="title">Package Features</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(15))
                        <li>
                            <a href="{{ url('admin/packages') }}" class="{{($segment_2 == 'packages') ? 'active' : ''}}">
                                <i class="fa fa-clipboard"></i><span class="title">Packages</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(110))
                        <li>
                            <a href="{{ url('admin/package-settings') }}" class="{{($segment_2 == 'package-settings') ? 'active' : ''}}">
                                <i class="fa fa-cog"></i><span class="title">Pay As You Go Package Settings</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            <hr>

            <?php
            if ($segment_2 == 'payment-settings' || $segment_2 == 'package-payments' || $segment_2 == 'pay-as-you-go-package-payments') {
                $payments_active_class = 'active';
                $payments_aria_expanded = 'true';
                $payments_div_height = '';
                $payments_div_collapse_class = 'collapse in';
            } else {
                $payments_active_class = 'collapsed';
                $payments_aria_expanded = 'false';
                $payments_div_height = 'height: 0px';
                $payments_div_collapse_class = 'collapse';
            }
            ?>
            @if(have_right(130) || have_right(131) || have_right(132))
            <li class="panel">
                <a href="#payments" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="{{ $payments_active_class }} drop-menu-links" aria-expanded="{{ $payments_aria_expanded }}">
                    <i class="fa fa-credit-card-alt"></i><span class="title">Payments</span>
                </a>

                <div id="payments" class="{{ $payments_div_collapse_class }}" aria-expanded="{{ $payments_aria_expanded }}" style="{{ $payments_div_height }}">
                    <ul class="submenu">
                        @if(have_right(130))
                        <li>
                            <a href="{{ url('admin/payment-settings') }}" class="{{($segment_2 == 'payment-settings') ? 'active' : ''}}">
                                <i class="fa fa-cog"></i><span class="title">Payment Settings</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(131))
                        <li>
                            <a href="{{ url('admin/package-payments') }}" class="{{($segment_2 == 'package-payments') ? 'active' : ''}}">
                                <i class="fa fa-credit-card-alt"></i><span class="title">Package Payments</span>
                            </a>
                        </li>
                        @endif
                        @if(have_right(132))
                        <li>
                            <a href="{{ url('admin/pay-as-you-go-package-payments') }}" class="{{($segment_2 == 'pay-as-you-go-package-payments') ? 'active' : ''}}">
                                <i class="fa fa-credit-card-alt"></i><span class="title">Pay-as-you-go Package Payments</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            <hr>

            @if(have_right(10))
            <li>
                <a href="{{ url('admin/settings') }}" class="{{($segment_2 == 'settings') ? 'active' : ''}}">
                    <i class="fa fa-cog"></i><span class="title">Site Settings</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
</div>