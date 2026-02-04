<?= $this-> include('templates/header');?>
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
                            <button class="nav-link" id="nsofts_setting_1" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_1" type="button" role="tab" aria-controls="nsofts_setting_1" aria-selected="false">
                                <i class="ri-list-settings-line"></i>
                                <span>General</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_3" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_3" type="button" role="tab" aria-controls="nsofts_setting_3" aria-selected="false">
                                <i class="ri-paint-brush-line"></i>
                                <span>Custom Style</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_4" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_4" type="button" role="tab" aria-controls="nsofts_setting_4" aria-selected="false">
                                <i class="ri-settings-5-line"></i>
                                <span>RTL Settings</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_2" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_2" type="button" role="tab" aria-controls="nsofts_setting_2" aria-selected="false">
                                <i class="ri-mail-settings-line"></i>
                                <span>SMTP Settings</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_5" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_5" type="button" role="tab" aria-controls="nsofts_setting_5" aria-selected="false">
                                <i class="ri-google-line"></i>
                                <span>reCAPTCHA</span>
                            </button>
                            
                        </div>
                    </div>
                    <div class="nsofts-setting__content">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nsofts_setting_content_1" role="tabpanel" aria-labelledby="nsofts_setting_1" tabindex="0">
                                <form action="<?= base_url('/ns-admin/panel_handler');?>" name="settings_general" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">General Settings</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="app_name" id="app_name" value="<?= esc($settings['app_name']) ?>" required="required">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Description</label>
                                        <div class="col-sm-10">
                                            <textarea rows="4" name="site_description" class="form-control" required=""><?= esc($settings['site_description']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Keywords</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="site_keywords" id="site_keywords" value="<?= esc($settings['site_keywords']) ?>"  class="form-control" required="required">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site logo</label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="file" class="form-control-file" name="app_logo" value="fileupload" accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload">
                                                    <p class="control-label-help hint_lbl">(Recommended resolution: 512x512)</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php if($settings['app_logo']!='') { ?>
                                                        <div class="fileupload_img" id="imagePreview">
                                                            <img  type="image" src="<?= base_url('images/'.$settings['app_logo']) ?>" style="width: 50px;height: 50px" onerror="this.src='<?= base_url('assets/images/300x300.jpg') ?>';" loading="lazy" alt="image" />
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="fileupload_img" id="imagePreview">
                                                            <img type="image" src="assets/images/300x300.jpg" style="width: 50px; height: 50px"  alt="image" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_general" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nsofts_setting_content_3" role="tabpanel" aria-labelledby="nsofts_setting_3" tabindex="0">
                                <form action="<?= base_url('/ns-admin/panel_handler');?>" method="post" id="custom_style">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Custom CSS or JS Script</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Header Code</label>
                                        <div class="col-sm-10">
                                            <textarea rows="8" name="header_code" class="form-control"  placeholder="Custom CSS or JS Script" ><?= isset($settings['header_code']) ? service('purify')->purifyHtml(html_entity_decode($settings['header_code'])) : '' ?></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Footer Code</label>
                                        <div class="col-sm-10">
                                            <textarea rows="8" name="footer_code" class="form-control" placeholder="Custom CSS or JS Script"><?= isset($settings['footer_code']) ? service('purify')->purifyHtml(html_entity_decode($settings['footer_code'])) : '' ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_style" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nsofts_setting_content_4" role="tabpanel" aria-labelledby="nsofts_setting_4" tabindex="0">
                                <form action="<?= base_url('/ns-admin/panel_handler');?>" method="post" id="submit_rtl">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">RTL Setting</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">RTL</label>
                                        <div class="col-sm-10">
                                            <div class="nsofts-switch d-flex align-items-center">
                                                <input type="checkbox" id="site_direction" name="site_direction" value="1" class="nsofts-switch__label" <?php if($settings['site_direction']=='1'){ echo 'checked'; }?>/>
                                                <label for="site_direction" class="nsofts-switch__label"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_rtl" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nsofts_setting_content_2" role="tabpanel" aria-labelledby="nsofts_setting_2" tabindex="0">
                                <h4 class="mb-4">SMTP Settings</h4>
                                
                                <div class="row">
                                    <form action="<?= base_url('/ns-admin/panel_handler');?>" method="post" class="form form-horizontal" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">SMTP Type</label>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="radio" name="smtp_type" id="gmail" class="form-check-input" value="gmail" <?php if ($row['smtp_type'] == 'gmail') { echo ' checked="" id="disabledFieldsetCheck"';} ?>>
                                                    <label class="form-check-label" for="gmail"> Gmail SMTP</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="radio" name="smtp_type" id="server" class="form-check-input" value="server" <?php if ($row['smtp_type'] == 'server') { echo ' checked="" disabled="disabled"';} ?>>
                                                    <label class="form-check-label" for="server">Server SMTP</label>
                                                </div>
                                                </div>
                                        </div>
                                        <input type="hidden" name="smtpIndex" value="<?= $row['smtp_type'] ?>">
                                        
                                        <div class="gmailContent" <?php if ($row['smtp_type'] == 'gmail') { echo 'style="display:block"'; } else { echo 'style="display:none"'; } ?>>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">SMTP Host</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="smtp_host[]" class="form-control col-md-7" value="<?= $row['smtp_ghost'] ?>" placeholder="mail.example.in" <?php if ($row['smtp_type'] == 'gmail') {echo 'required';} ?>> 
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="smtp_email[]" class="form-control col-md-7" value="<?= $row['smtp_gemail'] ?>" placeholder="info@example.com" <?php if ($row['smtp_type'] == 'gmail') { echo 'required';} ?>>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" name="smtp_password[]" class="form-control col-md-7" value="" placeholder="********">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-2 col-form-label">SMTPSecure</label>
                                                <div class="col-md-3">
                                                    <select name="smtp_secure[]" class="select2 form-control" <?php if ($row['smtp_type'] == 'gmail') { echo 'required';} ?>>
                                                        <option value="tls" <?php if ($row['smtp_gsecure'] == 'tls') {
                                                            echo 'selected';
                                                        } ?>>TLS</option>
                                                        <option value="ssl" <?php if ($row['smtp_gsecure'] == 'ssl') {
                                                            echo 'selected';
                                                        } ?>>SSL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="port_no[]" class="form-control" value="<?= $row['gport_no'] ?>" placeholder="Enter Port No." <?php if ($row['smtp_type'] == 'gmail') { echo 'required';} ?>>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="serverContent" <?php if ($row['smtp_type'] == 'server') { echo 'style="display:block"'; } else { echo 'style="display:none"'; } ?>>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">SMTP Host</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="smtp_host[]" id="smtp_host" class="form-control col-md-7" value="<?= $row['smtp_host'] ?>" placeholder="mail.example.in" <?php if ($row['smtp_type'] == 'server') { echo 'required';} ?>>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="smtp_email[]" id="smtp_email" class="form-control col-md-7" value="<?= $row['smtp_email'] ?>" placeholder="info@example.com" <?php if ($row['smtp_type'] == 'server') { echo 'required';} ?>>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-2 col-form-label">Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" name="smtp_password[]" id="smtp_password" class="form-control col-md-7" value="" placeholder="********">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-2 col-form-label">SMTPSecure</label>
                                                <div class="col-md-3">
                                                    <select name="smtp_secure[]" class="select2 form-control" <?php if ($row['smtp_type'] == 'server') { echo 'required';} ?>>
                                                        <option value="tls" <?php if ($row['smtp_secure'] == 'tls') { echo 'selected'; } ?>>TLS</option>
                                                        <option value="ssl" <?php if ($row['smtp_secure'] == 'ssl') { echo 'selected'; } ?>>SSL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="port_no[]" id="port_no" class="form-control" value="<?= $row['port_no'] ?>" placeholder="Enter Port No." <?php if ($row['smtp_type'] == 'server') { echo 'required';} ?>>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" id="server_data" data-stuff='<?php echo htmlentities(json_encode($row)); ?>'>
                                        <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                    </form>
                                    
                                    <form action="<?= base_url('/ns-admin/panel_handler');?>" method="post" id="check_smtp_form" class="mt-5">
                                        <?= csrf_field() ?>
                                        <div class="alert alert-info" role="alert">
                                            <h4 class="mb-4">Check Mail Configuration</h4>
                                            <p>Send test mail to your email to check Email functionality work or not.</p>
                                            <hr>
                                            <div class="mb-3 row">
                                                 <div class="form-group">
                                                    <label class="control-label">Email <span style="color: red">*</span>:-</label>
                                                    <input type="text" name="email" class="form-control" autocomplete="off" placeholder="info@example.com" required="">
                                                </div>
                                            </div>
                                            <button type="submit" name="btn_send" class="btn btn-primary" style="min-width: 120px;">Send</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="nsofts_setting_content_5" role="tabpanel" aria-labelledby="nsofts_setting_5" tabindex="0">
                                <form action="<?= base_url('/ns-admin/panel_handler');?>" method="post" id="google_recaptcha">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Setup reCAPTCHA</h4>
                                    <ul class="mb-5">
                                        <li class="mb-1">Go to: <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a></li>
                                        <li class="mb-1">Register for a site key (v2 Checkbox)</li>
                                        <li class="mb-1">Replace: SITE KEY & SECRET KEY</li>
                                    </ul>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-3 col-form-label">reCAPTCHA ON/OFF</label>
                                        <div class="col-sm-9">
                                            <div class="nsofts-switch d-flex align-items-center">
                                                <input type="checkbox" id="is_recaptcha" name="is_recaptcha" value="1" class="nsofts-switch__label" <?php if($settings['is_recaptcha']=='1'){ echo 'checked'; }?>/>
                                                <label for="is_recaptcha" class="nsofts-switch__label"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-3 col-form-label">SITE KEY</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="recaptcha_site_key"class="form-control" placeholder="Enter your site key" value="<?= esc($settings['recaptcha_site_key']) ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-3 col-form-label">SECRET KEY</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="recaptcha_secret_key"class="form-control" placeholder="Enter your secret key" value="<?= esc($settings['recaptcha_secret_key']) ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_recaptcha" class="btn btn-primary" style="min-width: 120px;">Save</button>
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
        const activeTab = localStorage.getItem('activeTab1') || '#nsofts_setting_content_1'; // Default to General tab
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
                localStorage.setItem('activeTab1', targetTab);
            });
        });
    });
