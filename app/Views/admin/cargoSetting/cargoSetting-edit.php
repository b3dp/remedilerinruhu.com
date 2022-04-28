<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('cargoEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $delivery_options->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Kargo Ayarları Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kargo Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">  
                                        <label class="form-label">Başlık</label>
                                        <input type="text" name="title" class="form-control" placeholder="" value="<?= $delivery_options->title ?>" />
                                    </div>
                                    <div class="mb-4">  
                                        <label class="form-label">Teslimat Süresi</label>
                                        <input type="text" name="shipping_time"
                                            class="form-control"
                                            placeholder="" value="<?= $delivery_options->shipping_time ?>" />
                                    </div>
                                    <div class="mb-4">  
                                        <label class="form-label">Ücretsiz Kargo</label>
                                        <input type="text" name="free_shipping_price"
                                            class="form-control"
                                            placeholder="" value="<?= $delivery_options->free_shipping_price ?>" />
                                    </div>
                                    <div class="mb-4">  
                                        <label class="form-label">Kargo Ücreti</label>
                                        <input type="text" name="shipping_price"
                                            class="form-control"
                                            placeholder="" value="<?= $delivery_options->shipping_price ?>" />
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

    window.addEventListener('load', function () {   
        var form = $('#mini_form');
        form.validate({
            errorPlacement: function (label, element) {
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
                    required: "Lütfen kargo için bir başlık giriniz.",
                },
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                miniSubmit('' + action + '', 'cargoSetting/list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
