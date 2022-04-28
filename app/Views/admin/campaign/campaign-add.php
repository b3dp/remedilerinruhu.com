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
            <form id="mini_form" action="<?= base_url('campaignAdd') ?>" onsubmit="return false">
                <input type="hidden" value="0" name="product_type">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Yeni Kampanya Ekle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kampanya Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kampanya Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">İndirim Oranı</label>
                                        <input type="text" name="discount" placeholder="İndirim Oranı" class="form-control" id="product_name" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kampanya Başlangıç Tarihi</label>
                                        <input type="date" name="start_at"  class="form-control" id="product_name" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kampanya Bitiş Tarihi</label>
                                        <input type="date" name="end_at" class="form-control" id="product_name" />
                                    </div>

                                    <div class="form-group mb-4">  
                                        <label class="align-items-center col-form-label d-flex">Kampanya'nın Geçerli Olduğu Kategoriler
                                            <i class="fs-6 material-icons md-info ms-2" data-bs-toggle="tooltip" title="Tüm kategorilerde geçerli olacak ise seçim yapmayın."></i>
                                        </label>
                                        <select name="category_id[]" class="form-select select2" data-placeholder="Lütfen kampanyaya ait kategorileri seçiniz." multiple data-control="select2" >
                                            <?php foreach ($categoriesList as $key => $row) : ?>
                                                <?php 
                                                    $topCatArray = '';
                                                    $topCatName = '';
                                                    if ($row->parent_id == '0') {
                                                        $firstTopCat = $row->title;
                                                    }
                                                
                                                    if ($row->parent_id != 0) {
                                                        $category->veri = array();
                                                        $topCatArray = array_reverse($category->c_top_all_list('', $row->parent_id)); 
                                                        foreach ($topCatArray as $item) {
                                                        
                                                            $topCatName .= $item['title'] . ' > ' ;
                                                        }
                                                    }
                                                ?>
                                                <option value="<?= $row->id ?>"><?= $topCatName . $row->title ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">  
                                        <label class="align-items-center col-form-label d-flex">Kampanya'nın Geçerli Olduğu Markalar
                                            <i class="fs-6 material-icons md-info ms-2" data-bs-toggle="tooltip" title="Tüm Markalarda geçerli olacak ise seçim yapmayın."></i>
                                        </label>
                                        <select name="brand_id[]" class="form-select select2" data-placeholder="Lütfen kampanyaya ait markaları seçiniz." multiple data-control="select2" >
                                                <?php foreach ($brands as $row) : ?>
                                                    <option value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kampanya Görseli ( <?= $campaign_w ?> x <?= $campaign_h ?> Önerilen Boyut )</h4>
                                </div>
                                <div class="card-body">
                                    <div class="input-upload">
                                        <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <input class="form-control" type="file" name="image" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kampanya</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Kampanya Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $productFind->is_active == '1' ? 'selected' : ''  ?> selected>Evet Kampanya Yayınlansın.</option>
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

    $(document).ready(function() {
        $('.select2').select2();
    });

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

                miniSubmit('' + action + '', 'campaign/list', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
