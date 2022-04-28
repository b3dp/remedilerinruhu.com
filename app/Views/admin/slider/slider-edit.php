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
            <form id="mini_form" action="<?= base_url('sliderEdit') ?>" onsubmit="return false" class="form" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $slider->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $slider->title ?> Slider Düzenle</h2>
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
                                        <input type="text" name="title" class="form-control" placeholder="" value="<?= $slider->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="col-form-label fw-bold fs-6">Link</label>
                                        <input type="text" name="link"  class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="https://" value="<?= $slider->link ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Masaüstü Görsel (<?= $slider_desktop_w ?>x<?= $slider_desktop_h ?> Önerilen Boyut)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($slider->pictureDesktop) : ?>
                                            <div class="col-md-9 mb-5" id="deleteItemArea-<?= $slider->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/sliders/<?= $slider->pictureDesktop ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $slider->id ?>','<?= base_url('sliderDesktopDeleteImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$slider->pictureDesktop) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="pictureDesktop" />
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Mobile Görsel (<?= $slider_mobile_w ?>x<?= $slider_mobile_h ?> Önerilen Boyut)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($slider->pictureMobile) : ?>
                                            <div class="col-md-6 mb-5" id="deleteItemTwoArea-<?= $slider->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/sliders/mobile/<?= $slider->pictureMobile ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $slider->id ?>','<?= base_url('sliderMobileDeleteImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$slider->pictureMobile) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="pictureMobile" />
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
                                                <option value="2" <?= $productFind->is_active == '2' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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
            },
            // Specify validation error messages
            messages: {
                title: {
                    required: "Lütfen slider ile ilgili bir başlik giriniz.",
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
