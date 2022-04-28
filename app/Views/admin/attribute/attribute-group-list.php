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
                        <h2 class="content-title card-title">Nitelikler (<?= $attributeCount ?>)</h2>
                    </div>
                    <div>
                        <a href="attribute/group-add" class="btn btn-primary btn-sm rounded">Yeni Nitelik Oluştur</a>
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
                                            <th>Durum</th>
                                            <th>Eylem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attributeList as $key => $row) : ?>
                                            <tr>
                                                <td><?= $row->id ?></td>
                                                <td><?= $row->title ?></td>
                                                <td>
                                                    <div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
                                                        <input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('statusAttributeGroup') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="attribute/group-edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="attribute/list/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-facebook"> <i class="material-icons md-list_alt"></i> Özellik Listesi </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('attributeGroupDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
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
