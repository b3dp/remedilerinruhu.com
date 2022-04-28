
<?= view("inculude/head") ?>

<?= $PageSeo->seo('Giriş Yap' , '' , 'giris-yap', '', ''); ?>

<?= view("inculude/body_start") ?>

<?= view("inculude/header") ?>
<?= view("inculude/modals") ?>

    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="index.html" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Pages <span></span> My Account
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
                                            <h1 class="mb-5">Giriş Yap</h1>
                                            <p class="mb-30">Hesabınız yok mu? <a href="page-register.html">Hemen Kayıt Olun</a></p>
                                        </div>
                                        <form id="mini_form_login" action="<?= base_url("loginUser") ?>" onsubmit="return false">
                                            <div class="form-group">
                                                <input type="email" required="" name="email" placeholder="E-Posta Adresiniz*" />
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="password" placeholder="Şifreniz" />
                                            </div>
                                            <div class="login_footer form-group mb-50">
                                                <a class="text-muted" data-bs-toggle="modal" data-bs-target="#modalPasswordReset">Şifremi Unuttum?</a>
                                            </div>
                                            <div class="form-group">
                                                <button class="g-recaptcha btn btn-heading btn-block hover-up"  data-sitekey="<?= getenv('google.recaptcha') ?>"  data-callback='onSubmit' data-action='submit'>Giriş Yap </button>
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

<div class="modal fade custom-modal" id="modalPasswordReset" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                 <p class="mb-7 font-size-sm text-gray-500">
                    Lütfen E-posta adresinizi giriniz. 
                    E-posta yoluyla yeni bir şifre oluşturmak için bir bağlantı alacaksınız
                </p>
                <form id="mini_form_forgot" action="<?= base_url("forgotPassword") ?>" onsubmit="return false">
                    <div class="form-group">
                        <label class="sr-only" for="modalPasswordResetEmail">
                            Email Adresiniz *
                        </label>
                        <input class="form-control form-control-sm" id="modalPasswordResetEmail" type="email" name="email"
                            placeholder="Email Adresiniz *" required>
                    </div>

                    <button class="btn btn-sm btn-dark">
                        Şifremi Unuttum
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>


<script>

    var formLogin = $('#mini_form_login');
    formLogin.validate({
        errorPlacement: function(label, element) {
            label.addClass('arrow');
            label.insertAfter(element);
        },
        wrapper: 'span',
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
            }
        },
        // Specify validation error messages
        messages: {
            email: {
                required: "Lütfen email adresinizi doldurunuz.",
                email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
            },
            password: {
                required: "Lütfen şifrenizi giriniz."
                
            }
        }
    });

    function onSubmit(token) {
        var action = $('#mini_form_login').attr('action');
        miniSubmitForm('mini_form_login',''+ action +'', '<?= $retun ? $retun : base_url(); ?>', '2');
    }

    window.addEventListener('load', function() {

        var formForgot = $('#mini_form_forgot');
        formForgot.validate({
            errorPlacement: function(label, element) {
                label.addClass('arrow');
                label.insertAfter(element);
            },
            wrapper: 'span',
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            // Specify validation error messages
            messages: {
                email: {
                    required: "Lütfen email adresinizi doldurunuz.",
                    email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
                }
            },  
            submitHandler: function() {
                var action = $('#mini_form_forgot').attr('action');

                miniSubmitForm('mini_form_forgot',''+ action +'', 'giris-yap', '1');
            }
        });
        
    }, false);

</script>

<script>
    /* Beginning of async download code. */
    function loadjscssfile(filename, filetype) {
        if(filetype == "js") {
            var cssNode = document.createElement('script');
            cssNode.setAttribute("type", "text/javascript");
            cssNode.setAttribute("src", filename);
        } else if(filetype == "css") {
            var cssNode = document.createElement("link");
            cssNode.setAttribute("rel", "stylesheet");
            cssNode.setAttribute("type", "text/css");
            cssNode.setAttribute("href", filename);
        }
        if(typeof cssNode != "undefined")
            document.getElementsByTagName("head")[0].appendChild(cssNode);
    }
    /* End of async download code. */
    loadjscssfile("https://www.google.com/recaptcha/api.js?render=<?= getenv('google.recaptcha') ?>", "js");
</script>