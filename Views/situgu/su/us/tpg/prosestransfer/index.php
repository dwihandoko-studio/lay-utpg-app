<?= $this->extend('t-situgu/su/index'); ?>

<?= $this->section('content'); ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="mb-sm-0 font-size-18">DATA USULAN TPG PROSES TRANSFER</h4>
                        </div>
                        <!-- <div class="col-6">
                            <div class="mb-3">
                                <label for="_filter_tw" class="col-form-label">Filter TW:</label>
                                <select class="form-control filter-tw" id="_filter_tw" name="_filter_tw" required>
                                    <option value="">--Pilih--</option>
                                    <?php if (isset($tws)) {
                                        if (count($tws) > 0) {
                                            foreach ($tws as $key => $value) { ?>
                                                <option value="<?= $value->id ?>">Tahun <?= $value->tahun ?> - TW. <?= $value->tw ?></option>
                                    <?php }
                                        }
                                    } ?>
                                </select>
                                <div class="help-block _filter_tw"></div>
                            </div>
                        </div> -->
                    </div>
                    <!-- <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:actionSyncAll(this);" class="btn btn-primary btn-rounded waves-effect waves-light">Syncrone Semua Data PTK</a></li>
                        </ol>
                    </div> -->

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="card-title">Data Usulan TPG Proses Transfer</h4>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="_filter_tw" class="col-form-label">Filter TW:</label>
                                    <select class="form-control filter-tw" id="_filter_tw" name="_filter_tw" required>
                                        <option value="">--Pilih--</option>
                                        <?php if (isset($tws)) {
                                            if (count($tws) > 0) {
                                                foreach ($tws as $key => $value) { ?>
                                                    <option value="<?= $value->id ?>">Tahun <?= $value->tahun ?> - TW. <?= $value->tw ?></option>
                                        <?php }
                                            }
                                        } ?>
                                    </select>
                                    <div class="help-block _filter_tw"></div>
                                </div>
                            </div>
                            <!-- <div class="col-6">
                                <div class="mb-3">
                                    <label for="_filter_tw" class="col-form-label">Filter Status:</label>
                                    <select class="form-control filter-tw" id="_filter_tw" name="_filter_tw" required>
                                        <option value="">--Pilih--</option>
                                        <option value="0">Antrian</option>
                                        <option value="1">Ditolak</option>
                                        <option value="pghm">PGHM</option>
                                    </select>
                                    <div class="help-block _filter_tw"></div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="data-datatables" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th data-orderable="false">#</th>
                                    <th data-orderable="false">Aksi</th>
                                    <th>NAMA</th>
                                    <th>NIK</th>
                                    <th>NUPTK</th>
                                    <th>NO SKTP</th>
                                    <th>NO URUT SK</th>
                                    <th>TANGGAL PROSES TRANSFER</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<!-- Modal -->
<div id="content-detailModal" class="modal fade content-detailModal" tabindex="-1" role="dialog" aria-labelledby="content-detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content modal-content-loading">
            <div class="modal-header">
                <h5 class="modal-title" id="content-detailModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="contentBodyModal">
            </div>
        </div>
    </div>
</div>
<div id="content-tolakModal" class="modal fade content-tolakModal" tabindex="-1" role="dialog" aria-labelledby="content-tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-loading-tolak">
            <div class="modal-header">
                <h5 class="modal-title" id="content-tolakModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="contentTolakBodyModal">
            </div>
        </div>
    </div>
</div>
<!-- end modal -->
<?= $this->endSection(); ?>

<?= $this->section('scriptBottom'); ?>
<script src="<?= base_url() ?>/assets/libs/select2/js/select2.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/libs/dropzone/min/dropzone.min.js"></script>

