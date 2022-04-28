<?= view("inculude/head") ?>
<?= $PageSeo->seo('Şifremi Sıfırla' , '' , 'sifremi-sifirla', '', ''); ?>
<?= view("inculude/body_start") ?>
<?= view("inculude/header") ?>


<main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="./" rel="nofollow"><i class="fi-rs-home mr-5"></i>Anasayfa</a>
                    <span></span> Şifremi Sıfırla 
                </div>
            </div>
        </div>
        <div class="page-content pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                        <div class="row">
                            <div class="col-lg-6 pr-30 d-none d-lg-block" style="display: flex!important; justify-content: center; align-items: center;">
                                <img class="border-radius-15" src="<?= base_url() ?>/public/frontend/assets/imgs/rr_logo.png" alt="" />
                            </div>
                            <div class="col-lg-6 col-md-8">
                                <div class="login_wrap widget-taber-content background-white">
                                    <div class="padding_eight_all bg-white">
                                        <div class="heading_s1">
                                            <h1 class="mb-5">Şifremi Sıfırla</h1>
                                        </div>
                                        <form id="mini_form_reset_password" action="<?= base_url("resetPassword") ?>" onsubmit="return false">
                                            <input type="hidden" name="email" value="<?= $email ?>">
                                            <input type="hidden" name="reset_code" value="<?= $reset_code ?>">
                                            <div class="form-group">
                                                <input type="password" required="" name="password" id="resetPassword" placeholder="Yeni Şifre*" />
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="passwordConfirm" id="resetPasswordConfirm" placeholder="Yeni Şifre Tekrar" />
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-heading btn-block hover-up">Şifremi Sıfırla </button>
                                            </div>
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

    window.addEventListener('load', function() {

        var form = $('#mini_form_reset_password');
        form.validate({
            errorPlacement: function(label, element) {
                label.addClass('arrow');
                label.insertAfter(element);
            },
            wrapper: 'span',
            rules: {
               
                password: {
                    required: true,
                    minlength: 6,
                    maxlength:16
                },
                passwordConfirm: {
                    required: true,
                    equalTo: "#resetPassword"
                }
            },
            // Specify validation error messages
            messages: {
                password: {
                    required: "Lütfen şifrenizi giriniz.",
                    minlength: "Girilen şifre 6 karakterden küçük olamaz.",
                    maxlength: "Girilen şifre 16 karakterden büyük olamaz."
                    
                },
                passwordConfirm: {
                    required: "Lütfen şifrenizi tekrardan giriniz.",
                    equalTo: "Girilen şifreler birbirleriyle uyuşmuyor."
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form_reset_password').attr('action');

                miniSubmitForm('mini_form_reset_password',''+ action +'', '<?= base_url("giris-yap") ?>', '1');
            }
        });

    }, false);

</script>

<?= view("inculude/body_end") ?>



