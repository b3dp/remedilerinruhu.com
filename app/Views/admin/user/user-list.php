<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
			<section class="content-main">		
                <div class="content-header">
                    <h2 class="content-title">Üyeler</h2>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTableStandart">
                                <thead>
                                    <tr>
										<th>ID</th>
										<th>Ad Soyad</th>
										<th>Telefon</th>
										<th>E-posta</th>
										<th>Son Giriş</th>
										<th>Üyelik Tipi</th>
										<th>Üyelik Tarihi</th>
										<th>Durum</th>
										<th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php foreach ($users as $row) : ?>
										<tr>
											<td><?= $row->id ?></td>
											<td class="">
												<div class="">
													<a href="user/detail/<?= $row->id ?>" class="text-gray-800 text-hover-primary mb-0"><?= $row->full_name ?></a> 
												</div>
											</td>
											<td><?= $row->phone ?></td>
											<td><?= $row->email ?></td>
											<td>
												<?php if ($row->last_login) : ?>
													<span class="badge badge-soft-success fw-bolder"><?= timeTR($row->last_login) ?></span>
												<?php endif ?>
											</td>
											<td><span class="badge badge-soft-info fw-bolder"><?= $row->ut_name ?></span></td>
											<td><?= timeTR($row->created_at) ?></td>
											<td>
												<div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
													<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('userStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
												</div>
											</td>
											<td>
												<a href="user/detail/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand mb-1"> <i class="material-icons md-edit"></i> Düzenle </a>
												<a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('userDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
											</td>
										</tr>
									<?php endforeach ?>
                                </tbody>
                            </table>
                            <!-- table-responsive.// -->
                        </div>
                    </div>
                    <!-- card-body end// -->
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>


