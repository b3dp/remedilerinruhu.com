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
                        <h2 class="content-title card-title">Hakkımızda Yazıları</h2>
                    </div>
                    <div>
                        <a href="about/add" class="btn btn-primary btn-sm rounded">Hakkımızda Yazısı Ekle</a>
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
											<th>Sıralama</th> 
											<th>Resim</th> 
											<th>Başlık</th> 
											<th>Durum</th> 
											<th>Eklenme Tarihi</th>
                                            <th>Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($aboutList as $row) : ?>
											<tr> 
												<td><b class="badge badge-soft-info"><?= $row->id ?></b></td> 
												<td>
													<input type="number" style="width: 70px" class="form-control" onchange="miniSingleRank(this.value, '<?= $row->id ?>', '<?= base_url('aboutRank') ?>')" placeholder="0" value="<?= $row->rank ?>" />
												</td> 
												<td class="d-flex align-items-center">
													<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
														<div class="symbol-label">
															<?php if ($row->picture) : ?>
																<img src="<?= base_url() ?>/uploads/about/<?= $row->picture ?>" alt="" class="img-md w-100" >
															<?php endif ?>
														</div>
													</div>
												</td>
												<td>
													<div class="d-flex flex-column">
														<strong class="text-gray-800 "><?= $row->title ?></strong> 
													</div>
												</td>
												<td>
													<div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
														<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('aboutStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder"><?= timeTR($row->created_at) ?></div>
												</td>
                                                <td>
                                                    <a href="about/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('aboutDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>
