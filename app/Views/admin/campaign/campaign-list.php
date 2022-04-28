<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <section class="content-main">
                <div class="content-header">
                    <div>
                        <h2 class="content-title card-title">Kampanya Listesi</h2>
                    </div>
                    <div>
                        <a href="campaign/add" class="btn btn-primary btn-sm rounded">Yeni Kampanya Ekle</a>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0" id="dataTableStandart">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
											<th >Resim</th> 
											<th >Başlık</th> 
											<th >Başlangıç Tarihi</th> 
											<th >Bitiş Tarihi</th>
											<th >Durum</th>
											<th >İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($campaigns as $row) : ?>
											<tr> 
												<td>
													<?= $row->id ?>
												</td>
												<td>
													<img src="<?= base_url() ?>/uploads/campaigns/<?= $row->image ?>" alt="" class="img-sm img-thumbnail" >
                                                </td>
												<td>
													<strong class="text-gray-800 "><?= $row->title ?></strong> 
												</td>
												<td>
													<div class="badge badge-soft-primary fw-bolder"><?= $row->start_at ? timeTR($row->start_at) : 'Sınırı Yok' ?></div>
												</td>
												<td>
													<div class="badge badge-soft-warning fw-bolder"><?= $row->end_at ? timeTR($row->end_at) : 'Sınırı Yok' ?></div>
												</td>
												<td>
													<div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
														<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('campaignStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
													</div>
												</td>
                                                <td>
                                                    <a href="campaign/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('campaignDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- card-body end// -->
                </div>
                <!-- card end// -->
                <div class="pagination-area mt-30 mb-50">
                    <?php if ($totalPage > 1) : ?>
                        <?= paginate('product/list', $page, $totalPage, $filter) ?>
                    <?php endif ?>
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>
