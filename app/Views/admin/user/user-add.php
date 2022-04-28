<?= view("admin/inculude/head") ?>
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
<link href="<?= base_url() ?>/public/admin/assets/css/ckeditor.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/public/admin/assets/css/tagify.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet"
    type="text/css" />

<link href="<?= base_url() ?>/public/admin/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url() ?>/public/admin/assets/plugins/global/plugins.bundle.js"></script>
<!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/header") ?>

<!--  BEGIN MAIN CONTAINER  --> 
<div class="page d-flex flex-row flex-column-fluid">
    <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

        <div class="toolbar py-5 py-lg-15" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container d-flex flex-stack flex-wrap">-
                <div class="page-title d-flex flex-column me-3">-
                    <h1 class="d-flex text-white fw-bolder my-1 fs-3">Yeni Ürün Ekle Ekle</h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">

                        <li class="breadcrumb-item text-white opacity-75">
                            <a href="./" class="text-white text-hover-primary">Anasayfa</a>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-white opacity-75">
                            <a href="product/list" class="text-white text-hover-primary">Ürünler</a>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-white opacity-75">Yeni Ürün Ekle</li>

                    </ul>

                </div>

                <div class="d-flex align-items-center py-3 py-md-1">

                    <a href="product/list" class=" btn btn-bg-white btn-active-color-primary">Geri Dön</a>
                    <!--end::Button-->
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Container-->
        </div>

        <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container">

            <div class="content flex-row-fluid" id="kt_content">

                <div class="card mb-5 mb-xl-10">
                    <!--begin::Card header-->
                    <div class="card-header border-0 ">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Yeni Ürün</h3>
                        </div>
                        <!--end::Card title-->
                    </div>

                    <div id="kt_account_profile_details" class="collapse show">
                        <form id="mini_form" action="<?= base_url('categoryAdd') ?>" onsubmit="return false"
                            class="form">

                            <div class="card-body border-top p-9">
                                <ul style="margin-left:50px;" class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Genel Ayarlar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a onclick="javascrit:void(0)" class="nav-link" data-bs-toggle="tab" href="javascrit:void(0)">Kombinasyonlar</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel"> 
                                         Tab 1 İçerik
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <button type="button" onclick=" miniSubmitConfirmation('<?= base_url('productInsert'); ?>' , 'custom_reload_ajax', '1')" class="btn btn-primary"
                                    id="kt_account_profile_details_submit">Kaydet ve Varyasyonları belirle.</button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Content-->
                </div>
            </div>
            <!--end::Post-->
        </div>

        <?= view("admin/inculude/footer") ?>

    </div>
</div>
<?= view("admin/inculude/script") ?>

<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/plugins/custom/ckeditor/ckeditor.js"></script>

<script src="<?= base_url() ?>/public/admin/assets/js/dropzone.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/js/form.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/js/jquery.validate.js"></script>
<script src="<?= base_url() ?>/public/admin/assets/js/tagify.min.js"></script>

<script>
    $(document).ready(function () {

        for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
 
        
    });
    
</script>

<script>

    CKEDITOR.replace('description', {
        toolbar: [ // Line break - next group will be placed in new line.
            {
                name: 'basicstyles',
                items: ['Bold', 'Italic']
            }
        ],
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
                    required: "Lütfen kategori için bir başlık giriniz.",
                }
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');

                miniSubmit('' + action + '', 'custom_reload_ajax', '1');
            }
        });
    }, false);
</script>
<?= view("admin/inculude/body_end") ?>