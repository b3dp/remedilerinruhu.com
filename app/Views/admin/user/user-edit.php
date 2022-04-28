<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <section class="content-main">
                <div class="content-header">
                    <a href="javascript:history.back()"><i class="material-icons md-arrow_back"></i> Go back </a>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-brand-2" style="height: 150px"></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                                <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 200px; margin-top: -120px">
                                    <?php if ($user->image) : ?>
                                        <img src="<?= base_url('uploads/users/'. $user->image .'') ?>" class="center-xy img-fluid" alt="<?= $user->full_name ?>">
                                    <?php else : ?>
                                        <img src="<?= base_url() ?>/public/admin/assets/imgs/brands/vendor-2.png" class="center-xy img-fluid" alt="<?= $user->full_name ?>">
                                    <?php endif ?>
                                    
                                </div>
                            </div>
                            <div class="col-xl col-lg">
                                <h3><?= $user->full_name ?></h3>
                            </div>
                        </div>
                        <!-- card-body.// -->
                        <hr class="my-4" />
                        <div class="d-flex g-4 justify-content-around row">
                            <div class="col-md-12 col-lg-3 col-xl-3">
                                <article class="box">
                                    <p class="mb-0 text-muted">Toplam Sipariş Tutarı:</p>
                                    <h5 class="text-success">₺<?= number_format($orderCount->total_price, 2) ?></h5>
                                    <p class="mb-0 text-muted">Toplam Siparş Miktarı:</p>
                                    <h5 class="text-success mb-0"><?= $orderCount->orderCount ?></h5>
                                </article>
                            </div>
                            <!--  col.// -->
                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                <h6>İletişim Bilgileri</h6>
                                <p>
                                    E-posta: <a href="mailTo:<?= $user->email ?>"><?= $user->email ?></a> <br />
                                    Telefon: <a href="tel:<?= $user->phone ?>"><?= $user->phone ?></a>  <br />
                                    Doğum Tarihi: <?= timeTR($user->birthday) ?> <br />
                                    Cinsiyet: <?= $user->gender == '1' ? 'Erkek' : 'Kadın' ?>
                                </p>
                            </div>
                        </div>
                        <!--  row.// -->
                    </div>
                    <!--  card-body.// -->
                </div>
                <!--  card.// -->
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="mb-4 nav nav-pills">
                            <a class="nav-link active" aria-current="page" data-bs-toggle="tab" href="#orderlistView">Siparişler</a>
                            <a class="nav-link" aria-current="page" data-bs-toggle="tab" href="#deliveryAdressView">Adresler</a>
                            <a class="nav-link" aria-current="page" data-bs-toggle="tab" href="#userInfoView">Üyelik Bilgileri</a>
                        </nav>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade <?= $pageValue <= 0 ? 'show active' : '' ?>" id="orderlistView" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="dataTableStandart">
                                        <thead>
                                            <tr>
                                                <th scope="col">#ID</th>
                                                <th scope="col">Sipariş No</th>
                                                <th scope="col">Tutar</th>
                                                <th scope="col">Durum</th>
                                                <th scope="col">Sipariş Tarihi</th>
                                                <th scope="col" class="text-end">Eylem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $key => $row) : ?>
                                                <tr>
                                                    <td><?= $row->id ?></td>
                                                    <td><?= $row->order_no ?></td>
                                                    <td><?= $row->discount_status ? number_format($row->total_price_discount, 2) : number_format($row->total_price, 2) ?> TL</td>
                                                    <td><?= orderStatusPanelView($row->status) ?></td>
                                                    <td><?= timeTR($row->buy_at) ?></td>
                                                    <td class="text-end">
                                                        <a target="_blank" href="order/detail/<?= $row->id ?>" class="btn btn-md rounded font-sm">Detail</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade <?= $pageValue >= 1 ? 'show active' : '' ?>" id="deliveryAdressView" role="tabpanel">
                                <div class="pt-4 mb-6 mb-xl-9">
                                    <div class="card-header border-0">
                                        <div id="kt_customer_view_payment_method" class="card-body pt-0 row">
                                            <?php $i = 1; foreach ($userAdressList as $row) : ?>
                                                <?php 
                                                    $cityFind = $addressModels->c_one("city", ['CityID' => $row->user_city]);
                                                    $townFind = $addressModels->c_one("town", ['TownID' => $row->user_town]);
                                                    $neighborhoodFind = $addressModels->c_one("neighborhood", ['NeighborhoodID' => $row->user_neighborhood]);    
                                                ?>
                                                <div class="col-md-6 card card-product-grid">
                                                    <div class="mt-2" data-kt-customer-payment-method="row">
                                                        <div class="d-flex flex-stack flex-wrap">
                                                            <div class="d-flex align-items-center collapsible rotate collapsed"
                                                                data-bs-toggle="collapse" href="#kt_customer_view_payment_method_<?= $row->id ?>"
                                                                role="button" aria-expanded="<?= $i == '1' ? 'false' : 'false' ?>"
                                                                aria-controls="kt_customer_view_payment_method_<?= $row->id ?>">
                                                                <div class="me-3 rotate-90">
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                fill-rule="evenodd">
                                                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                                                <path
                                                                                    d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z"
                                                                                    fill="#000000" fill-rule="nonzero"
                                                                                    transform="translate(12.000003, 11.999999) rotate(-270.000000) translate(-12.000003, -11.999999)" />
                                                                            </g>
                                                                        </svg>
                                                                    </span>
                                                                </div>
                                                                <div class="me-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="text-gray-800 fw-bolder"><?= $row->title ?></div>
                                                                        <div class="badge badge-light-primary ms-5"><?= $row->address_default == '1' ? 'Varsayılan' : '' ?></div>
                                                                    </div>
                                                                    <div class="text-gray-400"><?= $cityFind->CityName ?> / <?= $townFind->TownName ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex my-3 ms-9">
                                                            
                                                            </div>
                                                        </div>

                                                        <div id="kt_customer_view_payment_method_<?= $row->id ?>" class="collapse fs-6 ps-10" data-bs-parent="#kt_customer_view_payment_method">
                                                            <div class="d-flex flex-wrap flex-column">
                                                                <div class="flex-equal me-5 w-100 ">
                                                                    <table class="table table-flush fw-bold gy-1">
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">Alıcı Ad Soyad</td>
                                                                            <td class="text-gray-800 pb-0"><?= $row->receiver_name ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">E-posta
                                                                            </td>
                                                                            <td class="text-gray-800 pb-0"><?= $row->email ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">Cep Telefonu
                                                                            </td>
                                                                            <td class="text-gray-800 pb-0"><?= $row->phone ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">İl</td>
                                                                            <td class="text-gray-800 pb-0"><?= $cityFind->CityName ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">İlçe</td>
                                                                            <td class="text-gray-800 pb-0"><?= $townFind->TownName ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">Mahalle
                                                                            </td>
                                                                            <td class="text-gray-800 pb-0"><?= $neighborhoodFind->NeighborhoodName ?>.</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-gray-400 min-w-125px w-50 pb-0">Adres</td>
                                                                            <td class="text-gray-800 pb-0"><?= $row->address ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex info-wrap justify-content-around">
                                                        <a onclick="getCheckoutEditAddressForm('<?= $row->id ?>', '<?= base_url('getCheckoutEditAddressFormAdmin') ?>')" data-bs-toggle="modal" data-bs-target="#kt_modal_new_card" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                        <a onclick="miniSingle('<?= $row->id ?>', '<?= base_url('userAddressDeleteAdmin') ?>');" class="btn btn-sm font-sm btn-light rounded hover-red"> <i class="material-icons md-delete_forever"></i> Kaldır </a>
                                                    </div>
                                                </div>
                                            <?php $i++; endforeach ?>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade <?= $pageValue >= 1 ? 'show active' : '' ?>" id="userInfoView" role="tabpanel">
                                <div class="pt-4 mb-6 mb-xl-9">
                                    <div class="card-header border-0">
                                        <form id="mini_form" action="<?= base_url('userEdit') ?>" onsubmit="return false" class="form" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $user->id ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="product_name" class="form-label">Üye Adı</label>
                                                        <input type="text" name="name" placeholder="Üye Adı" class="form-control" id="product_name" value="<?= $user->name ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="product_name" class="form-label">Üye Soyad</label>
                                                        <input type="text" name="surname" placeholder="Üye Soyad" class="form-control" id="product_name" value="<?= $user->surname ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="product_name" class="form-label">Telefon</label>
                                                        <input type="text" name="phone" placeholder="Telefon Numarası" class="form-control" id="product_name" data-inputmask-clearincomplete="true" data-inputmask="'mask': '+\\90(999)-999-99-99'" value="<?= $user->phone ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label for="product_name" class="form-label">Üyelik Tipi</label>
                                                        <select type="text" name="user_type" class="form-select" onchange="userTypeChange(this.value)" value="<?= $user->phone ?>">
                                                            <?php foreach ($c_all_type as $row) : ?>
                                                                <option <?= $row->id == $user->role ? 'selected' : '' ?> value="<?= $row->id ?>"><?= $row->name ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-4">
                                                        <label for="product_name" class="form-label">Doğum Tarihi</label>
                                                        <div class="row fv-row">
                                                            <div class="col-4">
                                                                <select name="days"
                                                                    class="form-select form-select-solid" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="Gün">
                                                                    <?php foreach ($days as $key => $row) : ?>
                                                                        <option <?= isset($birthday['2']) && ($birthday['2'] == $row) ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-4">
                                                                <select name="month"
                                                                    class="form-select form-select-solid" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="Ay">
                                                                    <?php foreach ($month as $key => $row) : ?>
                                                                        <option <?= isset($birthday['1']) && ($birthday['1'] == $key) ? 'selected' : '' ?> value="<?= $key ?>"><?= $row ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-4">
                                                                <select name="year"
                                                                    class="form-select form-select-solid" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="Yıl">
                                                                    <?php foreach ($years as $key => $row) : ?>
                                                                        <option <?= isset($birthday['0']) && ($birthday['0'] == $row) ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                              
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
                                                            <option <?= $user->gln_code_status == '1' ? 'selected' : '' ?> value="1">Serbest eczacı değilim GLN Kodum yok</option>
                                                            <option <?= $user->gln_code_status == '2' ? 'selected' : '' ?> value="2">GLN Kodum Var</option>
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
                                            <div class="d-flex justify-content-center">
                                                <button type="submit"  class="btn btn-primary"> <span class="indicator-label">Kaydet</span></button>
                                            </div>
                                        </form>
                                        <div class="d-flex justify-content-center mt-30 row">
                                            <div class="col-md" style="max-width: 460px">
                                                <article class="box mb-3 bg-light emailArea">
                                                    <a class="btn float-end btn-light btn-sm rounded font-md" onclick="$('.changeEmailArea').removeClass('d-none');$('.emailArea').addClass('d-none')">Değiştir</a>
                                                    <h6>E-posta Adresi</h6>
                                                    <small class="text-muted d-block" style="width: 70%" id="emailArea"><?= $user->email ?></small>
                                                </article>
                                                <article class="box mb-3 bg-light changeEmailArea d-none">
                                                    <form id="userEmailChange" action="<?= base_url('userEmailChange') ?>" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                                        <input type="hidden" name="id" value="<?= $user->id ?>">
                                                        <div class="row mb-6">
                                                            <div class="col-lg-12 mb-4 mb-lg-0">
                                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                                    <label for="emailaddress"
                                                                        class="form-label fs-6 fw-bolder mb-3">Yeni E-posta Adresi</label>
                                                                    <input type="email"
                                                                        class="form-control form-control-lg form-control-solid"
                                                                        id="emailaddress" placeholder=""
                                                                        name="email"
                                                                        value="">
                                                                    <div
                                                                        class="fv-plugins-message-container invalid-feedback">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="d-flex justify-content-between mt-20">
                                                            <button id="kt_signin_submit" type="submit" class="btn btn-primary me-2 px-6">Güncelle</button>
                                                            <button id="kt_signin_cancel" type="button" class="btn btn-danger px-6" onclick="$('.changeEmailArea').addClass('d-none');$('.emailArea').removeClass('d-none')">İptal</button>
                                                        </div>
                                                        <div></div>
                                                    </form>
                                                </article>
                                            </div>
                                            <!-- col.// -->
                                            <div class="col-md" style="max-width: 460px">
                                                <article class="box mb-3 bg-light passwordArea">
                                                    <a class="btn float-end btn-light rounded btn-sm font-md" onclick="$('.changePasswordArea').removeClass('d-none');$('.passwordArea').addClass('d-none')">Şifreyi Sıfırla</a>
                                                    <h6>Şifre</h6>
                                                    <small class="text-muted d-block" style="width: 70%">************</small>
                                                </article>
                                                <article class="box mb-3 bg-light changePasswordArea d-none">
                                                    <form id="userPasswordChange" action="<?= base_url('userPasswordChange') ?>"
                                                        class="form fv-plugins-bootstrap5 fv-plugins-framework"
                                                        novalidate="novalidate">
                                                        <input type="hidden" name="id" value="<?= $user->id ?>">
                                                        <div class="row mb-1"> 
                                                            <div class="col-lg-12">
                                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                                    <label for="newpassword"
                                                                        class="form-label fs-6 fw-bolder mb-3">Yeni Şifre</label>
                                                                    <input type="password"
                                                                        class="form-control form-control-lg form-control-solid"
                                                                        name="password" id="newpassword">
                                                                    <div
                                                                        class="fv-plugins-message-container invalid-feedback">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                                    <label for="confirmpassword"
                                                                        class="form-label fs-6 fw-bolder mb-3">Yeni Şifre Tekrar</label>
                                                                    <input type="password"
                                                                        class="form-control form-control-lg form-control-solid"
                                                                        name="passwordConfirm" id="confirmpassword">
                                                                    <div
                                                                        class="fv-plugins-message-container invalid-feedback">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between mt-20">
                                                            <button id="kt_password_submit" type="submit" class="btn btn-primary me-2 px-6">Değiştir</button>
                                                            <button id="kt_password_cancel" type="button" class="btn btn-danger px-6" onclick="$('.changePasswordArea').addClass('d-none');$('.passwordArea').removeClass('d-none')">İptal</button>
                                                        </div>
                                                    </form>
                                                </article>
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>

    <div class="modal fade" id="kt_modal_new_card" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Adres Düzenle</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y ">
                    <form id="checkout_edit_address" class="form" action="#">
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>

    function userTypeChange (user_type) {
        $('.userTypeArea').addClass('d-none');
        $('.userTypeArea input').attr('disabled');
        $('.userTypeArea select').attr('disabled');

        $('.role-'+ user_type +'').removeClass('d-none');
        $('.role-'+ user_type +' input').removeAttr('disabled');
        $('.role-'+ user_type +' select').removeAttr('disabled');
    }

    window.addEventListener('load', function () {

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
                phone: "required",
            },
            // Specify validation error messages
            messages: {
                name: {
                    required: "Lütfen kullanıcının adını giriniz.",
                },
                surname: {
                    required: "Lütfen kullanıcının soyadını giriniz.",
                },
                phone: {
                    required: "Lütfen kullanıcının telefonunuzu giriniz.",
                },
            },
            submitHandler: function () {
                var action = $('#mini_form').attr('action');

                miniSubmit('' + action + '', 'custom_noreload', '1');
            }
        });

        var userEmailChange = $('#userEmailChange');
        userEmailChange.validate({
            errorPlacement: function (label, element) {
                label.addClass('arrow');
                label.insertAfter(element);
            },
            wrapper: 'span',

            rules: {
                email: {
                    required: true,
                    email: true
                },
            },
            // Specify validation error messages
            messages: {
                email: {
                    required: "Lütfen kullanıcı için email adresinizi doldurunuz.",
                    email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
                },
            },
            submitHandler: function () {
                var action = $('#userEmailChange').attr('action');

                miniSubmitForm('userEmailChange', '' + action + '', 'custom_noreload', '1');
            }
        });

        var userPasswordChange = $('#userPasswordChange');
        userPasswordChange.validate({
            errorPlacement: function (label, element) {
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
                    equalTo: "#newpassword"
                },
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
                },
            },
            submitHandler: function () {
                var action = $('#userPasswordChange').attr('action');

                miniSubmitForm('userPasswordChange', '' + action + '', 'custom_noreload', '1');
            }
        });

        var checkoutEditAddress = $('#checkout_edit_address');
        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg !== value;
        }, "Value must not equal arg.");
        checkoutEditAddress.validate({
            errorPlacement: function(label, element) {
                label.addClass('arrow');
                label.insertAfter(element);
            },
            wrapper: 'span',
            rules: {
                title: "required",
                receiver_name: "required",
                user_city: {
                    required: true,
                    valueNotEquals: "0"
                },
                user_town: {
                    required: true,
                    valueNotEquals: "0"
                },
                user_neighborhood: {
                    required: true,
                    valueNotEquals: "0"
                },
                address: "required",
                email: {
                    required: true,
                    email: true
                },
                phone: "required",
            },
            // Specify validation error messages
            messages: {
                title : "Lütfen adres tanımı giriniz.",
                receiver_name : "Lütfen Ad, Soyad / Firma giriniz.",
                user_city: {
                    required: "Lütfen ilinizi seçin.",
                    valueNotEquals: "Lütfen ilinizi seçin."
                },
                user_town: {
                    required: "Lütfen ilçenizi seçin.",
                    valueNotEquals: "Lütfen ilçenizi seçin."
                },
                user_neighborhood: {
                    required: "Lütfen mahallenizi seçin.",
                    valueNotEquals: "Lütfen mahallenizi seçin."
                },
                address : "Lütfen adres giriniz.",
                phone : "Lütfen telefon numaranızı giriniz.",
                email: {
                    required: "Lütfen email adresinizi doldurunuz.",
                    email: "Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz."
                },
            },  
            submitHandler: function() {
                checkoutEditAddressForm();
            }
        });

    }, false);
</script>

<?= view("admin/inculude/body_end") ?>


