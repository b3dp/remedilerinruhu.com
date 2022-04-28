<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <form id="mini_form" action="<?= base_url('managersAdd') ?>" onsubmit="return false" class="form">
                <section class="content-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-header">
                                <h2 class="content-title">Yeni Yönetici Ekle</h2>
                                <div>
                                    <button class="btn btn-md rounded font-sm hover-up">Kaydet</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Yönetici Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Ad</label>
                                        <input type="text" name="name" class="form-control" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Soyad</label>
                                        <input type="text" name="surname" class="form-control" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">E-Posta Adresi</label>
                                        <input type="text" name="email" class="form-control" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Telefon</label>
                                        <input type="text" name="phone" class="form-control" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Şifre</label>
                                        <input type="text" name="password" class="form-control" value="" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="product_name" class="form-label">Kullanıcı Modu</label>
                                        <select class="form-select" name="user_type" onchange="userTypeChange(this.value)">
                                        <?php foreach ($users_type as $row) : ?>
                                            <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                        <?php endforeach ?>
                                        </select>
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

    var form = $('#mini_form');
    form.validate({
        errorPlacement: function (label, element) {
            label.addClass('arrow');
            label.insertAfter(element);
        },
        wrapper: 'span',

        rules: {
            name: "required",
            surname: "required",
            email: {
                required: true,
                email: true
            },
            phone: "required",
            password: {
                required: true,
                minlength: 6,
                maxlength:16
            },
        },
        // Specify validation error messages
        messages: {
            name: {
                required: "Lütfen kullanıcının adını giriniz.",
            },
            surname: {
                required: "Lütfen kullanıcının soyadını giriniz.",
            },
            email: {
                required: "Lütfen kullanıcı için email adresinizi doldurunuz.",
                email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
            },
            phone: {
                required: "Lütfen kullanıcının telefonunuzu giriniz.",
            },
            password: {
                required: "Lütfen şifrenizi giriniz.",
                minlength: "Girilen şifre 6 karakterden küçük olamaz.",
                maxlength: "Girilen şifre 16 karakterden büyük olamaz."
                
            },
        },
        submitHandler: function () {
            var action = $('#mini_form').attr('action');
            miniSubmit('' + action + '', 'managers/list', '1');
        }
    });

</script>

<?= view("admin/inculude/body_end") ?>
