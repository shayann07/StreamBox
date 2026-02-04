<?= $this->include('templates/header'); ?>
<?php use App\Libraries\TimeHelper; ?>

<main id="nsofts_main">
    <div class="nsofts-container">

        <?php 
            $adminType = session()->get('userdata')->admin_type ?? null;
            if ($adminType !== null && ($adminType == 1 || $adminType == 3)) {
                $cards = [
                    ['title'=>'Notification', 'count'=>$notificationTotalCount, 'icon'=>'ri-notification-2-line', 'bg'=>'bg-warning'],
                    ['title'=>'Custom Ads', 'count'=>$customAdsTotalCount, 'icon'=>'ri-advertisement-line', 'bg'=>'bg-danger'],
                    ['title'=>'Extream codes', 'count'=>$extreamTotalCount, 'icon'=>'ri-xing-line', 'bg'=>'bg-success'],
                    ['title'=>'1-Stream', 'count'=>$streamTotalCount, 'icon'=>'ri-mist-line', 'bg'=>'bg-info'],
                    ['title'=>'Blocklist', 'count'=>$blocklistTotalCount, 'icon'=>'ri-spam-3-line', 'bg'=>'bg-primary'],
                    ['title'=>'Device ID', 'count'=>$deviceIdTotalCount, 'icon'=>'ri-group-line', 'bg'=>'bg-warning'],
                    ['title'=>'Activation Code', 'count'=>$activationCodeTotalCount, 'icon'=>'ri-group-line', 'bg'=>'bg-danger'],
                    ['title'=>'Reports', 'count'=>$reportsTotalCount, 'icon'=>'ri-feedback-line', 'bg'=>'bg-success'],
                    ['title'=>'Store Policy', 'count'=>$storePolicyTotalCount, 'icon'=>'ri-alarm-warning-line', 'bg'=>'bg-info'],
                    ['title'=>'API Token', 'count'=>$tokenTotalCount, 'icon'=>'ri-alarm-warning-line', 'bg'=>'bg-primary'],
                    ['title'=>'Admin', 'count'=>$adminTotalCount, 'icon'=>'ri-admin-line', 'bg'=>'bg-warning'],
                    ['title'=>'App Theme', 'count'=> 10, 'icon'=>'ri-pencil-ruler-2-line', 'bg'=>'bg-danger'],
                ];
            } else {
                $cards = [
                    ['title'=>'Device ID', 'count'=>$deviceIdTotalCount, 'icon'=>'ri-group-line', 'bg'=>'bg-warning'],
                    ['title'=>'Activation Code', 'count'=>$activationCodeTotalCount, 'icon'=>'ri-group-line', 'bg'=>'bg-danger'],
                ];
            }
        ?>
        
        <div class="row g-4">
            <?php foreach($cards as $card) : ?>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card card-badge">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <span class="h6 font-semibold text-muted text-sm d-block mb-2"><?= esc($card['title']); ?></span>
                                    <span class="h3 font-bold mb-0"><?= esc($card['count']); ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-shape <?= esc($card['bg']); ?> text-white text-lg">
                                        <i class="<?= esc($card['icon']); ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
            // Theme mapping
            $themes = [
                '2' => 'Glossy.webp',
                '3' => 'BlackPanther.webp',
                '4' => 'MovieUI.webp',
                '5' => 'VUI.webp',
                '6' => 'ChristmasUI.webp',
                '7' => 'HalloweenUI.webp',
                '8' => 'Ramadan.webp',
                '9' => 'Sports.webp',
                '10' => 'VibeUI.webp',
            ];
            $themeImage = $themes[$settings['is_theme']] ?? 'OneUI.webp';
        ?>
        
        <div class="row g-4 mt-2">
            <div class="col-lg-7 col-md-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-4">App Theme</h5>
                        <img src="<?= base_url("assets/images/themes/$themeImage"); ?>" 
                             alt="App Theme" 
                             style="width:100%; height:auto; border-radius:10px;">
                    </div>
                </div>
            </div>
            <?php if($adminType !== null && $adminType == 1 or $adminType == 3){?>
                <div class="col-lg-5 col-md-6">
                    <div class="card card-dashboard h-100">
                        <div class="card-body p-4">
                            <h5 class="mb-0">New Reports</h5>
                            <?php if(!empty($result)) : ?>
                                <?php foreach($result as $row) : ?>
                                    <div class="d-flex align-items-center mt-4">
                                        <span class="d-block fw-semibold"><?= esc($row['report_title']); ?></span>
                                        <span class="text-muted ms-auto"><?= esc(TimeHelper::calculateTimeSpan($row['report_on'])); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center p-2">
                                    <h5>No data found!</h5>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</main>

<?= $this->include('templates/footer'); ?>
