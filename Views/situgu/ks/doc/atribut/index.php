<?= $this->extend('t-situgu/ks/index'); ?>

<?= $this->section('content'); ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">KELENGKAPAN DOKUMEN ATRIBUT</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:actionAdd(this);" class="btn btn-primary btn-rounded waves-effect waves-light">Tambah Data Atribut</a></li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="card-title">Data Kelengkapan Dokumen Atribut || <?= $ptk->nama ?> (NUPTK: <?= $ptk->nuptk ?>)</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="data-datatables" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th rowspan="3" data-orderable="false">#</th>
                                    <th rowspan="3" data-orderable="false">Aksi</th>
                                    <th colspan="2" data-orderable="false">
                                        <div class="text-center">Tahun TW</div>
                                    </th>
                                    <th colspan="7" data-orderable="false">
                                        <div class="text-center">Riwayat Kepegawaian</div>
                                    </th>
                                    <th colspan="9" data-orderable="false">
                                        <div class="text-center">Dokumen Atribut</div>
                                    </th>
                                    <th rowspan="3">Status</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">Tahun</th>
                                    <th rowspan="2">TW</th>
                                    <th rowspan="2">Pangkat</th>
                                    <th rowspan="2">No SK</th>
                                    <th rowspan="2">TMT</th>
                                    <th rowspan="2">Tanggal</th>
                                    <th colspan="2">Masa Kerja</th>
                                    <th rowspan="2">Gaji Pokok</th>
                                    <th rowspan="2">Pangkat</th>
                                    <th rowspan="2">KGB</th>
                                    <th rowspan="2">Pernyataan</th>
                                    <th rowspan="2">SKP / PKG</th>
                                    <th rowspan="2">Cuti</th>
                                    <th rowspan="2">Pensiun</th>
                                    <th rowspan="2">Kematian</th>
                                    <th rowspan="2">Lainnya</th>
                                    <th rowspan="2">Pembaharuan Doc</th>
                                </tr>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Bulan</th>
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
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
    function openFileInNewTab(url) {
        const newTab = window.open(url, '_blank');
        if (newTab) {
            newTab.focus();
        } else {
            alert('Please allow pop-ups for this site');
        }
    }

    function actionAdd(event) {
        $.ajax({
            url: "./add",
            type: 'POST',
            data: {
                action: 'add',
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
                    $('#content-detailModalLabel').html('TAMBAH DATA ATRIBUT');
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

    async function actionPembaharuan(title, bulan, tw, id_ptk, fromOld = 0) {
        if (fromOld === 1) {
            const {
                value: pilihanUpload
            } = await Swal.fire({
                title: 'Silahkan Pilih Metode Upload Dokumen:',
                input: 'select',
                inputOptions: {
                    // 'old_dokumen': 'Gunakan Dokumen Sebelumnya',
                    'upload_dokumen_baru': 'Upload Dokumen Baru',
                    // 'lihat_dokumen_sebelumnya': 'Lihat Dokumen Sebelumnya'
                },
                inputPlaceholder: '-- Pilih Metode ---',
                showCancelButton: true,
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value === '' || value === undefined) {
                            resolve('Silahkan pilih metode...')
                        } else {
                            resolve()
                        }
                    })
                }
            })

            if (pilihanUpload) {
                if (pilihanUpload === "old_dokumen") {
                    Swal.fire({
                        title: 'Apakah anda yakin ingin menggunakan lampiran dokumen sebelumnya?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Gunakan.',
                        denyButtonText: `Lihat Dokumen`,
                        closeButtonText: `Batal`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "./gunakanDokumenSebelumnya",
                                type: 'POST',
                                data: {
                                    title: title,
                                    bulan: bulan,
                                    tw: tw,
                                    id_ptk: id_ptk,
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
                                        if (resul.status !== 201) {
                                            if (resul.status === 401) {
                                                Swal.fire(
                                                    'Failed!',
                                                    resul.message,
                                                    'warning'
                                                ).then((valRes) => {
                                                    reloadPage();
                                                });
                                            } else {
                                                Swal.fire(
                                                    'GAGAL!',
                                                    resul.message,
                                                    'warning'
                                                );
                                            }
                                        } else {
                                            Swal.fire(
                                                'Peringatan!',
                                                resul.message,
                                                'success'
                                            ).then((valRes) => {
                                                reloadPage();
                                            })
                                        }
                                    } else {
                                        Swal.fire(
                                            'SELAMAT!',
                                            resul.message,
                                            'success'
                                        ).then((valRes) => {
                                            reloadPage();
                                        })
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
                        } else if (result.isDenied) {
                            $.ajax({
                                url: "./getDokumenSebelumnya",
                                type: 'POST',
                                data: {
                                    bulan: bulan,
                                    tw: tw,
                                    id_ptk: id_ptk,
                                    title: title,
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
                                        openFileInNewTab(resul.data.doc);
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
                    })
                } else if (pilihanUpload === "upload_dokumen_baru") {
                    $.ajax({
                        url: "./formuploadpembaharuan",
                        type: 'POST',
                        data: {
                            bulan: bulan,
                            tw: tw,
                            id_ptk: id_ptk,
                            title: title,
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
                                $('#content-detailModalLabel').html('Upload Lampiran ' + title);
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
                } else {
                    $.ajax({
                        url: "./getDokumenSebelumnya",
                        type: 'POST',
                        data: {
                            bulan: bulan,
                            tw: tw,
                            id_ptk: id_ptk,
                            title: title,
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
                                openFileInNewTab(resul.data.doc);
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
            }
        } else {
            $.ajax({
                url: "./formuploadpembaharuan",
                type: 'POST',
                data: {
                    bulan: bulan,
                    tw: tw,
                    id_ptk: id_ptk,
                    title: title,
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
                        $('#content-detailModalLabel').html('Upload Lampiran ' + title);
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
    }

    async function actionUpload(title, bulan, tw, id_ptk, fromOld = 0) {
        if (fromOld === 1) {
            const {
                value: pilihanUpload
            } = await Swal.fire({
                title: 'Silahkan Pilih Metode Upload Dokumen:',
                input: 'select',
                inputOptions: {
                    'old_dokumen': 'Gunakan Dokumen Sebelumnya',
                    'upload_dokumen_baru': 'Upload Dokumen Baru',
                    'lihat_dokumen_sebelumnya': 'Lihat Dokumen Sebelumnya'
                },
                inputPlaceholder: '-- Pilih Metode ---',
                showCancelButton: true,
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value === '' || value === undefined) {
                            resolve('Silahkan pilih metode...')
                        } else {
                            resolve()
                        }
                    })
                }
            })

            if (pilihanUpload) {
                if (pilihanUpload === "old_dokumen") {
                    Swal.fire({
                        title: 'Apakah anda yakin ingin menggunakan lampiran dokumen sebelumnya?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Gunakan.',
                        denyButtonText: `Lihat Dokumen`,
                        closeButtonText: `Batal`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "./gunakanDokumenSebelumnya",
                                type: 'POST',
                                data: {
                                    title: title,
                                    bulan: bulan,
                                    tw: tw,
                                    id_ptk: id_ptk,
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
                                        if (resul.status !== 201) {
                                            if (resul.status === 401) {
                                                Swal.fire(
                                                    'Failed!',
                                                    resul.message,
                                                    'warning'
                                                ).then((valRes) => {
                                                    reloadPage();
                                                });
                                            } else {
                                                Swal.fire(
                                                    'GAGAL!',
                                                    resul.message,
                                                    'warning'
                                                );
                                            }
                                        } else {
                                            Swal.fire(
                                                'Peringatan!',
                                                resul.message,
                                                'success'
                                            ).then((valRes) => {
                                                reloadPage();
                                            })
                                        }
                                    } else {
                                        Swal.fire(
                                            'SELAMAT!',
                                            resul.message,
                                            'success'
                                        ).then((valRes) => {
                                            reloadPage();
                                        })
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
                        } else if (result.isDenied) {
                            $.ajax({
                                url: "./getDokumenSebelumnya",
                                type: 'POST',
                                data: {
                                    bulan: bulan,
                                    tw: tw,
                                    id_ptk: id_ptk,
                                    title: title,
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
                                        openFileInNewTab(resul.data.doc);
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
                    })
                } else if (pilihanUpload === "upload_dokumen_baru") {
                    $.ajax({
                        url: "./formupload",
                        type: 'POST',
                        data: {
                            bulan: bulan,
                            tw: tw,
                            id_ptk: id_ptk,
                            title: title,
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
                                $('#content-detailModalLabel').html('Upload Lampiran ' + title);
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
                } else {
                    $.ajax({
                        url: "./getDokumenSebelumnya",
                        type: 'POST',
                        data: {
                            bulan: bulan,
                            tw: tw,
                            id_ptk: id_ptk,
                            title: title,
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
                                openFileInNewTab(resul.data.doc);
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
            }
        } else {
            $.ajax({
                url: "./formupload",
                type: 'POST',
                data: {
                    bulan: bulan,
                    tw: tw,
                    id_ptk: id_ptk,
                    title: title,
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
                        $('#content-detailModalLabel').html('Upload Lampiran ' + title);
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
    }

    function actionHapusFile(title, bulan, tw, id_ptk, old) {
        Swal.fire({
            title: 'Apakah anda yakin ingin menghapus lampiran file ini?',
            text: "Hapus Lampiran File: " + title,
            showCancelButton: true,
            icon: 'question',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./hapusfile",
                    type: 'POST',
                    data: {
                        title: title,
                        bulan: bulan,
                        old: old,
                        tw: tw,
                        id_ptk: id_ptk,
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
                            if (resul.status !== 201) {
                                if (resul.status === 401) {
                                    Swal.fire(
                                        'Failed!',
                                        resul.message,
                                        'warning'
                                    ).then((valRes) => {
                                        reloadPage();
                                    });
                                } else {
                                    Swal.fire(
                                        'GAGAL!',
                                        resul.message,
                                        'warning'
                                    );
                                }
                            } else {
                                Swal.fire(
                                    'Peringatan!',
                                    resul.message,
                                    'success'
                                ).then((valRes) => {
                                    reloadPage();
                                })
                            }
                        } else {
                            Swal.fire(
                                'SELAMAT!',
                                resul.message,
                                'success'
                            ).then((valRes) => {
                                reloadPage();
                            })
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
        })
    }

    function actionEditPang(id) {
        $.ajax({
            url: "./edit",
            type: 'POST',
            data: {
                id: id,
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
                    $('#content-detailModalLabel').html('Edit Pangkat Inpassing');
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

    function actionEditFile(title, bulan, tw, id_ptk, old) {
        $.ajax({
            url: "./editformupload",
            type: 'POST',
            data: {
                bulan: bulan,
                tw: tw,
                id_ptk: id_ptk,
                title: title,
                old: old,
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
                    $('#content-detailModalLabel').html('Edit Lampiran ' + title);
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
        initSelect2("filter_tw", ".main-content");
        let tableDatatables = $('#data-datatables').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "./getAll",
                "type": "POST",
            },
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
            }],
        });

        $('#filter_tw').change(function() {
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