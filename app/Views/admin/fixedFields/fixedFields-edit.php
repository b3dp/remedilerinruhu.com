<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('fieldsEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $fixed_fields->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Sabit Alanlar Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Marka Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-6">
                                        <div class="col-lg-12">
                                            <div class="mb-4">  
                                                <label class="form-label">Başlık</label>
                                                <input type="text" name="title"
                                                    class="form-control"
                                                    placeholder="" value="<?= $fixed_fields->title ?>" />
                                            </div>
                                            <div class="mb-4">  
                                                <label class="form-label">İcerik</label>
                                                <textarea type="text" name="content"
                                                    class="form-control"
                                                    placeholder=""><?= $fixed_fields->content ?></textarea>
                                            </div>
                                            <div class="mb-4">  
                                                <label class="form-label">İcon</label>
                                                <input type="text" name="icon"
                                                    class="form-control"
                                                    placeholder="" value="<?= $fixed_fields->icon ?>" />
                                                    <small>Başka bir ikon secmek için <a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2">Fontawesome</a> Adresinden icon classlarını alabılırsınız.</small>
                                            </div>
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
                    required: "Lütfen marka için bir başlık giriniz.",
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form').attr('action');
                for ( instance in CKEDITOR.instances )
                CKEDITOR.instances[instance].updateElement();
                miniSubmit(''+ action +'', 'brand/list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