</script>

<script type="text/javascript">
$("input[name='smtp_type']").on("click", function(e) {

    var checkbox = $(this);
    
    $("input[name='smtp_password[]']").attr("required", false);
    
    e.preventDefault();
    e.stopPropagation();
    
    var _val = $(this).val();
    if (_val == 'gmail') {
      swal({
        title: "Are you sure?",
        type: "warning",
        confirmButtonClass: 'btn btn-primary m-2',
        cancelButtonClass: 'btn btn-danger m-2',
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: false
      }).then(function(result) {
        if (result.value) {
            
          checkbox.attr("disabled", true);
          checkbox.prop("checked", true);
          $("#server").attr("disabled", false);
          $("#server").prop("checked", false);
          $(".serverContent").hide();
          $(".gmailContent").show();
          $(".serverContent").find("input").attr("required", false);
          $(".gmailContent").find("input").attr("required", true);
          $("input[name='smtpIndex']").val('gmail');
          swal.close();
        } else {
          swal.close();
        }
      });
    } else {
        
      swal({
        title: "Are you sure?",
        type: "warning",
        confirmButtonClass: 'btn btn-primary m-2',
        cancelButtonClass: 'btn btn-danger m-2',
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: false
        
      }).then(function(result) {
        if (result.value) {
          checkbox.attr("disabled", true);
          checkbox.prop("checked", true);
          $("#gmail").attr("disabled", false);
          $("#gmail").prop("checked", false);
          $(".gmailContent").hide();
          $(".serverContent").show();
          $("input[name='smtpIndex']").val('server');
          $(".serverContent").find("input").attr("required", true);
          $(".gmailContent").find("input").attr("required", false);
          swal.close();
        } else {
          swal.close();
        }
      });
    }
});
</script>