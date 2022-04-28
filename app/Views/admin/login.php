
<?= view("admin/inculude/start") ?>
<title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
    <main>
        <header class="d-flex justify-content-center main-header navbar style-2">
            <div class="col-brand">
                <a href="index.html" class="brand-wrap">
                    <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/logo.svg" class="logo" alt="Nest Dashboard" />
                </a>
            </div>
        </header>
        
        <section class="content-main mt-80 mb-80">
            <div class="card mx-auto card-login">
                <div class="card-body">
                    <h4 class="card-title mb-4">Giriş Yap</h4>
                    <form id="mini_form" action="<?= base_url('admin/LoginCheack'); ?>">
                        <div class="mb-3">
                            <input class="form-control" placeholder="E-Posta Adresi" name="email" type="text" />
                        </div>
                        <div class="mb-3">
                            <input class="form-control" placeholder="Şifre" name="password" type="password" />
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember" checked="1" />
                                <span class="form-check-label">Beni Hatırlat</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
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
                    password: "required",
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
                    },
                    password: {
                        required: "Lütfen şifrenizi giriniz."
                    }
                },  
                submitHandler: function() {
                    var action = $('#mini_form').attr('action');

                    miniSubmit(''+ action +'', 'dashboard', '1');
                }
            });
        }, false);

        function onSubmit(token) {
            var action = $('#mini_form').attr('action');
            miniSubmit(''+ action +'', 'dashboard', '1');
        }
    </script>
<?= view("admin/inculude/body_end") ?>