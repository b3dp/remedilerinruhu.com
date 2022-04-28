<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('brandEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $brandFind->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $brandFind->title ?> Marka Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Marka Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">Marka Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="title" value="<?= $brandFind->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Marka Açıklaması</label>
                                        <textarea placeholder="Açıklama" name="description" class="form-control" rows="4"><?= $brandFind->description ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Marka Resmi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($brandFind->image) : ?>
                                            <div class="col-md-3 col-6 col-6 mb-5" id="deleteItemArea-<?= $brandFind->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/brand/<?= $brandFind->image ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $brandFind->id ?>','<?= base_url('brandDeleteImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$brandFind->image) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="logo" />
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Marka Seo Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="seo_title" class="form-label">Marka Seo Başlığı</label>
                                        <input type="text" name="seo_title" placeholder="Seo Başlık" class="form-control" id="seo_title" value="<?= $brandFind->seo_title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Marka Seo Açıklaması</label>
                                        <textarea placeholder="Seo Açıklama" name="seo_description" class="form-control" rows="4"><?= $brandFind->seo_description ?></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="slug" class="form-label">Kullanıcı dostu URL</label>
                                        <input type="text" name="slug" placeholder="Markanın Gözükeceği Link" class="form-control" id="slug" value="<?= $brandFind->slug ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Marka</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Marka Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $brandFind->is_active == '1' ? 'selected' : ''  ?> selected>Evet Marka Yayınlansın.</option>
                                                <option value="0" <?= $brandFind->is_active == '0' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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

        CKEDITOR.replace('description', {
            toolbar: [ 																				// Line break - next group will be placed in new line.
                { name: 'basicstyles', items: [ 'Bold', 'Italic' ] }
            ],
            wordcount : {
                // Whether or not you want to show the Paragraphs Count
                showParagraphs: true,

                // Whether or not you want to show the Word Count
                showWordCount: false,

                // Whether or not you want to show the Char Count
                showCharCount: true,

                // Whether or not you want to count Spaces as Chars
                countSpacesAsChars: true,

                // Whether or not to include Html chars in the Char Count
                countHTML: false,

                // Maximum allowed Word Count, -1 is default for unlimited
                maxWordCount: 1,

                // Maximum allowed Char Count, -1 is default for unlimited
                maxCharCount: 2000,

                maxParagraphs: 15,
            },
        });

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
