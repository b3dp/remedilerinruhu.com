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
                        <h2 class="content-title card-title">Kategori Listesi</h2>
                    </div>
                    <div>
                        <a href="categories/add<?= $typeLink ?>" class="btn btn-primary btn-sm rounded">Yeni Kategori Ekle</a>
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
                                            <th>Açıklama</th>
                                            <th>Sıralama</th>
                                            <th>Durum</th>
                                            <th>Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categoriesList as $key => $row) : ?>
                                            <?php 
                                                $topCatArray = '';
                                                $topCatName = '';
                                                if ($row->parent_id == '0') {
                                                    $firstTopCat = $row->title;
                                                }
                                            
                                                if ($row->parent_id != 0) {
                                                    $category->veri = array();
                                                    $topCatArray = array_reverse($category->c_top_all_list('', $row->parent_id)); 
                                                    foreach ($topCatArray as $item) {
                                                    
                                                        $topCatName .= $item['title'] . ' > ' ;
                                                    }
                                                }
                                            ?>
                                            <tr style="vertical-align: middle;">
                                                <td><?= $row->id ?></td>
                                                <td><img style=" max-width: 100px;" class="img-fluid" src="<?= base_url('uploads/category/menuPicture/'.$row->menuPicture.'') ?>" alt=""></td>
                                                <td><?= $topCatName . $row->title ?></td>
                                                <td><?= $row->description ?></td>
                                                <td><?= $row->rank ?></td>
                                                <td>
                                                    <div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
                                                        <input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('statusCategories') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="categories/edit/<?= $row->id ?><?= $typeLink ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('categoryDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
