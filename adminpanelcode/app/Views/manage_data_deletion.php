<?= $this-> include('templates/header');?>
<?php use App\Libraries\TimeHelper; ?>

<main id="nsofts_main">
    <div class="nsofts-container">
        <div class="card h-100">
            <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                    <span class="ps-2 lh-1"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></span>
                </div>
                <div class="d-flex mt-2 mt-md-0">
                    <form method="get" id="searchForm" action="" class="me-2">
                        <div class="input-group">
                            <input type="text" id="search_input" class="form-control" placeholder="Search email" name="keyword" value="<?= esc(service('request')->getGet('keyword')) ?>" required="required">
                            <button class="btn btn-outline-default d-inline-flex align-items-center" type="search">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(!empty($result)){ ?>
                    <div class="row g-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-truncate">User Email</th>
                                    <th class="text-center display-desktop">Date</th>
                                    <th class="text-center">Type</th>
                                    <th style="width: 100px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="load-more-container">
                                <?php $i=0; foreach($result as $row){ ?>
                                    <tr class="card-item">
                                        <td class="text-truncate"><?= esc($row['user_email']) ?></td>
                                        <td class="text-center display-desktop"><?= esc(TimeHelper::calculateTimeSpan($row['deletion_on'])); ?></td>
                                        <td class="text-center">
                                            <span class="nsofts-badge nsofts-badge-primary"><?= esc($row['policy_type']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-data-deletion/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                 <?php $i++; } ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="nsofts-load-btn mt-4 mb-2 d-flex align-items-center justify-content-center"
                        id="load-more-btn">
                        <span>Load More</span>
                        <i class="ri-sort-desc"></i>
                    </button>
                <?php } else { ?>
                    <h3 class="text-center p-5">No data found</h3>
                <?php } ?>
                </nav>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>