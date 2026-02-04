<?= $this-> include('templates/header_data');?>
<!-- Start: header -->
<header id="nsofts_header">
    <a href="javascript:void(0)" id="nsofts_hamburger_top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="18" height="18" class="nsofts-hamburger">
            <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-1" />
            <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-2" />
            <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-3" />
        </svg>
    </a>
    <a href="javascript:void(0)" id="nsofts_brand" class="text-truncate"><?= esc($settings['app_name']) ?></a>

    <!-- Header options -->
    <ul class="nsofts-header-nav ms-auto">
        <li class="nsofts-header-nav__item">
            <a href="javascript:void(0)" id="nsofts_theme_toggler" class="nsofts-header-nav__link">
                <i class="ri-moon-fill nsofts-theme-dark"></i>
                <i class="ri-sun-fill nsofts-theme-light"></i>
            </a>
        </li>
        <li class="nsofts-header-nav__item dropdown">
            <a href="javascript:void(0)" class="nsofts-header-nav__link" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-user-6-fill"></i>
            </a>
            <div class="dropdown-menu mt-3">
                <div class="px-3 py-2">
                    <div class="nsofts-avatar">
                        <div class="nsofts-avatar__image">
                            <?php
                            $image = session()->get('userdata')->image ?? '';
                            $imagePath = $image ? base_url('images/' . esc($image)) : base_url('assets/images/user_photo.png');
                            ?>
                            <img src="<?= $imagePath ?>" alt="">
                        </div>
                        <div class="ps-2">
                            <span class="d-block fw-semibold"><?= session()->get('userdata')->username ?? '' ?></span>
                            <?php 
                            $adminType = session()->get('userdata')->admin_type ?? null;
                            if ($adminType !== null): 
                            ?>
                                <?php if($adminType == 1){?>
                                    <span class="d-block">ADMIN</span>
                                <?php } else if($adminType == 0){?>
                                    <span class="d-block">EDITOR</span>
                                    <?php } else if($adminType == 2){?>
                                    <span class="d-block">RESELLER</span>
                                <?php } else if($adminType == 3){?>
                                    <span class="d-block">SUPER ADMIN</span>
                                <?php } else { ?>
                                    <span class="d-block"><?= $adminType ?></span>
                                <?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item dropdown-item--group" href="<?= base_url('ns-admin/profile') ?>">
                    <i class="ri-user-6-fill"></i>
                    <span>My Profile</span>
                </a>
                <a class="dropdown-item dropdown-item--group" href="<?= base_url('ns-admin/settings-panel') ?>">
                    <i class="ri-settings-3-fill"></i>
                    <span>Settings</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item dropdown-item--group" href="<?= base_url('ns-admin/logout') ?>">
                    <i class="ri-shut-down-line"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>
</header>
<!-- End: header -->

<!-- Start: sidebar -->
<aside id="nsofts_sidebar">
    <nav class="nsofts-sidebar-nav" data-scroll="true">
        <ul>
            
            <li class="nsofts-sidebar-nav__item">
                <a href="javascript:void(0)" id="nsofts_hamburger" class="nsofts-sidebar-menu">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="18" height="18" class="nsofts-hamburger">
                        <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-1" />
                        <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-2" />
                        <line x1="0" y1="50%" x2="100%" y2="50%" class="nsofts-hamburger__bar-3" />
                    </svg>
                </a>
            </li>
            
            <li class="nsofts-sidebar-nav__item">
                <a href="<?= base_url('ns-admin/dashboard') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "dashboard") { ?>active<?php } ?>">
                    <i class="ri-home-4-line nsofts-sidebar-nav__icon"></i>
                    <span class="nsofts-sidebar-nav__text">Dashboard</span>
                </a>
            </li>
            
            <?php if($adminType !== null && $adminType != 2){?>
            
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-notification') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "manage_notification" or $currentFile == "notification_onesignal") { ?>active<?php } ?>">
                        <i class="ri-notification-2-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Notification</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-ads') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "manage_ads" or $currentFile == "create_ads") { ?>active<?php } ?>">
                        <i class="ri-advertisement-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Custom Ads</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-extream') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "extream_codes") { ?>active<?php } ?>">
                        <i class="ri-xing-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Extream codes</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-stream') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "stream") { ?>active<?php } ?>">
                        <i class="ri-mist-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">1-Stream</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-blocklist') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "blocklist") { ?>active<?php } ?>">
                        <i class="ri-spam-3-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">DNS Blocklist</span>
                    </a>
                </li>
            
            <?php } ?>
            
            <li class="nsofts-sidebar-nav__item">
                <a href="<?= base_url('ns-admin/manage-device-id') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "device_id") { ?>active<?php } ?>">
                    <i class="ri-group-line nsofts-sidebar-nav__icon"></i>
                    <span class="nsofts-sidebar-nav__text">Device ID to Login</span>
                </a>
            </li>
            
            <li class="nsofts-sidebar-nav__item">
                <a href="<?= base_url('ns-admin/manage-activation') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "activation_code") { ?>active<?php } ?>">
                    <i class="ri-group-line nsofts-sidebar-nav__icon"></i>
                    <span class="nsofts-sidebar-nav__text">Activation Code to Login</span>
                </a>
            </li>
            
            <?php if($adminType !== null && $adminType == 1 or $adminType == 3){?>
            
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-report') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "manage_report") { ?>active<?php } ?>">
                        <i class="ri-feedback-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Reports</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-data-deletion') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "data_deletion") { ?>active<?php } ?>">
                        <i class="ri-alarm-warning-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Store Policy</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/manage-admin') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "manage_admin") { ?>active<?php } ?>">
                        <i class="ri-admin-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Admin</span>
                    </a>
                </li>
                
                <li class="nsofts-sidebar-nav__item nsofts-has-menu">
                    <a href="javascript:void(0)" class="nsofts-sidebar-nav__link <?php if ($currentFile == "settings_panel" or $currentFile == "settings_app" 
                    or $currentFile == "settings_app_ui" or $currentFile == "manage_gallery" or $currentFile == "settings_api" or $currentFile == "create_token"
                    or $currentFile == "settings_web" or $currentFile == "create_sidebar" or $currentFile == "settings_ads") { ?>open active<?php } ?>">
                        <i class="ri-settings-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Settings</span>
                    </a>
                    <ul class="nsofts-submenu <?php if ($currentFile == "settings_panel" or $currentFile == "settings_app" 
                    or $currentFile == "settings_app_ui" or $currentFile == "manage_gallery" or $currentFile == "settings_api" or $currentFile == "create_token"
                    or $currentFile == "settings_web" or $currentFile == "create_sidebar" or $currentFile == "settings_ads") { ?>show<?php } ?>">
                        <li><a href="<?= base_url('ns-admin/settings-panel') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_panel") { ?>active<?php } ?>">Panel Settings</a></li>
                        <li><a href="<?= base_url('ns-admin/settings-app') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_app") { ?>active<?php } ?>">App Settings</a></li>
                        <li><a href="<?= base_url('ns-admin/settings-app-ui') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_app_ui" or $currentFile == "manage_gallery") { ?>active<?php } ?>">App UI</a></li>
                        <li><a href="<?= base_url('ns-admin/settings-api') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_api" or $currentFile == "create_token") { ?>active<?php } ?>">Api Settings</a></li>
                        <li><a href="<?= base_url('ns-admin/settings-web') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_web" or $currentFile == "create_sidebar") { ?>active<?php } ?>">Web Settings</a></li>
                        <li><a href="<?= base_url('ns-admin/settings-ads') ?>" class="nsofts-submenu__link <?php if ($currentFile == "settings_ads") { ?>active<?php } ?>">Advertisement</a></li>
                    </ul>
                </li>
                
                <li class="nsofts-sidebar-nav__item">
                    <a href="<?= base_url('ns-admin/verification') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "verification") { ?>active<?php } ?>">
                        <i class="ri-shield-check-line nsofts-sidebar-nav__icon"></i>
                        <span class="nsofts-sidebar-nav__text">Verification</span>
                    </a>
                </li>
            
            <?php } ?>
            
            <li class="nsofts-sidebar-nav__item">
                <a href="<?= base_url('ns-admin/urls') ?>" class="nsofts-sidebar-nav__link <?php if ($currentFile == "urls") { ?>active<?php } ?>">
                    <i class="ri-links-line nsofts-sidebar-nav__icon"></i>
                    <span class="nsofts-sidebar-nav__text">URLs</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
<!-- End: sidebar -->