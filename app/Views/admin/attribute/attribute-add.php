<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('attributeAdd') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="group_id" value="<?= $attributeGroup->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $attributeGroup->title ?> Özellik Ekle</h2>
                                <div>
                                    <button type="submit" class="btn btn-facebook rounded font-sm hover-up" id="saveAndRefresh">Kaydet ve Yeni Değer Ekle</button>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4><?= $attributeGroup->title ?> Özellik Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Özellik Değeri</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" />
                                    </div>
                                    
                                    <?php if ($attributeGroup->is_color == '1') : ?>
                                        <div class="mb-4">
                                            <label class="form-label">Özellik Renk Kodu</label>
                                            <input type="color"  name="color" class="form-control" placeholder="Özellik Renk Kodu" value="" />
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Özellik </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="form-label">Özellik Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="2" <?= $productFind->is_active == '2' ? 'selected' : ''  ?> selected>Evet Soru Yayınlansın.</option>
                                                <option value="0" <?= $productFind->is_active == '0' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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
            },
            // Specify validation error messages
            messages: {
                title: {
                    required: "Lütfen niteliğin değerini giriniz.",
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form').attr('action');
                if (this.submitButton.id === 'saveAndRefresh') {
                    miniSubmit(''+ action +'', '', '1');
                }else if (this.submitButton.id === 'saveAndReturn') {
                    miniSubmit(''+ action +'', 'attribute/list/<?= $attributeGroup->id ?>', '1');
                }
                
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
