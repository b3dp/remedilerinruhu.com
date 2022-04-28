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
                        <h2 class="content-title card-title">Kargo Ayarları</h2>
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
											<th>Başlık</th> 
											<th>Teslimat Süresi</th> 
											<th>Ücretsiz Kargo</th> 
											<th>Kargo Ücreti</th>
                                            <th>Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($delivery_options as $row) : ?>
											<tr> 
												<td><b class="badge badge-soft-primary"><?= $row->id ?></b></td> 
												<td><b class="badge badge-soft-primary"><?= $row->title ?></b></td> 
												<td><b class="badge badge-soft-primary"><?= $row->shipping_time ?></b></td> 
												<td><b class="badge badge-soft-primary"><?= number_format($row->free_shipping_price, 2) ?> TL</b></td> 
												<td><b class="badge badge-soft-primary"><?= number_format($row->shipping_price, 2) ?> TL</b></td> 
                                                <td>
                                                    <a href="cargoSetting/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
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
