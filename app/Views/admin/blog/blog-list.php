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
                        <h2 class="content-title card-title">Blog Listesi</h2>
                    </div>
                    <div>
                        <a href="blog/add" class="btn btn-primary btn-sm rounded">Yeni Blog Ekle</a>
                        <a href="categories/list/blog" class="btn btn-primary btn-sm rounded">Blog Kategorileri Listesi</a>
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
											<th>Resim</th> 
											<th>Başlık</th> 
											<th>Durum</th> 
											<th>Eklenme Tarihi</th>
											<th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($blogList as $row) : ?>
											<tr> 
												<td><b class="badge badge-soft-primary"><?= $row->id ?></b></td> 
												<td class="d-flex align-items-center justify-content-center">
													<img src="<?= base_url() ?>/uploads/blog/<?= $row->picture ?>" alt="" class="mw-200 w-100" >
												</td>
												<td>
													<div class="d-flex flex-column">
														<strong class="text-gray-800 "><?= $row->title ?></strong> 
													</div>
												</td>
												<td>
													<div class="form-check form-check-custom form-check-solid form-switch mb-2">
														<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('blogStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder"><?= timeTR($row->created_at) ?></div>
												</td>
                                                <td>
                                                    <a href="blog/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('blogDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
