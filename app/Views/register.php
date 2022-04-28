<?= view("inculude/head") ?>
<?= $PageSeo->seo('Kayıt Ol' , '' , 'kayit-ol', '', ''); ?>

<?= view("inculude/body_start") ?>

<?= view("inculude/header") ?>
    <style>
        .form-select {
            border: 1px solid #f0e9ff;
            border-radius: 10px;
            height: 48px;
            padding-left: 18px;
            font-size: 16px;
        }

        .form-select:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 -0.75rem rgb(13 110 253 / 25%);
        }
    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="./" rel="nofollow"><i class="fi-rs-home mr-5"></i>Anasayfa</a>
                    <span></span> Üye Ol
                </div>
            </div>
        </div>
        <div class="page-content pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                        <div class="row">
                            <div class="col-lg-8 col-md-8">
                                <div class="login_wrap widget-taber-content background-white">
                                    <div class="padding_eight_all bg-white">
                                        <div class="heading_s1">
                                            <h1 class="mb-5">Yeni Hesap Oluştur</h1>
                                            <p class="mb-30">Zaten bir hesabınız var mı? <a href="giris-yap">Giriş Yap</a></p>
                                        </div>
                                        <form id="mini_form_register" action="<?= base_url("registerUser") ?>" onsubmit="return false">
                                            <input type="hidden" name="retun" value="<?= $retun ?>">
                                            <div class="form-group">
                                                <input type="text" required="" name="username" placeholder="Ad Soyad" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" required="" name="email" placeholder="Email" />
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="password" id="registerPassword" placeholder="Şifre" />
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="password" id="registerPasswordConfirm" placeholder="Şifre tekrar" />
                                            </div>
                                            <div class="form-group">
                                                <select class="form-select" required="" name="role" placeholder="Üyelik Tipi" onchange="userTypeChange(this.value)" style="height: 64px; border-radius: 10px;" >
                                                    <option value="">Üyelik Tipi</option>
                                                    <?php foreach ($user_type as $row) : ?>
                                                        <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 userTypeArea role-1 role-2 <?= ($user->role == '1' || $user->role == '2') ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="diploma_no" class="form-label">Diploma Tescil Numarası</label>
                                                        <input type="text" name="diploma_no" <?= ($user->role == '1' || $user->role == '2') ? '' : 'disabled' ?> placeholder="Diploma Tescil Numarası" class="form-control" id="diploma_no" value="<?= $user->diploma_no ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 userTypeArea role-1 role-2 <?= ($user->role == '1' || $user->role == '2') ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="working_status" class="form-label">Çalişma Durumu</label>
                                                        <select name="working_status" <?= ($user->role == '1' || $user->role == '2') ? '' : 'disabled' ?> class="form-select" id="working_status">
                                                            <option <?= $user->working_status == '1' ? 'selected' : '' ?> value="1">Emekliyim</option>
                                                            <option <?= $user->working_status == '2' ? 'selected' : '' ?> value="2">Değilim</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 userTypeArea role-6 <?= ($user->role == '6') ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="gln_code_status" class="form-label">GLN Kod Durumu</label>
                                                        <select name="gln_code_status" class="form-select" <?= ($user->role == '6') ? '' : 'disabled' ?> id="gln_code_status" onchange="this.value == 1 ? $('#gln_code').attr('disabled','disabled') : $('#gln_code').removeAttr('disabled','disabled') ">
                                                            <option <?= $user->gln_code_status == '2' ? 'selected' : '' ?> value="2">GLN Kodum Var</option>
                                                            <option <?= $user->gln_code_status == '1' ? 'selected' : '' ?> value="1">GLN Kodum yok</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 userTypeArea role-6 <?= ($user->role == '6') ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="gln_code" class="form-label">GLN Kodu</label>
                                                        <input type="text" name="gln_code" <?= ($user->role == '6') ? '' : 'disabled' ?> placeholder="GLN Kodu" <?= $user->gln_code_status == '1' ? 'disabled' : '' ?> class="form-control" id="gln_code" value="<?= $user->gln_code ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 userTypeArea role-6 <?= ($user->role == '6') ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="working_status" class="form-label">Çalişma Durumu</label>
                                                        <select name="working_status" <?= ($user->role == '6') ? '' : 'disabled' ?> class="form-select" id="working_status">
                                                            <option <?= $user->working_status == '1' ? 'selected' : '' ?> value="1">Emekliyim</option>
                                                            <option <?= $user->working_status == '2' ? 'selected' : '' ?> value="2">Değilim</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 userTypeArea role-7 <?= $user->role == '7' ? '' : 'd-none' ?>">
                                                    <div class="mb-4">
                                                        <label for="working_institution" class="form-label">Çalıştığı Kurum</label>
                                                        <input type="text" name="working_institution" <?= $user->role == '7' ? '' : 'disabled' ?> placeholder="Çalıştığı Kurum" class="form-control" id="working_institution" value="<?= $user->working_institution ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="payment_option mb-50">
                                                <h5 class="mb-10 text-center">Satış Seçeneği</h5>
                                                <div class="d-flex justify-content-around">
                                                    <div class="custome-radio">
                                                        <input class="form-check-input" required="" type="radio" name="is_seller" id="exampleRadios3" value="1">
                                                        <label class="form-check-label" for="exampleRadios3" data-bs-toggle="collapse" data-target="#bankTranfer" aria-controls="bankTranfer">Evet, Satiş Yapmak İstiyorum</label>
                                                    </div>
                                                    <div class="custome-radio">
                                                        <input class="form-check-input" required="" type="radio" name="is_seller" id="exampleRadios4" value="0" checked="">
                                                        <label class="form-check-label" for="exampleRadios4" data-bs-toggle="collapse" data-target="#checkPayment" aria-controls="checkPayment">Hayır, Satiş yapmak istemiyorum</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row is_seller_area">
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="company_name" class="form-label">Firma Adı</label>
                                                        <input type="text" name="company_name" placeholder="Firma Adınız" disabled class="form-control" id="company_name" value=""/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="company_address" class="form-label">Firma Adresi</label>
                                                        <input type="text" name="company_address" placeholder="Firma Adresi" disabled class="form-control" id="company_address" value="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="company_land_phone" class="form-label">Firma Sabit Tel</label>
                                                        <input type="text" name="company_land_phone" placeholder="Firma Sabit Telefon" disabled class="form-control" id="company_land_phone" value="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="company_mobile_phone" class="form-label">Firma Cep Tel</label>
                                                        <input type="text" name="company_mobile_phone" placeholder="Firma Sabit Telefon" disabled class="form-control" id="company_mobile_phone" value="" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="login_footer form-group mb-50">
                                                <div class="chek-form">
                                                    <div class="custome-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox12" value="" />
                                                        <label class="form-check-label" for="exampleCheckbox12"><span>I agree to terms &amp; Policy.</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-30">
                                                <button type="submit" class="btn btn-fill-out btn-block hover-up font-weight-bold" name="login">Submit &amp; Register</button>
                                            </div>
                                            <p class="font-xs text-muted"><strong>Note:</strong>Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy</p>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?= view("inculude/footer") ?>

