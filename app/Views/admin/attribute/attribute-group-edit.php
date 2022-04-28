<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('attributeGroupEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $attributeGroupFind->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $attributeGroupFind->title ?> Nitelik Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Nitelik Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Nitelik Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" value="<?= $attributeGroupFind->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Açıklama</label>
                                        <textarea placeholder="Açıklama" name="description" class="form-control" rows="4"><?= $attributeGroupFind->description ?></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Nitelik Türü</label>
                                        <select class="form-select" name="group_type" aria-label="Select example">
                                            <option value="select" <?= $attributeGroupFind->group_type == 'select' ? 'selected' : '' ?>>Açılır Liste</option>
                                            <option value="radio" <?= $attributeGroupFind->group_type == 'radio' ? 'selected' : '' ?>>Radyo Düğmeleri</option>
                                            <option value="color" <?= $attributeGroupFind->group_type == 'color' ? 'selected' : '' ?>>Renk Kodu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Nitelik</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Nitelik Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $attributeGroupFind->is_active == '1' ? 'checked' : '' ?> selected>Evet Nitelik Yayınlansın.</option>
                                                <option value="0" <?= $attributeGroupFind->is_active == '0' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
                                            </select>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/js/vendors/ckeditor/ckeditor.js"></script>

<script>

    window.addEventListener('load', function() {    
        var form = $('#mini_form');
        form.validate({
            errorPlacement: function(label, element) {
                label.addClass('arrow');
                label.insertAfter(element);
            },
            wrapper: 'span',

            rules: {
                title: "required",
                group_type: "required",
            },
            // Specify validation error messages
            messages: {
                title: {
                    required: "Lütfen Nitelik için bir başlık giriniz.",
                },
                group_type: {
                    required: "Lütfen Niteliğin türünü belirleyiniz.",
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form').attr('action');

                miniSubmit(''+ action +'', 'attribute/group-list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
