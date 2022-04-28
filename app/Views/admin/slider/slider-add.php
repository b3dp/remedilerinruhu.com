<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('sliderAdd') ?>" onsubmit="return false" class="form" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <input type="hidden" value="0" name="product_type">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Yeni Slider Ekle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Slider Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" name="title" class="form-control" placeholder="" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="col-form-label fw-bold fs-6">Link</label>
                                        <input type="text" name="link"  class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="https://" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Masaüstü Görsel (<?= $slider_desktop_w ?>x<?= $slider_desktop_h ?> Önerilen Boyut)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="input-upload">
                                        <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <input class="form-control" type="file" name="pictureDesktop" accept=".png, .jpg, .jpeg, .webp" />
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Mobile Görsel (<?= $slider_mobile_w ?>x<?= $slider_mobile_h ?> Önerilen Boyut)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="input-upload">
                                        <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <input class="form-control" type="file" name="pictureMobile" accept=".png, .jpg, .jpeg, .webp" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Slider</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Slider Görseli Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $productFind->is_active == '1' ? 'selected' : ''  ?> selected>Evet slider Yayınlansın.</option>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                pictureDesktop: "required",
                pictureMobile: "required",
            },
            // Specify validation error messages
            messages: {
                title: {
                    required: "Lütfen slider ile ilgili bir başlik giriniz.",
                },
                pictureDesktop: {
                    required: "Lütfen slider için masaüstü resmi seçiniz.",
                },
                pictureMobile: {
                    required: "Lütfen slider için mobile resmi seçiniz.",
                },
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');

                miniSubmit('' + action + '', 'slider/list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
