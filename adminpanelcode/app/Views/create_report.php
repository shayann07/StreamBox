<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></li>
            </ol>
        </nav>
        <div class="row g-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-5"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></h5>
                    <div class="mb-3">
                        <h5 class="mb-4">Title - <?php if(isset($report_id)){echo $row['report_title'];}?></h5>
                        <?=stripslashes($row['report_msg'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>