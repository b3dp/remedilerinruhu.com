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
                        <h2 class="content-title card-title">Ürün Listesi</h2>
                    </div>
                    <div>
                        <a href="product/add" class="btn btn-primary btn-sm rounded">Yeni Ürün Ekle</a>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                       
                            <header class="card-header">
                                <div class="row align-items-center">
                                    <form id="filter_add" class="d-flex" onsubmit="return false">
                                        <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                                            <input type="text" name="text[]" value="<?= $filterArrayVal['text']['0'] ?>" class="form-control form-control-solid w-250px ps-14" placeholder="Ürün Ara" />
                                        </div>
                                        <div class="col-md-2 col-6">
                                            <button onclick="filter_add();" type="button" class="btn btn-secondary btn-sm rounded" style="margin-left:15px">Ara</button>
                                        </div>
                                    </form>
                                </div>
                            </header>
                      
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Resim</th>
                                            <th>Adı</th>
                                            <th>Fiyat(vergi dahil)</th>
                                            <th>Adet</th>
                                            <th class="text-center">Durum</th>
                                            <th class="text-center">Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productList as $key => $row) : ?>
                                            <?php 
                                                $producImage = $productModels->c_one_image(["product_id" => $row->id]);
                                                $producStock = $productModels->productAttrAll(["id_product" => $row->id], "pa.id_product");
                                                foreach ($producStock as $value) {
                                                    $stock = $value->totalStock;
                                                }
                                                if (!isset($stock)) {
                                                    $stock = '0';
                                                }
                                            ?>
                                            <tr>
                                                <td><?= $row->id ?></td>
                                                <td>
                                                    <?php if ($producImage) : ?>
                                                        <img class="img-sm img-thumbnail" src="../uploads/products/min/<?= $producImage->image ?>" alt="">
                                                    <?php endif ?>
                                                </td>
                                                <td><?= $row->title ?></td>
                                                <td><?= $row->sale_price ?></td>
                                                <td><?= $stock ?></td>
                                                <td>
                                                    <div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
                                                        <input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('statusProduct') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="product/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('productDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
