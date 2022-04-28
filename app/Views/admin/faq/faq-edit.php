<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('faqEdit') ?>" onsubmit="return false" class="form">
                <input type="hidden" name="group_id" value="<?= $faqGroup->id ?>">
                <input type="hidden" name="id" value="<?= $faqFind->id ?>">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title"><?= $faqFind->title ?> Soru Düzenle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4><?= $faqGroup->title ?> Soru Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Soru Başlığı</label>
                                        <input type="text" name="title" placeholder="Başlık" class="form-control" id="product_name" value="<?= $faqFind->title ?>" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Soru Açıklaması</label>
                                        <textarea placeholder="Açıklama" name="description" class="form-control" rows="4"><?= $faqFind->description ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Soru</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-2">
                                       
                                        <div class="form-group mt-5">  
                                            <label class="col-form-label required fw-bold fs-6">Soru Yayınlansın mı?</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $faqFind->is_active == '1' ? 'selected' : ''  ?> selected>Evet Soru Yayınlansın.</option>
                                                <option value="0" <?= $faqFind->is_active == '0' ? 'selected' : '' ?> >Hayır taslak olarak bırak.</option>
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
                    required: "Lütfen niteliğin değerini giriniz.",
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form').attr('action');
                miniSubmit(''+ action +'', 'faq/list/<?= $faqGroup->id ?>', '1');
            }
        });
    }, false);

</script>

<?= view("admin/inculude/body_end") ?>
