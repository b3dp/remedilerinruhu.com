<?= view("admin/inculude/start") ?>
    <title>Nest Dashboard</title>
<?= view("admin/inculude/head") ?>
<?= view("admin/inculude/body_start") ?>
<?= view("admin/inculude/sidebar") ?>
    <main class="main-wrap">
        <?= view("admin/inculude/header") ?>
            <section class="content-main">
                <div class="content-header">
                    <div>
                        <h2 class="content-title card-title">Sipariş Detayları</h2>
                        <p>Sipariş No: <?= $order->order_no ?></p>
                    </div>
                </div>
                <div class="card">
                    <header class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                                <span> <i class="material-icons md-calendar_today"></i> <b><?= timeTR($order->buy_at) ?></b> </span> <br />
                                <small class="text-muted">Sipariş No: <?= $order->order_no ?></small>
                            </div>
                        
                            <div class="col-lg-6 col-md-6 ms-auto text-md-end">
                                <a class="btn btn-secondary print ms-2" href="#"><i class="icon material-icons md-print"></i></a>
                            </div>
                        
                        </div>
                    </header>
                    <!-- card-header end// -->
                    <form id="mini_form" class="form" onsubmit="return false" action="<?= base_url('panelOrderEdit') ?>">
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <div class="card-body">
                            <div class="row mb-50 mt-20 order-info-wrap">
                                <div class="col-md-4">
                                    <article class="icontext align-items-start">
                                        <span class="icon icon-sm rounded-circle bg-primary-light">
                                            <i class="text-primary material-icons md-person"></i>
                                        </span>
                                        <div class="text">
                                            <h6 class="mb-1">Üye Bilgileri</h6>
                                            <p class="mb-1">
                                                <?= $user->full_name ?> <br />
                                                <?= $user->email ?> <br />
                                                <?= $user->phone ?>
                                            </p>
                                            <a target="_blank" href="user/detail/<?= $user->id ?>">Üye Profili</a>
                                        </div>
                                    </article>
                                </div>
                                <!-- col// -->
                                <div class="col-md-4">
                                    <article class="icontext align-items-start">
                                        <span class="icon icon-sm rounded-circle bg-primary-light">
                                            <i class="text-primary material-icons md-local_shipping"></i>
                                        </span>
                                        <div class="text">
                                            <h6 class="mb-1">Gönderim Adresi</h6>
                                            <p class="mb-1">
                                                Ad Soyad: <?= $delivery_address->receiver_name ?> <br />
                                                E-posta: <?= $delivery_address->email ?> <br />
                                                Cep Telefonu: <?= $delivery_address->phone ?> <br />
                                                İl/İlçe: <?= $delivery_city->CityName ?> / <?= $delivery_town->TownName ?> <br />
                                                Mahalle: <?= $delivery_neighborhood->NeighborhoodName ?> <br />
                                                Adres: <?= $delivery_address->address ?> <br />
                                            </p>
                                        </div>
                                    </article>
                                </div>
                                <!-- col// -->
                                <div class="col-md-4">
                                    <article class="icontext align-items-start">
                                        <span class="icon icon-sm rounded-circle bg-primary-light">
                                            <i class="text-primary material-icons md-place"></i>
                                        </span>
                                        <div class="text">
                                            <h6 class="mb-1">Fatura Adresi</h6>
                                            <p class="mb-1">
                                                Ad Soyad: <?= $billing_address->receiver_name ?> <br />
                                                E-posta: <?= $billing_address->email ?> <br />
                                                Cep Telefonu: <?= $billing_address->phone ?> <br />
                                                İl/İlçe: <?= $billing_city->CityName ?> / <?= $billing_town->TownName ?> <br />
                                                Mahalle: <?= $billing_neighborhood->NeighborhoodName ?> <br />
                                                Adres: <?= $billing_address->address ?> <br />
                                            </p>
                                        </div>
                                    </article>
                                </div>
                                <!-- col// -->
                            </div>
                            <!-- row // -->
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="selectimputarea d-none">Seçenekler</th>
                                                    <th >Ürün</th>
                                                    <th >Adet</th>
                                                    <th >Tutar</th>
                                                    <th >Ürün Durumu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orderDetail as $veriable) : ?>
                                                    <?php

                                                        if (!$cargo_key || !$invonce_no || !$cargo_count) {
                                                            if ($veriable->status != '5' && $veriable->status != '6') {
                                                                $status = $veriable->status;
                                                                $cargo_key = $veriable->cargo_key;
                                                                $invonce_no = $veriable->invonce_no;
                                                                $cargo_count = $veriable->cargo_count;
                                                            }else {
                                                                $orederCanceledNote = $veriable->orederCanceledNote;
                                                            }
                                                        }
                                                        
                                                        $row = $productDetailModels->c_one(['p.id' => $veriable->product_id, 'pa.id' => $veriable->variant_id ]);
                                                        if ($row) {
                                                            $product_id = $veriable->product_id;
                                                            $variant_barcode = $veriable->variant_id;
                                                        }else{
                                                            $product_id = $veriable->product_id;
                                                        }

                                                        if ($variant_barcode) {
                                                            $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.id' => $veriable->variant_id]);
                                                        }else{
                                                            $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id]);
                                                        }

                                                        $productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' =>$productCombinationOne->id], '1');
                                                        if (!$productPicture) {
                                                            $productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], '1');
                                                        }
                                                        $featureArray = explode(' - ', $row->attr);
                                                        $categoryArray = explode(',', $row->category_id);
                                                        if ($productCombinationOne->attr_id) {
                                                            $combinationIDArray = explode(' - ', $productCombinationOne->attr_id);
                                                        }
                                                        if ($productCombinationOne->attr_group_id) {
                                                            $combinationGroupIDArray = explode(' - ', $productCombinationOne->attr_group_id);
                                                        }
                                                        if ($productCombinationOne->attr) {
                                                            $combinationTitleArray = explode(' - ', $productCombinationOne->attr);
                                                        }
                                                        if ($productCombinationOne->attr_group) {
                                                            $combinationGroupTitleArray = explode(' - ', $productCombinationOne->attr_group);
                                                        }
                                                        foreach ($combinationGroupIDArray as $key => $val) {
                                                            $selectCombinationArray[$key]['id'] = $combinationIDArray[$key];
                                                            $selectCombinationArray[$key]['title'] = $combinationTitleArray[$key];
                                                            $selectCombinationArray[$key]['group_title'] = $combinationGroupTitleArray[$key];
                                                            $selectCombinationArray[$key]['group_id'] = $combinationGroupIDArray[$key];
                                                        }
                                                        foreach ($categoryArray as $item) {
                                                            $catFind = '';
                                                            if ($item) {
                                                                $catFind = $category->c_all_list('', $item);
                                                                if(!$catFind){
                                                                    $endCatID = $item;
                                                                }
                                                            }
                                                        }
                                                        if ($endCatID) {
                                                            $data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
                                                        }
                                                        $endCategoryFind = $category->c_one(["id" =>$endCatID]);

                                                        $brand = $row->b_title;
                                                        $colorTitle = $color_title;
                                                        $sizeTitle = $size_title;
                                                        if ($productCombinationOne->title) {
                                                            $title = $productCombinationOne->title;
                                                        }else{
                                                            $title = $row->title;
                                                        }

                                                        $singlePrice = $veriable->price;
                                                        $canceledProductCount = $veriable->cancellation_count;
                                                        $canceledPrice = $singlePrice * $veriable->cancellation_count;

                                                        $returnRequestProductCount = $veriable->return_request_count;
                                                        $returnProductCount = $veriable->return_count;
                                                        $returnPrice = $singlePrice * $veriable->return_count;
                                                        $totalPrice = $totalPrice + (($veriable->price * $veriable->piece) - $canceledPrice - $returnPrice);
                                                        $status = $veriable->status;
                                                        $orderDetailCount = $veriable->piece - $veriable->return_count - $veriable->cancellation_count ;
                                                    ?>
                                                    <?php if ((($veriable->piece - $veriable->return_request_count - $veriable->cancellation_count) > 0) ) : ?>
                                                        <tr>
                                                            <td class="selectimputarea d-none">
																<div class="align-items-center d-flex justify-content-center">
																	<div class="form-group">
																		<div class="custom-control custom-checkbox d-flex" style="margin-right: 15px;">
																			<input class="form-check-input" id="loginRemember<?= $veriable->id ?>" name="selectedProduct[]" value="<?= $veriable->id ?>" type="checkbox">
																			<label class="form-check-label" for="loginRemember<?= $veriable->id ?>"></label>
																		</div>
																	</div>
																	<div class="form-group ml-4">
																		<select class="form-select valid" name="select_piece[<?= $veriable->id ?>]"> 
																			<?php for($i = 1 ; $orderDetailCount >= $i ; $i++) : ?>
																				<option value="<?= $i ?>"><?= $i ?></option>
																			<?php endfor ?>
																		</select>
																	</div>
																</div>
															</td>
                                                            <td class="d-flex align-items-center">
                                                                <!--begin:: Avatar -->
                                                                <div class="symbol symbol-50px me-5 overflow-hidden me-3">
                                                                    <div class="symbol-label">
                                                                        <?php foreach ($productPicture as $value) : ?>
                                                                            <?php if (file_exists('uploads/products/'.$value->image.'') && $value->image) : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/<?= $value->image ?>">
                                                                                    <img src="<?= base_url() ?>/uploads/products/min/<?= $value->image ?>" class="img-sm img-thumbnail" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php else : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png">
                                                                                    <img src="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png" class="" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php endif ?>
                                                                        <?php endforeach ?>
                                                                    </div>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::User details-->
                                                                <div class="d-flex flex-column">
                                                                    <strong class="text-gray-800 "><?= $title ?> x <?= $veriable->piece - $canceledProductCount - $returnRequestProductCount ?></strong> 
                                                                    <?php if ($variant_barcode) : ?>
                                                                        <strong class="text-gray-800 ">Barkod : #<?= $veriable->variant_barcode ?></strong> 
                                                                    <?php endif ?>
                                                                    <?php foreach ($selectCombinationArray as $val) : ?>
                                                                        <?= $val['group_title'] ?>: <?= $val['title'] ?> <br>
                                                                    <?php endforeach ?>
                                                                </div>
                                                                <!--begin::User details-->
                                                            </td>
                                                            <td><?= $veriable->piece - $canceledProductCount - $returnRequestProductCount ?></td>
                                                            <td><?= number_format((($veriable->price * $veriable->piece) - $canceledPrice - ($singlePrice * $returnRequestProductCount)), 2); ?> ₺</td>
                                                            <td><?= orderProductStatusPanelView($veriable->status) ?></td>
                                                        </tr>
                                                    <?php endif ?>
                                                    
                                                    <?php if ($canceledProductCount) : ?>
                                                        <tr>
                                                            <td class="selectimputarea d-none">
                                                                <div class="align-items-center d-flex justify-content-center">
                                                                    
                                                                </div>
                                                            </td>
                                                            <td class="d-flex align-items-center">
                                                                <!--begin:: Avatar -->
                                                                <div class="symbol symbol-50px me-5 overflow-hidden me-3">
                                                                    <div class="symbol-label">
                                                                        <?php foreach ($productPicture as $value) : ?>
                                                                            <?php if (file_exists('uploads/products/'.$value->image.'') && $value->image) : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/<?= $value->image ?>">
                                                                                    <img src="<?= base_url() ?>/uploads/products/min/<?= $value->image ?>" class="img-sm img-thumbnail" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php else : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png">
                                                                                    <img src="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png" class="" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php endif ?>
                                                                        <?php endforeach ?>
                                                                    </div>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::User details-->
                                                                <div class="d-flex flex-column">
                                                                    <strong class="text-gray-800 "><?= $title ?> x <?= $canceledProductCount ?></strong> 
                                                                    <?php if ($variant_barcode) : ?>
                                                                        <strong class="text-gray-800 ">#<?= $veriable->variant_barcode ?></strong> 
                                                                    <?php endif ?>
                                                                </div>
                                                                <!--begin::User details-->
                                                            </td>
                                                            <td><?= $size_title ?></td>
                                                            <td><?= $color_title?></td>
                                                            <td><?= $canceledProductCount ?></td>
                                                            <td><?= number_format($canceledPrice, 2) ?>₺</td>
                                                            <td>
                                                                <p class="badge badge-danger p-4 mb-0 font-size-lg font-weight-bold">
                                                                    Ürün İptal Edildi
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php endif ?>

                                                    <?php if (($returnRequestProductCount - $returnProductCount)) : ?>
                                                        <tr>
                                                            <td class="selectimputarea d-none">
                                                                <div class="align-items-center d-flex justify-content-center">
                                                                    
                                                                </div>
                                                            </td>
                                                            <td class="d-flex align-items-center">
                                                                <!--begin:: Avatar -->
                                                                <div class="symbol symbol-50px me-5 overflow-hidden me-3">
                                                                    <div class="symbol-label">
                                                                        <?php foreach ($productPicture as $value) : ?>
                                                                            <?php if (file_exists('uploads/products/'.$value->image.'') && $value->image) : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/<?= $value->image ?>">
                                                                                    <img src="<?= base_url() ?>/uploads/products/min/<?= $value->image ?>" class="img-sm img-thumbnail" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php else : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png">
                                                                                    <img src="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png" class="" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php endif ?>
                                                                        <?php endforeach ?>
                                                                    </div>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::User details-->
                                                                <div class="d-flex flex-column">
                                                                    <strong class="text-gray-800 "><?= $title ?> x <?= $returnRequestProductCount - $returnProductCount ?></strong> 
                                                                    <?php if ($variant_barcode) : ?>
                                                                        <strong class="text-gray-800 ">#<?= $veriable->variant_barcode ?></strong> 
                                                                    <?php endif ?>
                                                                </div>
                                                                <!--begin::User details-->
                                                            </td>
                                                            <td><?= $size_title ?></td>
                                                            <td><?= $color_title?></td>
                                                            <td><?= $returnRequestProductCount - $returnProductCount ?></td>
                                                            <td><?= number_format(($singlePrice * ($returnRequestProductCount - $returnProductCount)), 2); ?> ₺</td>
                                                            <td>
                                                                <p class="badge badge-warning p-4 mb-0 font-size-lg font-weight-bold">
                                                                    İade Talebi Gönderildi.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php endif ?>

                                                    <?php if (($returnProductCount)) : ?>
                                                        <tr>
                                                            <td class="selectimputarea d-none">
                                                                <div class="align-items-center d-flex justify-content-center">
                                                                    
                                                                </div>
                                                            </td>
                                                            <td class="d-flex align-items-center">
                                                                <!--begin:: Avatar -->
                                                                <div class="symbol symbol-50px me-5 overflow-hidden me-3">
                                                                    <div class="symbol-label">
                                                                        <?php foreach ($productPicture as $value) : ?>
                                                                            <?php if (file_exists('uploads/products/'.$value->image.'') && $value->image) : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/<?= $value->image ?>">
                                                                                    <img src="<?= base_url() ?>/uploads/products/min/<?= $value->image ?>" class="img-sm img-thumbnail" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php else : ?>
                                                                                <a target="_blank" class="colorPicture" href="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png">
                                                                                    <img src="<?= base_url() ?>/uploads/products/no_image/bilt_no_product_500x750.png" class="" alt="<?= $title ?> <?= $size_title ?> <?= $color_title?>">
                                                                                </a>
                                                                            <?php endif ?>
                                                                        <?php endforeach ?>
                                                                    </div>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::User details-->
                                                                <div class="d-flex flex-column">
                                                                    <strong class="text-gray-800 "><?= $title ?> x <?= $returnProductCount ?></strong> 
                                                                    <?php if ($variant_barcode) : ?>
                                                                        <strong class="text-gray-800 ">#<?= $veriable->variant_barcode ?></strong> 
                                                                    <?php endif ?>
                                                                </div>
                                                                <!--begin::User details-->
                                                            </td>
                                                            <td><?= $size_title ?></td>
                                                            <td><?= $color_title?></td>
                                                            <td><?= $returnProductCount ?></td>
                                                            <td><?= number_format(($returnPrice), 2); ?> ₺</td>
                                                            <td>
                                                                <p class="badge badge-success p-4 mb-0 font-size-lg font-weight-bold">
                                                                    İade Talebi Kabul Edildi.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php endif ?>

                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- table-responsive// -->
                                    <div class="mb-3">
                                        <label class="mb-3">Müşteri Notu</label>
                                        <textarea class="form-control" name="notes" id="notes" placeholder="Müşteri Notu"><?= $order->order_note ?></textarea>
                                    </div>
                                </div>
                                <!-- col// -->
                                <div class="col-lg-1"></div>
                                <div class="col-lg-4">
                                    <div class="h-25">
                                        <div class="mb-3">
                                            <label class="mb-3">Sipariş Durumu</label>
                                            <select class="form-select mb-lg-0 mr-5" name="status">
                                                <option value="1" <?= $order->status == '1' ? 'selected' : '' ?>>Yeni Sipariş Alındı</option>
                                                <option value="2" <?= $order->status == '2' ? 'selected' : '' ?>>Siparişiniz Onaylandı ve Hazırlanıyor</option>
                                                <option value="3" <?= $order->status == '3' ? 'selected' : '' ?>>Kargoda</option>
                                                <option value="4" <?= $order->status == '4' ? 'selected' : '' ?>>Tamamlandı</option>
                                                <option value="5" <?= $order->status == '5' ? 'selected' : '' ?>>İptal Edildi</option>
                                            </select>
                                        </div>

                                        <div id="cargoInfoArea" class="mb-8 <?= $order->status == '3' || $order->status == '4' ? '' : 'd-none' ?>">
                                            <div class="form-group mb-3">
                                                <label for="invonceNo">Fatura Numarası</label>
                                                <input class="form-control" <?= $status == '3' || $status == '4' ? 'disabled' : '' ?> type="text" id="invonceNo" name="invonceNo" value="<?= $invonce_no ?>">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="cargoCount">Kargo Sayısı</label>
                                                <input class="form-control" <?= $status == '3' || $status == '4' ? 'disabled' : '' ?> type="text" id="cargoCount" name="cargoCount" value="<?= $cargo_count ?>">
                                            </div>
                                            <div class="mb-0 mb-3">
                                                <button type="submit" class="btn btn-facebook" id="cargoReceipt">
                                                    Kargo Fişini Yazdır
                                                </button>
                                            </div>
                                        </div>
                                        <div id="canceledInfoArea" class="mb-3 <?= $status == '5' ? '' : 'd-none' ?>">
											<div class="form-group mb-3">
												<label class="mb-3" for="orederCanceledNote">İptal Sebebiniz</label>
												<textarea class="form-control form-control-solid" <?= $status != '5' ? 'disabled' : '' ?> type="text" id="orederCanceledNote" name="orederCanceledNote"><?= $orederCanceledNote ?></textarea>
											</div>
										</div>
                                        <button class="btn btn-primary">Kaydet</button>
                                    </div>
                                </div>
                                <!-- col// -->
                            </div>
                        </div> 
                    </form>
                    <!-- card-body end// -->
                </div>
                <!-- card end// -->
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<script>

    $('#mini_form select[name="status"]').change(function() {
        var value = $('#mini_form select[name="status"]').val();
        if (value == '5') {
            $('.selectimputarea').removeClass('d-none');
        }else{
            $('.selectimputarea').addClass('d-none');
        }
        if (value == '3' || value == '4') {
            $('#cargoInfoArea').removeClass('d-none');
            $('#canceledInfoArea').addClass('d-none');
            $('#orederCanceledNote').attr('disabled','disabled');
        }else if (value == '5') {
            $('#canceledInfoArea').removeClass('d-none');
            $('#orederCanceledNote').removeAttr('disabled','disabled');
            $('#cargoInfoArea').addClass('d-none');
        }else{
            $('#orederCanceledNote').attr('disabled','disabled');
            $('#cargoInfoArea').addClass('d-none');
            $('#canceledInfoArea').addClass('d-none');
        }
    });

</script>
<?= view("admin/inculude/body_end") ?>
