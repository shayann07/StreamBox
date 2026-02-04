<?= $this-> include('templates/header');?>
<?php
$privacy_policy_file_path = base_url().'privacy-policy';
$terms_file_path = base_url().'terms';
?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></li>
            </ol>
        </nav>
            
        <div class="card">
            <div class="card-body p-0">                    
                <div class="nsofts-setting">
                    <div class="nsofts-setting__sidebar">
                        <div class="nav flex-column nav-pills" id="nsofts_setting" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="nsofts_setting_1" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_1" type="button" role="tab" aria-controls="nsofts_setting_1" aria-selected="true">
                                <i class="ri-list-settings-line"></i>
                                <span>General</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_2" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_2" type="button" role="tab" aria-controls="nsofts_setting_2" aria-selected="false">
                                <i class="ri-settings-5-line"></i>
                                <span>App Settings</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_4" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_4" type="button" role="tab" aria-controls="nsofts_setting_4" aria-selected="false">
                                <i class="ri-survey-line"></i>
                                <span>Privacy Policy</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_5" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_5" type="button" role="tab" aria-controls="nsofts_setting_5" aria-selected="false">
                                <i class="ri-survey-line"></i>
                                <span>Terms & Conditions</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_6" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_6" type="button" role="tab" aria-controls="nsofts_setting_6" aria-selected="false">
                                <i class="ri-notification-3-line"></i>
                                <span>Notification</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_7" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_7" type="button" role="tab" aria-controls="nsofts_setting_7" aria-selected="false">
                                <i class="ri-refresh-line"></i>
                                <span>App Update</span>
                            </button>
                            
                        </div>
                    </div>
                    <div class="nsofts-setting__content">
                        <div class="tab-content">
                            
                            <!--General Settings-->
                            <div class="tab-pane fade show active" id="nsofts_setting_content_1" role="tabpanel" aria-labelledby="nsofts_setting_1" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_general" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">General Settings</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_email" id="app_email" value="<?= esc($settings['app_email']) ?>" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Author</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_author" id="app_author" value="<?= esc($settings['app_author']) ?>" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Contact</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_contact" id="app_contact" value="<?= esc($settings['app_contact']) ?>" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Website</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_website" id="app_website" value="<?= esc($settings['app_website']) ?>" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Developed By</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_developed_by" id="app_developed_by" value="<?= esc($settings['app_developed_by']) ?>" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="app_description" id="app_description" class="form-control" ><?= esc($settings['app_description']) ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_general" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--App Settings-->
                            <div class="tab-pane fade" id="nsofts_setting_content_2" role="tabpanel" aria-labelledby="nsofts_setting_2" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_app" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                             <h4 class="mb-4">Active Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Xtream codes</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_xui" name="is_select_xui" value="true" class="nsofts-switch__label" <?php if($settings['is_select_xui']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_xui" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">1-Stream</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_stream" name="is_select_stream" value="true" class="nsofts-switch__label" <?php if($settings['is_select_stream']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_stream" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">M3U Playlist</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_playlist" name="is_select_playlist" value="true" class="nsofts-switch__label" <?php if($settings['is_select_playlist']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_playlist" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Device ID</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_device_id" name="is_select_device_id" value="true" class="nsofts-switch__label" <?php if($settings['is_select_device_id']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_device_id" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Activation Code</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_activation_code" name="is_select_activation_code" value="true" class="nsofts-switch__label" <?php if($settings['is_select_activation_code']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_activation_code" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Single Stream</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_single" name="is_select_single" value="true" class="nsofts-switch__label" <?php if($settings['is_select_single']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_single" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Local Storage</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_local_storage" name="is_local_storage" value="true" class="nsofts-switch__label" <?php if($settings['is_local_storage']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_local_storage" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">OpenVPN</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_ovpn" name="is_ovpn" value="true" class="nsofts-switch__label" <?php if($settings['is_ovpn']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_ovpn" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-4">App Settings</h4>
                                            <div class="mb-3 row" style="display: none;">
                                                <label for="" class="col-sm-5 col-form-label">App Orientation</label>
                                                <div class="col-sm-5">
                                                    <select name="app_orientation" id="app_orientation" class="form-select" required>
                                                        <option value="0" <?php if ($settings['app_orientation'] == '0') { ?>selected<?php } ?>>Auto</option>
                                                        <option value="1" <?php if ($settings['app_orientation'] == '1') { ?>selected<?php } ?>>Landscape</option>
                                                        <option value="2" <?php if ($settings['app_orientation'] == '2') { ?>selected<?php } ?>>Portrait</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3 row mt-5">
                                                <label for="" class="col-sm-5 col-form-label">Download Video</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_dowload" name="is_dowload" value="true" class="nsofts-switch__label" <?php if($settings['is_dowload']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_dowload" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">RTL</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_rtl" name="is_rtl" value="true" class="nsofts-switch__label" <?php if($settings['is_rtl']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_rtl" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">App Maintenance</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_maintenance" name="is_maintenance" value="true" class="cbx hidden" <?php if($settings['is_maintenance']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_maintenance" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Sccrenshot block</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_screenshot" name="is_screenshot" value="true" class="cbx hidden" <?php if($settings['is_screenshot']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_screenshot" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Developer block</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_apk" name="is_apk" value="true" class="cbx hidden" <?php if($settings['is_apk']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_apk" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">VPN block</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_vpn" name="is_vpn" value="true" class="cbx hidden" <?php if($settings['is_vpn']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_vpn" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h4 class="mb-4">Extream codes Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Trial Login</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_trial_xui" name="is_trial_xui" value="true" class="nsofts-switch__label" <?php if($settings['is_trial_xui']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_trial_xui" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active DNS</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_xui_dns" name="is_xui_dns" value="true" class="nsofts-switch__label" <?php if($settings['is_xui_dns']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_xui_dns" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active Radio</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_xui_radio" name="is_xui_radio" value="true" class="nsofts-switch__label" <?php if($settings['is_xui_radio']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_xui_radio" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-4">1-Stream Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active DNS</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_stream_dns" name="is_stream_dns" value="true" class="nsofts-switch__label" <?php if($settings['is_stream_dns']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_stream_dns" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active Radio</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_stream_radio" name="is_stream_radio" value="true" class="nsofts-switch__label" <?php if($settings['is_stream_radio']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_stream_radio" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="app_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--Privacy Policy-->
                            <div class="tab-pane fade" id="nsofts_setting_content_4" role="tabpanel" aria-labelledby="nsofts_setting_4" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_policy" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Privacy Policy</h4>
                                    <div class="pb-clipboard mb-2">
                                        <span class="pb-clipboard__url"><span id="clipboard_policy"><?= esc($privacy_policy_file_path) ?></span></span>
                                        <a class="pb-clipboard__link btn_policy" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#clipboard_base_url" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" title="Copy url">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div>
                                        <textarea name="app_privacy_policy" id="app_privacy_policy" rows="5" class="nsofts-editor mb-4">
                                            <?= esc(stripslashes($settings['app_privacy_policy'])) ?>
                                        </textarea>
                                    </div>
                                    <button type="submit" name="policy_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--Terms & Conditions-->
                            <div class="tab-pane fade" id="nsofts_setting_content_5" role="tabpanel" aria-labelledby="nsofts_setting_5" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_terms" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Terms & Conditions</h4>
                                    <div class="pb-clipboard mb-2">
                                        <span class="pb-clipboard__url"><span id="clipboard_terms"><?= esc($terms_file_path) ?></span></span>
                                        <a class="pb-clipboard__link btn_terms" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#clipboard_base_url" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" title="Copy url">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div>
                                        <textarea name="app_terms" id="app_terms" rows="5" class="nsofts-editor mb-4">
                                            <?= esc(stripslashes($settings['app_terms'])) ?>
                                        </textarea>
                                    </div>
                                    <button type="submit" name="terms_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--Notification-->
                            <div class="tab-pane fade" id="nsofts_setting_content_6" role="tabpanel" aria-labelledby="nsofts_setting_6" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_notification" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Notification</h4>
                                    <div class="mb-3 row">
                                        <label class="col-sm-2 col-form-label">OneSignal App ID</label>
                                        <div class="col-sm-10">
                                            <div class="nsofts-input-icon nsofts-input-icon--left">
                                                <label for="onesignal_app_id" class="nsofts-input-icon__left">
                                                    <i class="ri-key-2-line"></i>
                                                </label>
                                                <input type="text" name="onesignal_app_id" id="onesignal_app_id" value="<?= esc($settings['onesignal_app_id']) ?>"  class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-2 col-form-label">OneSignal Rest Key</label>
                                        <div class="col-sm-10">
                                            <div class="nsofts-input-icon nsofts-input-icon--left">
                                                <label for="onesignal_rest_key" class="nsofts-input-icon__left">
                                                    <i class="ri-key-2-line"></i>
                                                </label>
                                                <input type="text" name="onesignal_rest_key" id="onesignal_rest_key" value="<?= esc($settings['onesignal_rest_key']) ?>"   class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="notification_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--App Update-->
                            <div class="tab-pane fade" id="nsofts_setting_content_7" role="tabpanel" aria-labelledby="nsofts_setting_7" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_handler');?>" name="settings_app_update" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">App Update</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">ON/OFF</label>
                                        <div class="col-sm-10">
                                            <div class="nsofts-switch d-flex align-items-center">
                                                <input type="checkbox" id="app_update_status" name="app_update_status" value="true" class="nsofts-switch__label" <?php if($settings['app_update_status']=='true'){ echo 'checked'; }?>/>
                                                <label for="app_update_status" class="nsofts-switch__label"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">New App Version Code</label>
                                        <div class="col-sm-10">
                                            <input type="number" min="1" name="app_new_version" id="app_new_version" required="" class="form-control" value="<?= esc($settings['app_new_version']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="app_update_desc"  class="form-control"><?php echo stripslashes($settings['app_update_desc']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">App Link</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="app_redirect_url" id="app_redirect_url" required="" class="form-control" value="<?= esc($settings['app_redirect_url']) ?>">
                                        </div>
                                    </div>
                                    <button type="submit" name="app_update_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabLinks = document.querySelectorAll('#nsofts_setting .nav-link');
        const tabContents = document.querySelectorAll('.tab-pane');
        
        // Load last active tab from localStorage
        const activeTab = localStorage.getItem('activeTabApp') || '#nsofts_setting_content_1'; // Default to General tab
        const activeTabLink = document.querySelector(`#nsofts_setting [data-bs-target="${activeTab}"]`);
        const activeTabContent = document.querySelector(activeTab);
        
        // Check if the tab exists, otherwise fallback to General tab
        if (activeTabLink && activeTabContent) {
            tabLinks.forEach(link => link.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('show', 'active'));
            activeTabLink.classList.add('active');
            activeTabContent.classList.add('show', 'active');
        }
        
        // Listen for tab click events and save to localStorage
        tabLinks.forEach(link => {
            link.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-bs-target');
                localStorage.setItem('activeTabApp', targetTab);
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(event) {
        
        $(document).on("click", ".btn_policy", function(e) {
            var el = document.getElementById('clipboard_policy');
            var successful = copyToClipboard(el);
            if (successful) {
                $.notify('Copied!', { position:"top right",className: 'success'} );
            } else {
                $.notify('Whoops, not copied!', { position:"top right",className: 'error'} );
            }
        });
        
        $(document).on("click", ".btn_terms", function(e) {
            var el = document.getElementById('clipboard_terms');
            var successful = copyToClipboard(el);
            if (successful) {
                $.notify('Copied!', { position:"top right",className: 'success'} );
            } else {
                $.notify('Whoops, not copied!', { position:"top right",className: 'error'} );
            }
        });
    });
</script>