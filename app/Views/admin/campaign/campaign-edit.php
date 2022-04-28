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
            <form id="mini_form" action="<?= base_url('campaignEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $campaign->id ?>">
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
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" value="<?= $campaign->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="discount" class="form-label">İndirim Oranı</label>
                                        <input type="text" name="discount" placeholder="İndirim Oranı" class="form-control" id="discount" value="<?= $campaign->discount ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="start_at" class="form-label">Kampanya Başlangıç Tarihi</label>
                                        <input type="date" name="start_at"  class="form-control" id="start_at" value="<?= $campaign->start_at ?>"/>
                                    </div>
                                    <div class="mb-4">
                                        <label for="end_at" class="form-label">Kampanya Bitiş Tarihi</label>
                                        <input type="date" name="end_at" class="form-control" id="end_at" value="<?= $campaign->end_at ?>"/>
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
                                                <option <?= in_array($row->id, $categorArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $topCatName . $row->title ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">  
                                        <label class="align-items-center col-form-label d-flex">Kampanya'nın Geçerli Olduğu Markalar
                                            <i class="fs-6 material-icons md-info ms-2" data-bs-toggle="tooltip" title="Tüm Markalarda geçerli olacak ise seçim yapmayın."></i>
                                        </label>
                                        <select name="brand_id[]" class="form-select select2" data-placeholder="Lütfen kampanyaya ait markaları seçiniz." multiple data-control="select2" >
                                                <?php foreach ($brands as $row) : ?>
                                                    <option <?= in_array($row->id, $brandArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
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
                                    <div class="d-flex draggable-zone justify-content-center row" id="servicesPictureArea">
                                        <?php if ($campaign->image) : ?>
                                            <div class="col-md-3 col-6 col-6 mb-5" id="deleteItemArea-<?= $campaign->id ?>">
                                                <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                                    
                                                    <div class="image-input-wrapper h-200px w-150px" >
                                                        <img src="<?= base_url() ?>/uploads/campaigns/<?= $campaign->image ?>" alt="" />
                                                    </div>

                                                    <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('<?= $campaign->id ?>','<?= base_url('campaignDeleteImg') ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                                        <i class="icon material-icons md-clear"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="input-upload">
                                        <?php if (!$campaign->image) : ?>
                                            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                        <?php endif ?>
                                        <input class="form-control mt-3" type="file" name="image" />
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
                                                <option value="1" <?= $campaign->is_active == '1' ? 'selected' : ''  ?> selected>Evet Kampanya Yayınlansın.</option>
                                                <option value="0" <?= $campaign->is_active == '0' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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
                    required: "Lütfen kampanya için bir başlık giriniz.",
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