<script>
    function actionDetail(id, id_ptk, tw, nama) {
        $.ajax({
            url: "./detail",
            type: 'POST',
            data: {
                id: id,
                id_ptk: id_ptk,
                tw: tw,
                nama: nama,
            },
            dataType: 'JSON',
            beforeSend: function() {
                $('div.main-content').block({
                    message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                });
            },
            success: function(resul) {
                $('div.main-content').unblock();
                if (resul.status !== 200) {
                    Swal.fire(
                        'Failed!',
                        resul.message,
                        'warning'
                    );
                } else {
                    $('#content-detailModalLabel').html('DETAIL USULAN PTK ' + nama);
                    $('.contentBodyModal').html(resul.data);
                    $('.content-detailModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    });
                    $('.content-detailModal').modal('show');
                }
            },
            error: function() {
                $('div.main-content').unblock();
                Swal.fire(
                    'Failed!',
                    "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                    'warning'
                );
            }
        });
    }

    function changeValidation(event) {
        $('.' + event).css('display', 'none');
    };

    function inputFocus(id) {
        const color = $(id).attr('id');
        $(id).removeAttr('style');
        $('.' + color).html('');
    }

    function inputChange(event) {
        console.log(event.value);
        if (event.value === null || (event.value.length > 0 && event.value !== "")) {
            $(event).removeAttr('style');
        } else {
            $(event).css("color", "#dc3545");
            $(event).css("border-color", "#dc3545");
            // $('.nama_instansi').html('<ul role="alert" style="color: #dc3545;"><li style="color: #dc3545;">Isian tidak boleh kosong.</li></ul>');
        }
    }

    function ambilId(id) {
        return document.getElementById(id);
    }

    $('#content-detailModal').on('click', '.btn-remove-preview-image', function(event) {
        $('.imagePreviewUpload').removeAttr('src');
        document.getElementsByName("_file")[0].value = "";
    });

    function initSelect2(event, parrent) {
        $('#' + event).select2({
            dropdownParent: parrent
        });
    }

    $(document).ready(function() {

        let tableDatatables = $('#data-datatables').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "./getAll",
                "type": "POST",
                "data": function(data) {
                    // data.tw = '<?= $tw->id ?>';
                    data.tw_active = '<?= $tw->id ?>';
                    data.tw = $('#_filter_tw').val();
                }
            },
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
            }],
        });

        $('#_filter_tw').change(function() {
            tableDatatables.draw();
        });

    });
</script>
<?= $this->endSection(); ?>

<?= $this->section('scriptTop'); ?>
<link href="<?= base_url() ?>/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url() ?>/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script src="<?= base_url() ?>/assets/libs/ckeditor5-custom/build/ckeditor.js"></script>
<link href="<?= base_url() ?>/assets/libs/dropzone/min/dropzone.min.css" rel="stylesheet" type="text/css" />

<style>
    .preview-image-upload {
        position: relative;
    }

    .preview-image-upload .imagePreviewUpload {
        max-width: 300px;
        max-height: 300px;
        cursor: pointer;
    }

    .preview-image-upload .btn-remove-preview-image {
        display: none;
        position: absolute;
        top: 5px;
        left: 5px;
        background-color: #555;
        color: white;
        font-size: 16px;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
    }

    .imagePreviewUpload:hover+.btn-remove-preview-image,
    .btn-remove-preview-image:hover {
        display: block;
    }

    .ul-custom-style-sub-menu-action {
        list-style: none;
        padding-left: 0.5rem;
        border: 1px solid #ffffff2e;
        padding-top: 0.5rem;
        padding-right: 0.5rem;
        border-radius: 1.5rem;
    }

    .li-custom-style-sub-menu-action {
        border: 1px solid white;
        display: inline-block !important;
        padding: 0.3rem 0.5rem 0rem 0.3rem;
        margin-right: 0.3rem;
        margin-bottom: 0.5rem;
        border-radius: 2rem;
    }

    .custom-style-sub-menu-action {
        font-size: 1em;
        line-height: 1;
        height: 24px;
        color: #f6f6f6;
        display: inline-block;
        position: relative;
        text-align: center;
        font-weight: 500;
        box-sizing: border-box;
        margin-top: -15px;
        vertical-align: -webkit-baseline-middle;
    }
</style>
<?= $this->endSection(); ?>