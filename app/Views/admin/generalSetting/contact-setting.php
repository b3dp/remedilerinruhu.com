<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('generalSettingsEdit') ?>" onsubmit="return false" class="form">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">İletişim Ayarları</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>İletişim Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($generalSettings as $type => $grup){ ?>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="mb-4">
                                                    <label class="form-label" for="site_baslik">
                                                        <?=$grup->title?>
                                                    </label>
                                                    <?php if($grup->form_type == 'text'){ ?>
                                                        <input type="text" class="form-control"  name="contact[<?=$grup->name?>]" value="<?=$grup->value?>">
                                                    <?php } ?>
                                                    <?php if($grup->form_type == 'textarea'){ ?>
                                                        <textarea  name="contact[<?=$grup->name?>]" class="form-control" cols="10" rows="5"><?=$grup->value?></textarea>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
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
                    required: "Lütfen kategori için bir başlık giriniz.",
                }
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');

                miniSubmit('' + action + '', 'custom_noreload', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
