<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/public/admin/assets/css/tagify.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('blogEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $blog->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $blog->title ?> Blog Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Blog Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" name="title" class="form-control" placeholder="" value="<?= $blog->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Kısa Açıklama</label>
                                        <input type="text" name="short_description" class="form-control" placeholder="" value="<?= $blog->short_description ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">İçerik</label>
                                        <textarea name="description" id="description" class="form-control"><?= $blog->description ?></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Blog Etiketleri</label>
                                        <input class="form-control" name="tags" value="<?= $blog->tag ?>" id="kt_tagify_1"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Blog Kapak Görseli ( <?= $blog_cover_w ?> x <?= $blog_cover_h ?> Önerilen Boyut )</h4>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($blog->picture_cover) : ?>
                                            <div class="col-md-9 mb-5" id="deleteItemArea-<?= $blog->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/blog/cover/<?= $blog->picture_cover ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $blog->id ?>','<?= base_url('blogCoverDeleteImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$blog->picture_cover) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="pictureCover" accept=".png, .jpg, .jpeg, .webp" />
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Blog Görseli ( <?= $blog_w ?> x <?= $blog_h ?> Önerilen Boyut )</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($blog->picture) : ?>
                                            <div class="col-md-9 mb-5" id="deleteItemAreaTwo-<?= $blog->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/blog/<?= $blog->picture ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $blog->id ?>','<?= base_url('blogDeleteImg') ?>', 'deleteItemValue')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$blog->picture) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="picture" accept=".png, .jpg, .jpeg, .webp"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Seo Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="seo_title" class="form-label">Seo Başlığı</label>
                                        <input type="text" name="seo_title" placeholder="Seo Başlık" class="form-control" id="seo_title" value="<?= $blog->seo_title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Seo Açıklaması</label>
                                        <textarea placeholder="Seo Açıklama" name="seo_description" class="form-control" rows="4"><?= $blog->seo_description ?></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="slug" class="form-label">Kullanıcı dostu URL</label>
                                        <input type="text" name="slug" placeholder="Blogun Gözükeceği Link" class="form-control" id="slug" value="<?= $blog->slug ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Blog</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Blog Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $productFind->is_active == '1' ? 'selected' : ''  ?> selected>Evet Blog Yayınlansın.</option>
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
<script src="<?= base_url() ?>/public/admin/assets/js/tagify.min.js"></script>
<script>

    CKEDITOR.replace('description', {
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

    var input1 = document.querySelector("#kt_tagify_1");
    new Tagify(input1);

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
                    required: "Lütfen blog için bir başlık giriniz.",
                },
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');
                CKEDITOR.instances['description'].updateElement();
                miniSubmit('' + action + '', 'blog/list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
