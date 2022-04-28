
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
    <!--  BEGIN MAIN CONTAINER  -->
    <div class="page d-flex flex-row flex-column-fluid">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
 
            <div class="toolbar py-5 py-lg-15" id="kt_toolbar">
                <div id="kt_toolbar_container" class="container d-flex flex-stack flex-wrap">-
                    <div class="page-title d-flex flex-column me-3">-
                        <h1 class="d-flex text-white fw-bolder my-1 fs-3"><?= $attributeFind->title ?> Değer Düzenle</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">

                            <li class="breadcrumb-item text-white opacity-75">
                                <a href="dashboard" class="text-white text-hover-primary">Anasayfa</a>
                            </li>

                            <li class="breadcrumb-item">
                                <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                            </li>

                            <li class="breadcrumb-item text-white opacity-75">
                                <a href="attribute/group-list" class="text-white text-hover-primary">Nitelikler</a>
                            </li>

                            <li class="breadcrumb-item">
                                <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                            </li>

                            <li class="breadcrumb-item text-white opacity-75">
                                <a href="attribute/list/<?= $attributeGroup->id ?>" class="text-white text-hover-primary"><?= $attributeGroup->title ?></a>
                            </li>
                            
                            <li class="breadcrumb-item">
                                <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                            </li>

                            <li class="breadcrumb-item text-white opacity-75">Değer Düzenle</li>

                        </ul>

                    </div>

                    <div class="d-flex align-items-center py-3 py-md-1">

                        <a href="attribute/list/<?= $attributeGroup->id ?>" class="btn btn-bg-white btn-active-color-primary" >Geri Dön</a>
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
                                <h3 class="fw-bolder m-0"><?= $attributeGroup->title ?> > Yeni Değer</h3>
                            </div>
                            <!--end::Card title-->
                        </div>

                        <div id="kt_account_profile_details" class="collapse show">
                            <form id="mini_form" action="<?= base_url('attributeChange') ?>" onsubmit="return false" class="form">
                                <input type="hidden" name="group_id" value="<?= $attributeGroup->id ?>">
                                <input type="hidden" name="id" value="<?= $attributeFind->id ?>">
                                <div class="card-body border-top p-9">

                                    <div class="row mb-6">
                                        <div class="col-lg-6 fv-row">
                                            <label class="required fw-bold fs-6">Nebim Renk Adi</label>
                                            <select name="selectAttr[]" class="form-select form-select-solid" data-placeholder="Lütfen kampanyaya ait kategorileri seçiniz." multiple data-control="select2" >
                                                <?php foreach ($attributeList as $row) : ?>
                                                    <option value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 fv-row">
                                            <label class="required fw-bold fs-6">Biltstore Renk Adı</label>
                                            <select class="form-select" name="thisAtrr" aria-label="Select example">
                                                <?php foreach ($attributeListTwo as $row) : ?>
                                                    <option value="<?= $row->id ?>"><?= $row->title ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="submit" class="btn btn-primary" id="saveAndReturn">Kaydet</button>
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

            <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                <!--begin::Container-->
                <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <!--begin::Copyright-->
                    <div class="text-dark order-2 order-md-1">
                        <span class="text-muted fw-bold me-1">2021©</span>
                        <a href="" target="_blank" class="text-gray-800 text-hover-primary">Keenthemes</a>
                    </div>
                    <!--end::Copyright-->
                    <!--begin::Menu-->
                    <ul class="menu menu-gray-600 menu-hover-primary fw-bold order-1">
                        <li class="menu-item">
                            <a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
                        </li>
                        <li class="menu-item">
                            <a href="https://keenthemes.com/support" target="_blank" class="menu-link px-2">Support</a>
                        </li>
                        <li class="menu-item">
                            <a href="https://1.envato.market/EA4JP" target="_blank" class="menu-link px-2">Purchase</a>
                        </li>
                    </ul>
                    <!--end::Menu-->
                </div>
                <!--end::Container-->
            </div>

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
                if (this.submitButton.id === 'saveAndRefresh') {
                    miniSubmit(''+ action +'', '', '1');
                }else if (this.submitButton.id === 'saveAndReturn') {
                    miniSubmit(''+ action +'', 'attribute/list/<?= $attributeGroup->id ?>', '1');
                }
                
            }
        });
    }, false);

</script>
<?= view("admin/inculude/body_end") ?>