<?= view("inculude/script") ?>

<script>

    function userTypeChange (user_type) {
        $('.userTypeArea').addClass('d-none');
        $('.userTypeArea input').attr('disabled');
        $('.userTypeArea select').attr('disabled');

        $('.role-'+ user_type +'').removeClass('d-none');
        $('.role-'+ user_type +' input').removeAttr('disabled');
        $('.role-'+ user_type +' select').removeAttr('disabled');
    }

    window.addEventListener('load', function() {
        var form = $('#mini_form_register');
        form.validate({
            errorPlacement: function(label, element) {
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
                password: {
                    required: true,
                    minlength: 6,
                    maxlength:16
                },
                passwordConfirm: {
                    required: true,
                    equalTo: "#registerPassword"
                },
                phone: "required",
                days: "required",
                month: "required",
                year: "required",
                contrat: "required",
            },
            // Specify validation error messages
            messages: {
                name : "Lütfen adınızı giriniz.",
                surname : "Lütfen soyadınızı giriniz.",
                email: {
                    required: "Lütfen email adresinizi doldurunuz.",
                    email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
                },
                password: {
                    required: "Lütfen şifrenizi giriniz.",
                    minlength: "Girilen şifre 6 karakterden küçük olamaz.",
                    maxlength: "Girilen şifre 16 karakterden büyük olamaz."
                    
                },
                passwordConfirm: {
                    required: "Lütfen şifrenizi tekrardan giriniz.",
                    equalTo: "Girilen şifreler birbirleriyle uyuşmuyor."
                },
                phone : "Lütfen telefon numaranızı giriniz.",
                days : "Lütfen doğduğunuz günü seçiniz.",
                month : "Lütfen dogdugunuz ayı seçiniz.",
                year : "Lütfen dogdugunuz yılı seçiniz.",
                contrat : "Lütfen üyelik için gerekli sözleşmeleri okuyup onaylayınız.",
            },  
            submitHandler: function() {
                var action = $('#mini_form_register').attr('action');

                miniSubmitForm('mini_form_register',''+ action +'', 'giris-yap?info=activation', '1');
            }
        });
    }, false);

</script>

<?= view("inculude/body_end") ?>



