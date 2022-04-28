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
                        <h2 class="content-title card-title">Slider Listesi</h2>
                    </div>
                    <div>
                        <a href="slider/add" class="btn btn-primary btn-sm rounded">Yeni Slider Ekle</a>
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
											<th >Sıra</th> 
											<th >Durum</th> 
											<th >Eklenme Tarihi</th>
											<th >İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($sliders as $row) : ?>
											<tr> 
                                                <td>
                                                    <?= $row->id ?>
                                                </td>
												<td class="d-flex align-items-center">
													<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
														<div class="symbol-label">
															<?php if ($row->pictureMobile) : ?>
																<img src="<?= base_url() ?>/uploads/sliders/mobile/<?= $row->pictureMobile ?>" alt="" class="img-md w-100" >
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
													<input type="number" style="width: 70px" class="form-control" onchange="miniSingleRank(this.value, '<?= $row->id ?>', '<?= base_url('sliderRank') ?>')" placeholder="Sira" value="<?= $row->rank ?>" />
												</td> 
												<td>
													<div class="form-check form-check-custom form-check-solid form-switch mb-2">
														<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('sliderStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder"><?= timeTR($row->created_at) ?></div>
												</td>
                                                <td>
                                                    <a href="slider/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('sliderDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
