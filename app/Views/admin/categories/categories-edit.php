<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('categoryEdit') ?>" onsubmit="return false">
                <input type="hidden" name="id" value="<?= $categoriesFind->id ?>">
                <input type="hidden" value="<?= $type ?>" name="type">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $categoriesFind->title ?> Kategori Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kategori Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kategori Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" value="<?= $categoriesFind->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Kategori Açıklaması</label>
                                        <textarea placeholder="Açıklama" name="description" class="form-control" rows="4"><?= $categoriesFind->description ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-4">  
                                                <label class="form-label">Kategori Komisyon Oranı</label>
                                                <input type="text" name="commission_rate" class="form-control" value="<?= $categoriesFind->commission_rate ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kategori Resmi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($categoriesFind->menuPicture) : ?>
                                            <div class="col-md-3 col-6 col-6 mb-5" id="deleteItemArea-<?= $categoriesFind->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/category/menuPicture/<?= $categoriesFind->menuPicture ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $categoriesFind->id ?>','<?= base_url('deleteCategoryImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$categoriesFind->menuPicture) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="menuPicture" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Üst Kategori</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                        <div class="col-sm-12 mb-3">
                                            <div class="form-group mt-5">  
                                                <label class="col-form-label required fw-bold fs-6">Üst Kategori Seçimi</label>       
                                                <input class="form-control form-control-solid mb-3 mb-lg-0" id="deliverable_search" type="text" placeholder="Kategori Ara">                                     
                                                <input type="hidden" name="parent_id" id="parent_id" value="<?= $categoriesFind->parent_id ?>">
                                                <div id="tree" class="mt-3">Yükleniyor</div>
                                            </div>
                                        </div>
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Kategori Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $categoriesFind->is_active == '1' ? 'selected' : ''  ?>>Evet Kategori Yayınlansın.</option>
                                                <option value="0" <?= $categoriesFind->is_active == '0' ? 'selected' : '' ?> <?= $categoriesFind->is_active != '1' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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

        $(document).ready(function () {
            var $tree = $('#tree');
            var $categoriesListJson = '[ { "id": "0", "text": "Ana Kategori", "parent" : "#", "state": { "opened": true <?= $categoriesFind->parent_id == '0' ? ', "selected": true' : '' ?> }}, <?= rtrim($categoriesList, ',') ?>]';
            var resetting = false;
            $('#tree').jstree({
               
                "core": {
                    "data": JSON.parse($categoriesListJson)
                },
                'checkbox': {
                    three_state: false,
                    cascade: 'none'
                },
                "select_node": '0',
                "types" : {
                    "default" : {
                        "icon" : "fa fa-angle-right fa-fw"
                    },
                    "demo" : {
                        "icon" : "fa fa-angle-right fa-fw"
                    }
                },
                "plugins": [
                    "types",
                    "wholerow",
                    "checkbox" 
                ]
            })
            $('#tree').on('changed.jstree', function (e, data) {
                if (resetting) //ignoring the changed event
                {
                    resetting = false;
                    return;
                }
                if (!$("#multiselect").is(':checked') && data.selected.length > 1) {
                    resetting = true; //ignore next changed event
                    data.instance.uncheck_all(); //will invoke the changed event once
                    data.instance.check_node(data.node/*currently selected node*/);
                    return;
                }else if (data.selected.length < 1) {

                } 
                var parent_id = data.instance.get_node(data.selected).id;
                $('#parent_id').val(parent_id);
            });
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
                        required: "Lütfen kategori için bir başlık giriniz.",
                    }
                },  
                submitHandler: function() {
                    var action = $('#mini_form').attr('action');
                    for ( instance in CKEDITOR.instances )
                    CKEDITOR.instances[instance].updateElement();
                    miniSubmit(''+ action +'', 'categories/list', '1');
                }
            });
        }, false);

</script>

<?= view("admin/inculude/body_end") ?>
