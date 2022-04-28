
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/select2.min.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/perfect-scrollbar.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/jquery.fullscreen.min.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/vendors/chart.js"></script>
    <!-- Main Script -->
    <script src="<?= base_url() ?>/public/admin/assets/js/main.js?v=1.1" type="text/javascript"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/custom-chart.js" type="text/javascript"></script>

    <script src="<?= base_url() ?>/public/admin/assets/js/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/dropzone.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/form.js"></script>
    <script src="<?= base_url() ?>/public/admin/assets/js/jquery.validate.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
    <style>
        .dataTables_length {
            margin-bottom:20px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#dataTableStandart').DataTable({
                lengthMenu: [[25,50, -1], [25,50, "Hepsi"]],
                "order": [[ 0, "desc" ]],
                language: {
                    "sDecimal":        ",",
                    "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
                    "sInfo":           "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
                    "sInfoEmpty":      "Kayıt yok",
                    "sInfoFiltered":   "(_MAX_ kayıt içerisinden bulunan)",
                    "sInfoPostFix":    "",
                    "sInfoThousands":  ".",
                    "sLengthMenu":     "Sayfada _MENU_ kayıt göster",
                    "sLoadingRecords": "Yükleniyor...",
                    "sProcessing":     "İşleniyor...",
                    "sSearch":         "Ara:",
                    "sZeroRecords":    "Eşleşen kayıt bulunamadı",
                    "oPaginate": {
                        "sFirst":    "İlk",
                        "sLast":     "Son",
                        "sNext":     "Sonraki",
                        "sPrevious": "Önceki"
                    },
                    "oAria": {
                        "sSortAscending":  ": artan sütun sıralamasını aktifleştir",
                        "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
                    }
                },
                aoColumnDefs: [
                    { 'bSortable': false, 'aTargets': [ 'nosort' ] }
                ]
            });
        });
    </script>

