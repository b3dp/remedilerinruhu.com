<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" onsubmit="return false">
                <input type="hidden" value="0" name="product_type">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Yeni Ürün Ekle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Ürün Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Ürün Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name_alt" class="form-label">Ürün Alt Başlığı</label>
                                        <input type="text" name="alt_title" placeholder="Alt Başlık" class="form-control" id="product_name_alt" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Ürün Açıklaması</label>
                                        <textarea placeholder="Açıklama" name="description" id="description" class="form-control" rows="4"></textarea>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="mb-4">  
                                                <label class="form-label">Barkod (ISBN, UPC, GTIN vb.)</label>
                                                <input type="text" name="barcode_no" class="form-control" value="" />
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-4">  
                                                <label class="form-label">Stok</label>
                                                <input type="text" name="stock" class="form-control" value="" />
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-4">
                                            <label class="form-label">Miat Süresi</label>
                                            <input class="form-control" type="date" name="expiration_date"> 
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="mb-4">  
                                                <label class="form-label">MF Gerekliliği</label>
                                                <input type="text" name="mf_required" class="form-control" value="<?= $productCombination['0']->mf_required ?>" />
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-4">  
                                                <label class="form-label">MF Sayısı</label>
                                                <input type="text" name="mf_count" class="form-control" value="<?= $productCombination['0']->mf_count ?>" />
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div id="coupon_type_1_area">
                                                <div class="d-grid mb-4">  
                                                    <label class="form-label">MF Ürünleri</label>
                                                    <select name="mf_product[]" class="form-select form-select-solid" data-placeholder="Lütfen MF için ürünleri seçiniz." multiple data-control="select2" >
                                                        <?php foreach ($product as $key => $row) : ?>
                                                            <option <?= in_array($row->id, $productArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="col-lg-12 mb-4">
                                            <div id="coupon_type_1_area">
                                                <div class="d-grid mb-4">  
                                                    <label class="form-label">Benzer Ürün Seçimi</label>
                                                    <select name="similar_product[]" class="form-select form-select-solid" data-placeholder="Lütfen 4 adet benzer ürün seçimi yapınız." data-maximum-selection-length="4" multiple data-control="select2" >
                                                        <?php foreach ($product as $key => $row) : ?>
                                                            <option <?= in_array($row->id, $similarProductArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div> 
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>PSF Fiyatlandırması</h4>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">  
                                                <label class="form-label">Vergi Miktarı</label>
                                                <select name="standart_kdv_rate" id="standart_kdv_rate" onchange="this.value == '-1' ? $('.specialKdvRateArea').removeClass('d-none') : $('.specialKdvRateArea').addClass('d-none') " class="form-select">
                                                    <option value="8" <?= $productFind->tax_rate == '8' ? 'selected' : '' ?>>KDV Oranı %8 </option>
                                                    <option value="18" <?= $productFind->tax_rate == '18' ? 'selected' : '' ?>>KDV Oranı %18</option>
                                                    <option value="1" <?= $productFind->tax_rate == '1' ? 'selected' : '' ?>>KDV Oranı %1</option>
                                                    <option value="0" <?= $productFind->tax_rate == '0' ? 'selected' : '' ?>>KDV Yok</option>
                                                    <option value="-1" <?= $productFind->tax_rate != '1' || $productFind->tax_rate != '18' || $productFind->tax_rate != '1' || $productFind->tax_rate != '0' ? '' : 'selected'  ?>>Özel KDV</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-4 specialKdvRateArea d-none">  
                                                <label class="form-label">Özel KDV Değeri</label>
                                                <input type="text" name="special_kdv_rate" id="special_kdv_rate"
                                                class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                placeholder="Özel KDV oranınızı giriniz." value="<?= $productCombination['0']->tax_rate ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">  
                                                <label class="form-label">PSF Fiyatı (KDV Hariç)</label>
                                                <input type="number" name="sale_price" id="discount_price"
                                                class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                placeholder="PSF Bilgisini Giriniz." value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">  
                                                <label class="form-label">PSF Fiyatı (KDV Dahil)</label>
                                                <input type="number" name="sale_price_kdv" id="discount_price_kdv"
                                                class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                placeholder="PSF Fiyat Bilgisini Giriniz." value="<?= $productCombination['0']->sale_price ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group mb-4">  
                                                <label for="max_sale" class="form-label">Maks Satış Adedi</label>
                                                <input type="text" name="max_sale" placeholder="Maks Satış Adedi" class="form-control" id="max_sale" value="<?= $productCombination['0']->max_sale ?>" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Satış Fiyatlandırması</h4>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($c_all_type as $row) : ?>
                                        <div class="row">
                                            <div class="align-items-center col-md-3 d-flex">
                                                <div class="form-group">  
                                                    <label class="form-label"><?= $row->name ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-4">  
                                                    <label class="form-label">Satiş Fiyati (KDV Hariç)</label>
                                                    <input type="number" name="discount_price[<?= $row->id ?>]" id="sale_price_<?= $row->id ?>" data-id="<?= $row->id ?>"
                                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 sale_price"
                                                    placeholder="Satiş Fiyat Bilgisini Giriniz." value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-4">  
                                                    <label class="form-label">Satiş Fiyati (KDV Dahil)</label>
                                                    <input type="number" name="discount_price_kdv[<?= $row->id ?>]" id="sale_price_kdv_<?= $row->id ?>" data-id="<?= $row->id ?>"
                                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 sale_price_kdv"
                                                    placeholder="Satiş Fiyat Bilgisini Giriniz." value="<?= $productPrice[$row->id] ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach ?>                       
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Ürün Resimleri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="servicesPictureArea">
                                    </div>
                                    <div class="input-upload">
                                        <div class="dropzone" id="productImajeArea">
                                            <div class="dz-message needsclick">
                                                <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/upload.svg" alt="" />
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Ürün Resimlerinizi buradan sürükle birak ile ekleyebilir yada seçerek ekleye bilirsiniz.</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kategori ve Marka</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                        <div class="col-sm-12 mb-3">
                                            <div class="form-group mt-5">  
                                                    <label class="col-form-label required fw-bold fs-6">Kategori Seçimi</label>       
                                                    <input class="form-control form-control-solid mb-3 mb-lg-0" id="deliverable_search" type="text" placeholder="Kategori Ara">                                     
                                                    <input type="hidden" name="parent_id" id="parent_id">
                                                    <div class="menux mt-3"></div>
                                                </div>
                                        </div>

                                        <div class="col-sm-12 mb-3">
                                            <div class="form-group mt-5">  
                                                <label class="col-form-label  fw-bold fs-6">Marka Seçimi</label>
                                                <select name="brand_id" class="form-select form-select-solid">
                                                    <option value="0">Ürününüzün markasını buradan seçebilirsiniz.</option>
                                                    <?php foreach ($brandList as $row) : ?>
                                                        <option value="<?= $row->id ?>"><?= $row->title ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>

                                        <?php foreach ($attributeGroupList as $row) : ?>
                                            <?php $attributList =  $attributeModels->c_all(["a.is_active" => '2', 'ag.id' => $row->id]); ?>
                                            <div class="col-sm-12 mb-3">
                                                <div class="form-group mt-5">  
                                                    <label class="col-form-label  fw-bold fs-6"><?= $row->title ?></label>
                                                    <select name="productFeature[<?= $row->id ?>]" class="form-select form-select-solid" data-control="select2" >
                                                        <option value="0"><?= $row->title ?> Seçebilirsinizç</option>
                                                        <?php foreach ($attributList as $item) : ?>
                                                            <option value="<?= $item->id ?>"><?= $item->title ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endforeach ?>

                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Ürün Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $productFind->is_active == '1' ? 'selected' : ''  ?>>Evet Ürün Yayınlansın.</option>
                                                <option value="0" <?= $productFind->is_active == '0' ? 'selected' : '' ?> <?= $productFind->is_active != '1' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Ürünün Satışı Yapılsın mı?</label>
                                            <select name="is_active_sell" class="form-select">
                                                <option value="1" <?= $productCombination['0']->is_active == '1' ? 'selected' : ''  ?>>Evet Bu ürünü satmak istiyorum.</option>
                                                <option value="0" <?= $productCombination['0']->is_active == '0' ? 'selected' : '' ?> <?= $productFind->is_active != '1' ? 'selected' : '' ?> >Hayır ürün satışını yapmayacağım.</option>
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

    $(document).ready(function() {
        $('[data-control="select2"]').each(function(){
            var placeholder = $(this).attr('data-placeholder')
            placeholder = (placeholder === undefined) ? '' : placeholder;
            $(this).select2();
        });
    });

    CKEDITOR.replace('description', {
        wordcount: {
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

    var dropzoneReferance = new Dropzone('#productImajeArea', {
        url: "<?= base_url("prodcutIMGUpload"); ?>",
        thumbnailHeight: 120,
        thumbnailWidth: 120,
        maxFilesize: 2000,
        parallelUploads: 800,
        maxFiles: 8,
        addRemoveLinks: true,
        processingmultiple: true,
        error:function(file, response){
            error = false;
        },
        success:function(file, response){
            success = false;
        },
        timeout : 900000,
        dictRemoveFile : "Kaldır",
        acceptedFiles: "image/*",
        removedfile: function(file) {
        var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init:function(){
            var self = this;
            self.options.addRemoveLinks = true;
            self.options.dictRemoveFile = "Kaldır";

            self.on("success", function(file, response) {
                response = JSON.parse(response);
                if (response.error) {
                    self.defaultOptions.error(file, cevap.hata);  
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    toast({
                        type: 'error',
                        title: cevap.hata,
                        padding: '2em',
                    })
                    self.removeFile(file);
                }else{
                    self.defaultOptions.success(file);  
                    var content = '';
                    content = `
                        <div class="col-md-3 col-6 col-6 mb-5" id="deleteItemArea-${response.uploadID}">
                            <div class="image-input image-input-outline" data-kt-image-input="true" style="position: relative;">
                                
                                <div class="image-input-wrapper h-200px w-150px" >
                                    <img src="<?= base_url() ?>/${response.upload}" alt="" />
                                </div>
                                <!--
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Kapak Resmi Yap">
                                        <i class="bi bi-bookmark fs-7"></i>
                                    </label>
                                -->
                                <span style="position: absolute; right: -10px; bottom: -5px;border-radius: 50%;" onclick="miniSingle('${response.uploadID}','<?= base_url("deleteProductImg") ?>', 'deleteItem')" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                    <i class="icon material-icons md-clear"></i>
                                </span>
                            </div>
                        </div>
                    `;
                    $("#servicesPictureArea").append(content);
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    toast({
                        type: 'success',
                        title: response.success,
                        padding: '2em',
                    })
                    self.removeFile(file);
                }
            })
        }
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
                    required: "Lütfen ürünün başlığını belirleyiniz.",
                }
            },
            submitHandler: function () {
                CKEDITOR.instances['description'].updateElement();
                miniSubmitConfirmation('<?= base_url('productInsert'); ?>' , 'custom_reload_ajax', '1')
            }
        });
    }, false);

</script>

<script>

    function kvdPrice(thisPrice, kdvRate) {
        var toplamTutar = thisPrice / parseFloat(1 + kdvRate / 100);
        const dec = String(toplamTutar).split('.')[1]
        const len = dec && dec.length > 2 ? dec.length : 2
        const lenPar = len && len > 5 ? 6 : len
        if (toplamTutar.toFixed(lenPar) === "NaN") {
            toplamTutar = "0.00";
        }
        console.log(toplamTutar);
        return toplamTutar.toFixed(lenPar);
    }

    $('.sale_price').keyup(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }
        var toplamTutar = this.value * parseFloat(1 + kdvRate / 100);
        const dec = String(toplamTutar).split('.')[1]
        const len = dec && dec.length > 2 ? dec.length : 2
        const lenPar = len && len > 5 ? 6 : len
        if (toplamTutar.toFixed(len) === "NaN") {
            toplamTutar = "0.00";
        }
        var ID = this.getAttribute('data-id');
        $('#sale_price_kdv_'+ ID).val(toplamTutar.toFixed(lenPar));
    });
    $('.sale_price_kdv').keyup(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }
        var toplamTutar = this.value / parseFloat(1 + kdvRate / 100);
        const dec = String(toplamTutar).split('.')[1]
        const len = dec && dec.length > 2 ? dec.length : 2
        const lenPar = len && len > 5 ? 6 : len
        if (toplamTutar.toFixed(len) === "NaN") {
            toplamTutar = "0.00";
        }
        var ID = this.getAttribute('data-id');
        $('#sale_price_'+ID).val(toplamTutar.toFixed(lenPar));
    });
    $('.sale_price_kdv').trigger("keyup");

    $('#discount_price').keyup(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }
        var toplamTutar = this.value * parseFloat(1 + kdvRate / 100);
        const dec = String(toplamTutar).split('.')[1]
        const len = dec && dec.length > 2 ? dec.length : 2
        const lenPar = len && len > 5 ? 6 : len
        if (toplamTutar.toFixed(len) === "NaN") {
            toplamTutar = "0.00";
        }
        $('#discount_price_kdv').val(toplamTutar.toFixed(lenPar));
        if ($('#basket_price_kdv').val() != '0'){
            $('#basket_price_kdv').trigger("keyup");
        }
    });
    $('#discount_price_kdv').keyup(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }
        var toplamTutar = this.value / parseFloat(1 + kdvRate / 100);
        const dec = String(toplamTutar).split('.')[1]
        const len = dec && dec.length > 2 ? dec.length : 2
        const lenPar = len && len > 5 ? 6 : len
        if (toplamTutar.toFixed(len) === "NaN") {
            toplamTutar = "0.00";
        }
        $('#discount_price').val(toplamTutar.toFixed(lenPar));
        if ($('#basket_price_kdv').val() != '0'){
            $('#basket_price_kdv').trigger("keyup");
        }
    });
    $('#discount_price_kdv').trigger("keyup");

    $('#standart_kdv_rate').change(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }
        $('.sale_price_kdv').trigger("keyup");
        $('#discount_price_kdv').trigger("keyup");
    });

    $('#special_kdv_rate').keyup(function() {
        var kdvRateSelect = $('#standart_kdv_rate').val();
        if (kdvRateSelect == '-1') {
            var kdvRate = $('#special_kdv_rate').val();
        }else{
            var kdvRate = kdvRateSelect;
        }

        $('.sale_price_kdv').trigger("keyup");
        $('#discount_price_kdv').trigger("keyup");
    });

    var myVar;
    $(document).ready(function () {
        var $tree = $('.menux');
        var $categoriesListJson =
            '[ <?= rtrim($categoriesList, ',
            ') ?>]';
        var resetting = false;
        $('.menux').jstree({
            "core": {
                "themes": {
                    "icons": false,
                },
                "data": JSON.parse($categoriesListJson)
            },
            'checkbox': {
                "keep_selected_style": true
            },
            "select_node": '0',
            'search': {
                'case_insensitive': true,
                'show_only_matches' : true
            },
            "types": {
                "default": {
                    "icon": "fa fa-angle-right fa-fw"
                },
                "demo": {
                    "icon": "fa fa-angle-right fa-fw"
                }
            },
            "plugins": [
                "search",
                "types",
                "wholerow",
                "checkbox"
            ]
        }).on('search.jstree', function (nodes, str, res) {
            if (str.nodes.length===0) {
                $('.menux').jstree(true).hide_all();
            }
        });

        $('#deliverable_search').keyup(function(){
            $('.menux').jstree(true).show_all();
            $('.menux').jstree('search', $(this).val());
        });

        $('.menux').on('changed.jstree', function (e, data) {
            myVar = setTimeout(function () {
                GetSelectedMenu();
            }, 500);
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
</script>
<?= view("admin/inculude/body_end") ?>
