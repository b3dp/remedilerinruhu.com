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
                        <h2 class="content-title card-title">Dashboard</h2>
                        <p>Whole data about your business here</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-primary-light d-flex"><i class="text-primary material-icons md-monetization_on"></i></span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Revenue</h6>
                                    <span>$13,456.5</span>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-success-light d-flex"><i class="text-success material-icons md-local_shipping"></i></span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Orders</h6>
                                    <span>53.668</span>
                                    <span class="text-sm"> Excluding orders in transit </span>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-warning-light d-flex"><i class="text-warning material-icons md-qr_code"></i></span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Products</h6>
                                    <span>9.856</span>
                                    <span class="text-sm"> In 19 Categories </span>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-info-light d-flex"><i class="text-info material-icons md-shopping_basket"></i></span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Monthly Earning</h6>
                                    <span>$6,982</span>
                                    <span class="text-sm"> Based in your local time. </span>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <header class="card-header">
                        <h4 class="card-title">Son Siparişler</h4>
                    </header>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="align-middle" scope="col">Sipariş No</th>
                                            <th class="align-middle" scope="col">Üye</th>
                                            <th class="align-middle" scope="col">Tutar</th>
                                            <th class="align-middle" scope="col">Durum</th>
                                            <th class="align-middle" scope="col">Sipariş Tarihi</th>
                                            <th class="align-middle" scope="col">Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $key => $row) : ?>
                                            <tr>
                                                <td><a href="order/detail/<?= $row->id ?>" class="fw-bold">#<?= $row->order_no ?></a></td>
                                                <td><?= $row->full_name ?></td>
                                                <td><?= number_format($row->odn_price, 2) ?> TL</td>
                                                <td><?= orderStatusPanelView($row->odn_status) ?></td>
                                                <td><?= timeTR($row->buy_at) ?></td>
                                                <td>
                                                    <a href="order/detail/<?= $row->id ?>" class="btn btn-xs">Detay</a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table-responsive end// -->
                    </div>
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>
