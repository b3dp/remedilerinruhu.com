<?= view("admin/inculude/start") ?>
    <?php 
        if ($coupon->discount_type == '1') {
            if ($coupon->discount < 100) {
                $discount = '0'.$coupon->discount;
            }else{
                $discount = $coupon->discount;
            }
        }else{
            $discount = $coupon->discount;
        }
    ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('couponEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="id" value="<?= $coupon->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $coupon->title ?> Kupon Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kupon Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kupon Başlık</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" value="<?= $coupon->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="code" class="form-label">Kupon Kodu</label>
                                        <input type="text" name="code" placeholder="Kupon Kodu" class="form-control" id="code" value="<?= $coupon->code ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="piece" class="form-label">Kupon Adeti</label>
                                        <input type="text" name="piece" placeholder="Kupon Adeti" class="form-control" id="piece" value="<?= $coupon->piece ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">İndirim Tipi</label>
                                        <select id="discount_type" name="discount_type" onchange="discountType(this);" class="form-select">
                                            <option value="1" <?= $coupon->discount_type == '1' ? 'selected' : '' ?> >Yüzdelik İndirim</option>
                                            <option value="2" <?= $coupon->discount_type == '2' ? 'selected' : '' ?> >Sabit İndirim</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">  
                                        <label class="form-label" id="discount_rate_label"><?= $coupon->discount_type == '1' ? 'İndirim Oranını Giriniz.' : 'İndirim Mıktarını giriniz.' ?></label>
                                        <input type="text" id="discount_rate" name="discount" class="form-control" placeholder="<?= $coupon->discount_type == '1' ? 'İndirim Oranı' : 'İndirim Mıktarı' ?>" value="<?= $discount ?>" />
                                    </div> 

                                    <div class="mb-4 discount_min_area <?= $coupon->discount_type == '2' ? '' : 'd-none' ?>">  
                                        <label class="form-label" for="discount_min">Min Sepet Tutarı</label>
                                        <input type="text" id="discount_min" name="discount_min" disabled class="form-control" placeholder="Minimum gerekli sepet Ücreti" value="<?= $coupon->discount_min ?>" />
                                    </div> 

                                    <div class="mb-4">
                                        <label class="form-label">İndirim Son Kullanma Tarihi</label>
                                        <input type="date" class="form-control" placeholder="İndirim Son Kullanma Tarihi" autocomplete="off" id="end_at" name="end_at" value="<?= $coupon->end_at ?>" />
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Kupon Tipi</label>
                                        <select id="coupon_type" name="coupon_type" onchange="couponType(this);" class="form-select">
                                            <option value="0" <?= $coupon->coupon_type == '0' ? 'selected' : '' ?> selected >Standart</option>
                                            <option value="1" <?= $coupon->coupon_type == '1' ? 'selected' : '' ?> >Özel Seçenek</option>
                                        </select>
                                    </div>

                                    <div id="coupon_type_1_area" class="<?= $coupon->coupon_type == '1' ? '' : 'd-none' ?>">
                                        <div class="d-grid mb-4">  
                                            <label class="form-label">Kodun Geçerli Olduğu Markalar
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm Markalarda geçerli olacak ise seçim yapmayın."></i>
                                            </label>
                                            <select name="category_id[]" <?= $coupon->coupon_type == '1' ? '' : 'disabled="disabled"' ?> class="form-select form-select-solid" data-placeholder="Lütfen kupon koduna ait kategorileri seçiniz." multiple data-control="select2" >
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

                                        <div class="d-grid mb-4">  
                                            <label class="form-label">Kodun Geçerli Olduğu Markalar
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm Markalarda geçerli olacak ise seçim yapmayın."></i>
                                            </label>
                                            <select name="brand_id[]" <?= $coupon->coupon_type == '1' ? '' : 'disabled="disabled"' ?> class="form-select form-select-solid" data-placeholder="Lütfen kupon koduna ait markaları seçiniz." multiple data-control="select2" >
                                                <?php foreach ($brands as $row) : ?>
                                                    <option <?= in_array($row->id, $brandArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>

                                        <div class="d-grid mb-4">  
                                            <label class="form-label">Kuponun Geçerli Olduğu Ürünler
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm ürünlerde geçerli olacak ise seçim yapmayın."></i>
                                            </label>
                                            <select name="product_id[]" <?= $coupon->coupon_type == '1' ? '' : 'disabled="disabled"' ?> class="form-select form-select-solid" data-placeholder="Lütfen indirim koduna ait ürünleri seçiniz." multiple data-control="select2" >
                                                <?php foreach ($product as $key => $row) : ?>
                                                    <option <?= in_array($row->id, $productArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>

                                        <div class="d-grid mb-4">  
                                            <label class="form-label">Tedavi Türü
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm ürünlerde geçerli olacak ise seçim yapmayın."></i>
                                            </label>
                                            <select name="attribute_id[]" <?= $coupon->coupon_type == '1' ? '' : 'disabled="disabled"' ?> class="form-select form-select-solid" data-placeholder="Lütfen indirim koduna ait ürünleri seçiniz." multiple data-control="select2" >
                                                <?php foreach ($attribute as $key => $row) : ?>
                                                    <option <?= in_array($row->id, $attributeArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">  
                                        <label class="form-label">Satıcı Seçeneği
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm satıcılarda geçerli olacak ise seçim yapmayın."></i>
                                        </label>
                                        <select name="seller_id[]" class="form-select form-select-solid select2" data-placeholder="Tüm üyelerde geçerli olacak ise seçim yapmayın." multiple data-control="select2" >
                                            <?php foreach ($userSeller as $key => $row) : ?>
                                                <option <?= in_array($row->id, $sellerArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->full_name ?> - <?= $row->email ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="mb-4">  
                                        <label class="form-label">Üye Seçimi
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tüm üyelerde geçerli olacak ise seçim yapmayın."></i>
                                        </label>
                                        <select name="user_id[]" class="form-select form-select-solid select2" data-placeholder="Tüm üyelerde geçerli olacak ise seçim yapmayın." multiple data-control="select2" >
                                            <?php foreach ($user as $key => $row) : ?>
                                                <option <?= in_array($row->id, $userArray) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->full_name ?> - <?= $row->email ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Kupon</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Kupon Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $coupon->is_active == '1' ? 'selected' : '' ?> >Evet Yayınlansın.</option>
                                                <option value="0"<?= $coupon->is_active != '1' ? 'selected' : '' ?>  >Hayır taslak olarak bırak.</option>
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
<script src="<?= base_url() ?>/public/admin/assets/js/jquery.inputmask.min.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/js/inputmask.binding.js"></script>

<script>

    $(document).ready(function() {
        $('[data-control="select2"]').each(function(){
            var placeholder = $(this).attr('data-placeholder')
            placeholder = (placeholder === undefined) ? '' : placeholder;
            $(this).select2();
        });
    });

    $('#discount_type').trigger('change');
    function discountType (el) {
        var value ;
    
        if (el.value == '2') {
            $('#discount_rate_label').text('İndirim Mıktarını giriniz.');
            $('#discount_rate').attr('placeholder', 'İndirim Mıktarını');
            $('.discount_min_area').removeClass('d-none');
            $('#discount_min').removeAttr('disabled', 'disabled');
            $("#discount_rate").inputmask({
                mask:"",
                greedy: false,
                definitions: {
                '*': {
                        validator: "[0-9]"
                    }
                },
                rightAlign: false
            });
        }else{
            value = '1'
            $('#discount_rate_label').text('İndirim Oranını Giriniz.');
            $('#discount_rate').attr('placeholder', 'İndirim Oranı');
            $('.discount_min_area').addClass('d-none');
            $('#discount_min').attr('disabled', 'disabled');
            $("#discount_rate").inputmask({
                mask: "999[.99]",
                greedy: false,
                definitions: {
                '*': {
                        validator: "[0-9]"
                    }
                },
                rightAlign: false
            });
        }
    }

    function couponType (el) {
        var value ;
    
        if (el.value == '2') {
            $('#coupon_type_2_area').removeClass('d-none');
            $('#coupon_type_2_area select').removeAttr('disabled', 'disabled');
            $('#coupon_type_1_area').addClass('d-none');
            $('#coupon_type_1_area select').attr('disabled', 'disabled');
        }else if (el.value == '1') {
            value = '1'
            $('#coupon_type_1_area').removeClass('d-none');
            $('#coupon_type_1_area select').removeAttr('disabled', 'disabled');
            $('#coupon_type_2_area').addClass('d-none');
            $('#coupon_type_2_area select').attr('disabled', 'disabled');
        }else if (el.value == '0') {
            value = '1'
            $('#coupon_type_1_area').addClass('d-none');
            $('#coupon_type_1_area select').attr('disabled', 'disabled');
            $('#coupon_type_2_area').addClass('d-none');
            $('#coupon_type_2_area select').attr('disabled', 'disabled');
        }
    }

</script>

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
                    required: "Lütfen kampanya için bir başlık giriniz.",
                }
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');

                miniSubmit('' + action + '', 'coupon/list', '1');
            }
        });
    }, false);
    
</script>

<?= view("admin/inculude/body_end") ?>
