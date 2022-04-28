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
                        <h2 class="content-title card-title">Tüm Siparişler</h2>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTableStandart">
                                <thead>
                                    <tr>
                                        <th scope="col">#ID</th>
										<th scope="col">Sipariş No</th>
										<th scope="col">Üye</th>
										<th scope="col">Tutar</th>
										<th scope="col">Durum</th>
										<th scope="col">Sipariş Tarihi</th>
										<th scope="col" class="text-end">Eylem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $key => $row) : ?>
                                        <tr>
                                            <td><?= $row->id ?></td>
                                            <td><?= $row->order_no ?></td>
                                            <td><?= $row->full_name ?></td>
                                            <td><?= number_format($row->odn_price, 2) ?> TL</td>
                                            <td><?= orderStatusPanelView($row->odn_status) ?></td>
                                            <td><?= timeTR($row->buy_at) ?></td>
                                            <td class="text-end">
                                                <a href="order/detail/<?= $row->id ?>" class="btn btn-md rounded font-sm">Detail</a>
                                                <div class="dropdown d-none">
                                                    <a href="order/detail/<?= $row->id ?>" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="order/detail/<?= $row->id ?>">View detail</a>
                                                        <a class="dropdown-item" href="order/detail/<?= $row->id ?>">Edit info</a>
                                                    </div>
                                                </div>
                                                <!-- dropdown //end -->
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>
