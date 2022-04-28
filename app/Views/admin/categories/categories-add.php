<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('categoryAdd') ?>" onsubmit="return false">
                <input type="hidden" value="<?= $type ?>" name="type">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Yeni Kategori Ekle</h2>
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
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Kategori Açıklaması</label>
                                        <textarea placeholder="Açıklama" name="description" class="form-control" rows="4"></textarea>
                                    </div>
                                    <?php if ($type == 'product') : ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-4">  
                                                    <label class="form-label">Kategori Komisyon Oranı</label>
                                                    <input type="text" name="commission_rate" class="form-control" value="" />
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kategori Resmi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="input-upload">
                                        <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <input class="form-control" type="file" name="menuPicture" />
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
                                                <input type="hidden" name="parent_id" id="parent_id">
                                                <div id="tree" class="mt-3">Yükleniyor</div>
                                            </div>
                                        </div>
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Kategori Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" selected >Evet Kategori Yayınlansın.</option>
                                                <option value="0" >Hayır taslak olarak bırak.</option>
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

        var myVar;
            $(document).ready(function () {
                var $tree = $('#tree');
                var $categoriesListJson = '[ { "id": "0", "text": "Ana Kategori", "parent" : "#", "state": { "opened": true }} <?= $categoriesList ? ','.rtrim($categoriesList, ',') : '' ?>]';
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
                        "search",
                        "types",
                        "wholerow",
                        "checkbox" 
                    ]
                });
                $('#tree').on('loaded.jstree', function(e, data) {
                    data.instance.check_node(data.node);
                });
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
                $('#deliverable_search').keyup(function(){
                    $('#tree').jstree(true).show_all();
                    $('#tree').jstree('search', $(this).val());
                });
            });

            function GetSelectedMenu() {
                var menuX = '';
                var menuXCategories = '';
                $('.menux .jstree-anchor,.menux .jstree-checkbox').each(function () {
                    if ($(this).find('.jstree-undetermined').length > 0 || $(this).hasClass('jstree-clicked')) {
                        menuX += $(this).parents('li').attr('id') + ',';
                    }
                });
                var parent_id = menuX;
                $('#parent_id').val(parent_id);
                clearTimeout(myVar);
            }

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
                    miniSubmit(''+ action +'', 'categories/list<?= $typeLink ?>', '1');
                }
            });
        }, false);

</script>

<?= view("admin/inculude/body_end") ?>
