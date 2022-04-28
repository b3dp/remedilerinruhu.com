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
                        <h2 class="content-title card-title">İndirim Kuponları</h2>
                    </div>
                    <div>
                        <a href="coupon/add" class="btn btn-primary btn-sm rounded">İndirim Kuponu Ekle</a>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0" id="dataTableStandart">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
											<th >Başlığı</th> 
											<th >Kodu</th> 
											<th >İndirim Türü</th> 
											<th >Oranı</th> 
											<th >Türü</th> 
											<th >Adeti</th> 
											<th >S.K.T</th>
											<th >Durum</th>
											<th >İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php foreach ($coupons as $row) : ?>
											<tr> 
												<td>
													<strong class="text-gray-800 "><?= $row->id ?></strong> 
												</td>
												<td>
													<strong class="text-gray-800 "><?= $row->title ?></strong> 
												</td>
												<td>
													<div class="badge badge-soft-secondary fw-bolder p-2"><?= $row->code ?></div>
												</td>
												<td>
													<div class="badge badge-soft-success fw-bolder p-2">
														<?php if ($row->discount_type == '1') : ?>
															Yüzdelik İndirim
														<?php else : ?>
															Sabit İndirim
														<?php endif ?>	
													</div>
												</td>
												<td>
													<div class="badge badge-soft-success fw-bolder p-2">
														<?php if ($row->discount_type == '1') : ?>
															<?= $row->discount ?>%
														<?php else : ?>
															<?= $row->discount ?> TL
														<?php endif ?>	
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder">
														<?php if ($row->coupon_type == '1') : ?>
															Ürün Seçenekli
														<?php elseif ($row->coupon_type == '2') : ?>
															Kategori ve Markaya Özel
														<?php elseif ($row->coupon_type == '0') : ?>
															Standart Kupon
														<?php endif ?>	
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder w-100">
														<?= $row->piece ?> Adet
													</div>
												</td>
												<td>
													<div class="badge badge-soft-info fw-bolder"><?= $row->end_at ? timeTR($row->end_at) : 'Sınırı Yok2' ?></div>
												</td>
												<td>
													<div class="d-flex form-check-custom form-check-solid form-switch justify-content-center mb-2">
														<input class="form-check-input" type="checkbox" onchange="miniSingleStatus(this, '<?= $row->id ?>', '<?= base_url('couponStatus') ?>');" <?= $row->is_active == '1' ? 'checked' : '' ?> value="1">
													</div>
												</td>
                                                <td>
                                                    <a href="coupon/edit/<?= $row->id ?>" class="btn btn-sm font-sm rounded btn-brand mb-1"> <i class="material-icons md-edit"></i> Düzenle </a>
                                                    <a href="javascript:void(0)" onclick="miniSingle('<?= $row->id ?>','<?= base_url('couponDelete') ?>');" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-delete_forever"></i> Sil </a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- card-body end// -->
                </div>
                <!-- card end// -->
                <div class="pagination-area mt-30 mb-50">
                    <?php if ($totalPage > 1) : ?>
                        <?= paginate('product/list', $page, $totalPage, $filter) ?>
                    <?php endif ?>
                </div>
            </section>
        <?= view("admin/inculude/footer") ?>
    </main>
<?= view("admin/inculude/script") ?>
<?= view("admin/inculude/body_end") ?>
