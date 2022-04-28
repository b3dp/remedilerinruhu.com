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
                        <h2 class="content-title card-title">Sabit Alanlar</h2>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0" id="dataTableStandart">
                                    <thead>
                                        <tr>
											<th>ID</th> 
											<th>Lokasyon</th> 
											<th>Başlık</th> 
											<th>İçerik</th> 
											<th>İcon</th> 
											<th>Link</th> 
                                            <th>Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($fixed_fields as $row) : ?>
											<tr> 
												<td><?= $row->id ?></td> 
												<td><?= $row->location ?></td> 
												<td><?= $row->title ?></td> 
												<td><?= $row->content ?></td> 
												<td><i class="<?= $row->icon ?> fa-2x featuresBandIcon text-primary"></i></td> 
												<td><b class="badge badge-soft-primary"><a target="_blank" href="<?= $row->url ?>">Link</a></b></td> 
                                                <td>
                                                    <a href="fixedFields/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
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
