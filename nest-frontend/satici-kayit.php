<?php require_once 'req/start.php'; ?>

    <meta name="description" content="" />
    <title>Nest - Multipurpose eCommerce HTML Template</title>
    
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
        
<?php require_once 'req/head.php'; ?>
<?php require_once 'req/body_start.php'; ?>
<?php require_once 'req/modals.php'; ?>
        
    <!--header-->
    <?php require_once 'req/header.php'; ?>
    <!--End header-->

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
                        <div class="col-xl-5 col-lg-10 col-md-12 m-auto">
                            
                            <div class="login_wrap widget-taber-content background-white">
                                <div class="padding_eight_all bg-white">
                                    <div class="heading_s1">
                                        <h1 class="mb-5">Create an Account</h1>
                                        <p class="mb-30">Already have an account? <a href="page-login.html">Login</a></p>
                                    </div>
                                    <form method="post">
                                        <div class="form-group">
                                            <input type="text" required="" name="username" placeholder="Username" />
                                        </div>
                                        <div class="form-group">
                                            <input type="text" required="" name="email" placeholder="Email" />
                                        </div>
                                        <div class="form-group">
                                            <input required="" type="password" name="password" placeholder="Password" />
                                        </div>
                                        <div class="form-group">
                                            <input required="" type="password" name="password" placeholder="Confirm password" />
                                        </div>
                                        <div class="login_footer form-group">
                                            <div class="chek-form">
                                                <input type="text" required="" name="email" placeholder="Security code *" />
                                            </div>
                                            <span class="security-code">
                                                <b class="text-new">8</b>
                                                <b class="text-hot">6</b>
                                                <b class="text-sale">7</b>
                                                <b class="text-best">5</b>
                                                    </span>
                                                </div>
                                                <div class="payment_option mb-50">
                                                    <div class="custome-radio">
                                                        <input class="form-check-input" required="" type="radio" name="payment_option" id="exampleRadios3" checked="" />
                                                        <label class="form-check-label" for="exampleRadios3" data-bs-toggle="collapse" data-target="#bankTranfer" aria-controls="bankTranfer">I am a customer</label>
                                                    </div>
                                                    <div class="custome-radio">
                                                        <input class="form-check-input" required="" type="radio" name="payment_option" id="exampleRadios4" checked="" />
                                                        <label class="form-check-label" for="exampleRadios4" data-bs-toggle="collapse" data-target="#checkPayment" aria-controls="checkPayment">I am a vendor</label>
                                                    </div>
                                                </div>
                                                <div class="login_footer form-group mb-50">
                                                    <div class="chek-form">
                                                        <div class="custome-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox12" value="" />
                                                            <label class="form-check-label" for="exampleCheckbox12"><span>I agree to terms &amp; Policy.</span></label>
                                                        </div>
                                                    </div>
                                                    <a href="page-privacy-policy.html"><i class="fi-rs-book-alt mr-5 text-muted"></i>Lean more</a>
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
        </main>
        
        <?php require_once 'req/footer.php'; ?>
        <script src="assets/js/vendor/modernizr-3.6.0.min.js"></script>
        <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
        <script src="assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
        <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
        <script src="assets/js/plugins/slick.js"></script>
        <script src="assets/js/plugins/jquery.syotimer.min.js"></script>
        <script src="assets/js/plugins/wow.js"></script>
        <script src="assets/js/plugins/slider-range.js"></script>
        <script src="assets/js/plugins/perfect-scrollbar.js"></script>
        <script src="assets/js/plugins/magnific-popup.js"></script>
        <script src="assets/js/plugins/select2.min.js"></script>
        <script src="assets/js/plugins/waypoints.js"></script>
        <script src="assets/js/plugins/counterup.js"></script>
        <script src="assets/js/plugins/jquery.countdown.min.js"></script>
        <script src="assets/js/plugins/images-loaded.js"></script>
        <script src="assets/js/plugins/isotope.js"></script>
        <script src="assets/js/plugins/scrollup.js"></script>
        <script src="assets/js/plugins/jquery.vticker-min.js"></script>
        <script src="assets/js/plugins/jquery.theia.sticky.js"></script>
        <script src="assets/js/plugins/jquery.elevatezoom.js"></script>
        <!-- Template  JS -->
        <script src="./assets/js/main.js?v=4.0"></script>
        <script src="./assets/js/shop.js?v=4.0"></script>
    </body>
</html>
