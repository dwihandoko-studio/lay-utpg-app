<?= form_open('./savetagihan', ['class' => 'formsimpanbanyak']) ?>

<input type="hidden" value="<?= $tw_active ?>" id="id" name="id" readonly>
<?php if (count($datas) > 0) { ?>
    <div class="tombol-simpan-data" style="display: block;">
        <?php if (isset($prosesed_ajuan)) { ?>
            <?php if ($prosesed_ajuan > 0) { ?>
                <a class="btn btn-sm btn-primary waves-effect waves-light" target="_blank" href="./download?tb=<?= $tw_active ?>"><i class="fas fa-download font-size-16 align-middle me-2"></i> DOWNLOAD DATA USULAN TAGIHAN</a>
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnaddtagihan"><i class="fas fa-plus-circle font-size-16 align-middle me-2"></i> TAMBAH</button> &nbsp;&nbsp;
                <?php if (isset($prosesed_ajuan_tambahan)) { ?>
                    <?php if ($prosesed_ajuan_tambahan > 0) { ?>
                        <a class="btn btn-sm btn-warning waves-effect waves-light" href="javascript:actionAjukanProsesTagihan(this, '<?= $tw->tahun ?>', '<?= $tw->bulan ?>', '<?= $tw_active ?>');"><i class="fas fa-map-signs font-size-16 align-middle me-2"></i> AJUKAN PROSES TAGIHAN</a>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <!-- <button type="submit" class="btn btn-sm btn-success waves-effect waves-light btnsimpanbanyak"><i class="bx bx-save font-size-16 align-middle me-2"></i> SIMPAN</button> &nbsp;&nbsp; -->
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnaddtagihan"><i class="fas fa-plus-circle font-size-16 align-middle me-2"></i> TAMBAH</button> &nbsp;&nbsp;
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnuploadtagihan"><i class="fas fa-upload font-size-16 align-middle me-2"></i> UPLOAD</button> &nbsp;&nbsp;
                <a class="btn btn-sm btn-warning waves-effect waves-light" href="javascript:actionAjukanProsesTagihan(this, '<?= $tw->tahun ?>', '<?= $tw->bulan ?>', '<?= $tw_active ?>');"><i class="fas fa-map-signs font-size-16 align-middle me-2"></i> AJUKAN PROSES TAGIHAN</a>
            <?php } ?>
        <?php } else { ?>
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnaddtagihan"><i class="fas fa-plus-circle font-size-16 align-middle me-2"></i> TAMBAH</button> &nbsp;&nbsp;
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnuploadtagihan"><i class="fas fa-upload font-size-16 align-middle me-2"></i> UPLOAD</button> &nbsp;&nbsp;
            <!-- <button type="submit" class="btn btn-sm btn-success waves-effect waves-light btnsimpanbanyak"><i class="bx bx-save font-size-16 align-middle me-2"></i> SIMPAN</button> &nbsp;&nbsp; -->
            <a class="btn btn-sm btn-warning waves-effect waves-light" href="javascript:actionAjukanProsesTagihan(this, '<?= $tw->tahun ?>', '<?= $tw->bulan ?>', '<?= $tw_active ?>');"><i class="fas fa-map-signs font-size-16 align-middle me-2"></i> AJUKAN PROSES TAGIHAN</a>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="tomboh-simpan-data" style="display: block;">
        <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnaddtagihan"><i class="fas fa-plus-circle font-size-16 align-middle me-2"></i> TAMBAH</button> &nbsp;&nbsp;
        <a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionAmbilTagihan(this);"><i class="fas fa-assistive-listening-systems font-size-16 align-middle me-2"></i> Ambil Data Dari Bulan Sebelumnya</a>&nbsp;&nbsp;
        <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btnuploadtagihan"><i class="fas fa-upload font-size-16 align-middle me-2"></i> UPLOAD</button> &nbsp;&nbsp;
    </div>
<?php } ?>
<table id="data-datatables" class="table table-bordered w-100 tb-datatables">
    <thead>
        <tr>
            <th data-orderable="false" width="19%">Action</th>
            <th data-orderable="false" width="19%">Nama</th>
            <th data-orderable="false" width="14.5%">NIP</th>
            <th data-orderable="false">Instansi</th>
            <th data-orderable="false">Kecamatan</th>
            <th data-orderable="false" width="11%">Besar Pinjaman</th>
            <th data-orderable="false" width="10%">Jumlah Tagihan</th>
            <th data-orderable="false" width="7.5%">Jml Bulan<br>Angs</th>
            <th data-orderable="false" width="7%">Angs Ke</th>
            <th data-orderable="false"> </th>
        </tr>
    </thead>
    <tbody class="formtambah">
        <?php if (isset($datas)) { ?>
            <?php if (count($datas) > 0) { ?>
                <?php foreach ($datas as $key => $value) { ?>
                    <?php if ($key < 1) { ?>
                        <tr data-id="<?= $value->id; ?>" data-fullname="<?= $value->nama; ?>">
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:actionEditTagihan('<?= $value->id; ?>', <?= $value->status_ajuan ?>);"><i class="fas fa-edit font-size-16 align-middle"></i> &nbsp;Edit</a>
                                        <a class="dropdown-item" href="javascript:actionHapus('<?= $value->id; ?>', <?= $value->status_ajuan ?>, '<?= $value->nama; ?>');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Hapus</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="form-control filter-pegawai" id="_filter_pegawai_<?= $key + 1; ?>" name="_filter_pegawai[]" data-id="<?= $key + 1; ?>" onchange="changePegawai(this)" aria-readonly="">
                                    <option value="<?= $value->id_pegawai; ?>" selected><?= $value->nama; ?></option>
                                </select>
                            </td>
                            <td>
                                <?= $value->nip; ?>
                                <input class="form-control" type="hidden" value="<?= $value->nip; ?>" id="nip_<?= $key + 1; ?>" name="nip[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" value="<?= $value->nama_instansi; ?>" id="instansi_<?= $key + 1; ?>" name="instansi[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" value="<?= $value->nama_kecamatan; ?>" id="kecamatan_<?= $key + 1; ?>" name="kecamatan[]" readonly>
                            </td>
                            <td>
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->besar_pinjaman); ?>" id="jumlah_pinjaman_<?= $key + 1; ?>" name="jumlah_pinjaman[]" readonly>
                            </td>
                            <td>
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->jumlah_tagihan); ?>" id="jumlah_tagihan_<?= $key + 1; ?>" name="jumlah_tagihan[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" value="<?= $value->jumlah_bulan_angsuran; ?>" id="jumlah_bulan_angsuran_<?= $key + 1; ?>" name="jumlah_bulan_angsuran[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" value="<?= $value->angsuran_ke; ?>" id="angsuran_ke_<?= $key + 1; ?>" name="angsuran_ke[]" readonly>
                            </td>
                            <td>
                                <?php if ($value->status_ajuan > 0) { ?>
                                    &nbsp;
                                <?php } else { ?>
                                    <?php if ($value->edited > 0) { ?>
                                        <button type="button" class="btn btn-warning btn-rounded waves-effect waves-light btneditform"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light btneditform"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr data-id="<?= $value->id; ?>" data-fullname="<?= $value->nama; ?>">
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:actionEditTagihan('<?= $value->id; ?>', <?= $value->status_ajuan ?>);"><i class="fas fa-edit font-size-16 align-middle"></i> &nbsp;Edit</a>
                                        <a class="dropdown-item" href="javascript:actionHapus('<?= $value->id; ?>', <?= $value->status_ajuan ?>, '<?= $value->nama; ?>');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Hapus</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="form-control filter-pegawai" id="_filter_pegawai_<?= $key + 1; ?>" name="_filter_pegawai[]" data-id="<?= $key + 1; ?>" onchange="changePegawai(this)" aria-readonly="">
                                    <option value="<?= $value->id_pegawai; ?>" selected><?= $value->nama; ?></option>
                                </select>
                            </td>
                            <td>
                                <?= $value->nip; ?>
                                <input class="form-control" type="hidden" value="<?= $value->nip; ?>" id="nip_<?= $key + 1; ?>" name="nip[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" value="<?= $value->nama_instansi; ?>" id="instansi_<?= $key + 1; ?>" name="instansi[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" value="<?= $value->nama_kecamatan; ?>" id="kecamatan_<?= $key + 1; ?>" name="kecamatan[]" readonly>
                            </td>
                            <td>
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->besar_pinjaman); ?>" id="jumlah_pinjaman_<?= $key + 1; ?>" name="jumlah_pinjaman[]" readonly>
                            </td>
                            <td>
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->jumlah_tagihan); ?>" id="jumlah_tagihan_<?= $key + 1; ?>" name="jumlah_tagihan[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" value="<?= $value->jumlah_bulan_angsuran; ?>" id="jumlah_bulan_angsuran_<?= $key + 1; ?>" name="jumlah_bulan_angsuran[]" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" value="<?= $value->angsuran_ke; ?>" id="angsuran_ke_<?= $key + 1; ?>" name="angsuran_ke[]" readonly>
                            </td>
                            <td>
                                <?php if ($value->status_ajuan > 0) { ?>
                                    &nbsp;
                                <?php } else { ?>
                                    <?php if ($value->edited > 0) { ?>
                                        <button type="button" class="btn btn-warning btn-rounded waves-effect waves-light btneditform"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light btneditform"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
            <?php } ?>
        <?php } else { ?>
        <?php } ?>
    </tbody>
</table>
<?= form_close(); ?>
<script>
    function actionAjukanProsesTagihan(event, tahun, bulan, id) {
        Swal.fire({
            title: 'Apakah anda yakin ingin mengajukan proses tagihan data ini?',
            text: "Ajukan Proses tagihan : " + tahun + " - " + bulan,
            showCancelButton: true,
            icon: 'question',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ajukan Proses!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./ajukanprosestagihan",
                    type: 'POST',
                    data: {
                        id: id,
                        tahun: tahun,
                        bulan: bulan,
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('div.main-content').block({
                            message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                        });
                    },
                    complete: function() {
                        $('div.main-content').unblock();
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
                            Swal.fire(
                                'SELAMAT!',
                                resul.message,
                                'success'
                            ).then((valRes) => {
                                reloadPage(resul.url);
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

    // Function untuk mengubah data pegawai saat dipilih
    function changePegawai(event) {
        const getId = $(event).data('id');
        const selectedOption = $('#_filter_pegawai_' + getId).find(':selected');

        $('#formCheck_' + getId).val(selectedOption.data('custom-idpegawai'));
        $('#nip_' + getId).val(selectedOption.data('custom-nip'));
        $('#instansi_' + getId).val(selectedOption.data('custom-instansi'));
        $('#kecamatan_' + getId).val(selectedOption.data('custom-kecamatan'));
    }

    // Function untuk memformat angka menjadi Rupiah
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        // rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        // return 'Rp. ' + rupiah;
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
    }
    let rowBody = 1;


    $(document).ready(function() {
        <?php if (isset($datas)) { ?>
            <?php if (count($datas) > 0) { ?>
                rowBody = <?= count($datas) ?>;
            <?php } else { ?>
                rowBody = 1;
            <?php } ?>
        <?php } else { ?>
            rowBody = 1;
        <?php } ?>

        let tableDatatables = $("#data-datatables").DataTable({
            columnDefs: [{
                orderable: false,
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }],
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            // buttons: ["copy", "excel", "pdf"]
        });
        // }).buttons().container().appendTo("#data-datatables_wrapper .col-md-6:eq(0)");

        $('#_filter_pegawai_' + rowBody).select2({
            dropdownParent: ".data-contens",
            allowClear: true,
            ajax: {
                url: "./getPegawai",
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term,
                    };
                },
                processResults: function(data, params) {
                    if (data.status === 200) {
                        return {
                            results: data.data
                        };
                    } else {
                        return {
                            results: []
                        };
                    }
                },
                cache: true
            },
            placeholder: 'Cari Pegawai',
            minimumInputLength: 3,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        // Tambahkan event handler untuk tombol tambah baris
        $('.btnaddform').click(function(e) {
            e.preventDefault();
            rowBody++;

            // Tambahkan baris baru ke dalam tabel
            let newRow = `
                    <tr>
                        <td>
                            <input class="form-check-input" type="checkbox" id="formCheck_${rowBody}" name="check[]" value="">
                        </td>
                        <td>
                            <select class="form-control filter-pegawai" id="_filter_pegawai_${rowBody}" name="_filter_pegawai[]" data-id="${rowBody}" onchange="changePegawai(this)" required>
                                <option value="">&nbsp;</option>
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="nip_${rowBody}" name="nip[]" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="instansi_${rowBody}" name="instansi[]" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="kecamatan_${rowBody}" name="kecamatan[]" readonly>
                        </td>
                        <td>
                            <input class="form-control jumlah-pinjaman" type="text" value="" id="jumlah_pinjaman_${rowBody}" name="jumlah_pinjaman[]" required>
                        </td>
                        <td>
                            <input class="form-control  jumlah-pinjaman" type="text" value="" id="jumlah_tagihan_${rowBody}" name="jumlah_tagihan[]" required>
                        </td>
                        <td>
                            <input class="form-control" type="number" value="" id="jumlah_bulan_angsuran_${rowBody}" name="jumlah_bulan_angsuran[]" required>
                        </td>
                        <td>
                            <input class="form-control" type="number" value="" id="angsuran_ke_${rowBody}" name="angsuran_ke[]" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                        </td>
                    </tr>`;

            // Tambahkan baris baru ke tabel
            $('.formtambah').append(newRow);

            // Inisialisasi Select2 pada elemen terbaru
            $('#_filter_pegawai_' + rowBody).select2({
                dropdownParent: ".data-contens",
                allowClear: true,
                ajax: {
                    url: "./getPegawai",
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term,
                        };
                    },
                    processResults: function(data, params) {
                        if (data.status === 200) {
                            return {
                                results: data.data
                            };
                        } else {
                            return {
                                results: []
                            };
                        }
                    },
                    cache: true
                },
                placeholder: 'Cari Pegawai',
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        });

        // Event delegation untuk memformat input jumlah pinjaman
        $('.formtambah').on('keyup', '.jumlah-pinjaman', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('.formsimpanbanyak').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serializeArray();
            let processedData = {};
            for (let i = 0; i < formData.length; i++) {
                const field = formData[i].name;
                const value = formData[i].value;

                if (field.endsWith('[]')) { // Check if field name ends with [] for multiple values
                    processedData[field.slice(0, -2)] = processedData[field.slice(0, -2)] || []; // Initialize array if needed
                    processedData[field.slice(0, -2)].push(value);
                } else {
                    processedData[field] = value;
                }
            }

            const jsonData = JSON.stringify(processedData);
            // const jsonData = JSON.stringify(formData);
            $.ajax({
                url: './savetagihan',
                // url: $(this).attr('action'),
                type: 'POST',
                data: {
                    data: jsonData,
                    format: "json"
                },
                dataType: "json",
                beforeSend: function() {
                    $('.btnsimpanbanyak').attr('disable', 'disabled');
                    $('.btnsimpanbanyak').html('<i class="mdi mdi-reload mdi-spin"></i>');
                },
                complete: function() {
                    $('.btnsimpanbanyak').removeAttr('disable')
                    $('.btnsimpanbanyak').html('<i class="bx bx-save font-size-16 align-middle me-2"></i> SIMPAN');
                },
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire(
                            'SELAMAT!',
                            response.message + " " + response.data,
                            'success'
                        ).then((valRes) => {
                            reloadPage("<?= base_url('sigaji/bank/tagihan/antrian/datadetail?d=' . $tw_active) ?>");
                        })
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'warning'
                        );
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    Swal.fire(
                        'Failed!',
                        "gagal mengambil data (" + xhr.status.toString + ")",
                        'warning'
                    );
                }

            });
        })
    });

    $(document).on('click', '.btnhapusform', function(e) {
        e.preventDefault();
        const id = $(this).parents('tr').data('id');
        const nama = $(this).parents('tr').data('fullname');

        Swal.fire({
            title: 'Apakah anda yakin ingin menghapus tagihan ini?',
            text: "Hapus Tagihan Untuk Pegawai : " + nama,
            showCancelButton: true,
            icon: 'question',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Tagihan!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./hapusdatatagihan",
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('div.main-content').block({
                            message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                        });
                    },
                    complete: function() {
                        $('div.main-content').unblock();
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire(
                                'BERHASIL!',
                                response.message,
                                'success'
                            ).then((valRes) => {
                                $(this).parents('tr').remove();
                                reloadPage();
                            })
                        } else {
                            Swal.fire(
                                'Failed!',
                                "gagal mengambil data",
                                'warning'
                            );
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        Swal.fire(
                            'Failed!',
                            "gagal mengambil data (" + xhr.status.toString + ")",
                            'warning'
                        );
                    }

                });

            }
        })
    });

    $(document).on('click', '.btneditform', function(e) {
        e.preventDefault();
        const id = $(this).parents('tr').data('id');
        const nama = $(this).parents('tr').data('fullname');

        $.ajax({
            url: "./ambildataedit",
            type: 'POST',
            data: {
                id: id,
            },
            dataType: "json",
            beforeSend: function() {
                $('div.main-content').block({
                    message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                });
            },
            complete: function() {
                $('div.main-content').unblock();
            },
            success: function(response) {
                if (response.status == 200) {
                    $('#content-detailModalLabel').html('EDIT TAGIHAN ' + nama);
                    $('.contentBodyModal').html(response.data);
                    $('.content-detailModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    });
                    $('.content-detailModal').modal('show');
                } else {
                    Swal.fire(
                        'Failed!',
                        "gagal mengambil data",
                        'warning'
                    );
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                Swal.fire(
                    'Failed!',
                    "gagal mengambil data (" + xhr.status.toString + ")",
                    'warning'
                );
            }

        });
    });

    function actionEditTagihan(id, status) {
        if (status === 0 || status === 3) {
            $.ajax({
                url: "./editTagihan",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                },
                dataType: "json",
                beforeSend: function() {
                    $('div.main-content').block({
                        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    });
                },
                complete: function() {
                    $('div.main-content').unblock();
                },
                success: function(response) {
                    if (response.status == 200) {
                        $('#content-detailModalLabel').html('EDIT TAGIHAN BARU');
                        $('.contentBodyModal').html(response.data);
                        $('.content-detailModal').modal({
                            backdrop: 'static',
                            keyboard: false,
                        });
                        $('.content-detailModal').modal('show');
                    } else {
                        Swal.fire(
                            'Failed!',
                            response.message,
                            'warning'
                        );
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    Swal.fire(
                        'Failed!',
                        "gagal mengambil data (" + xhr.status.toString + ")",
                        'warning'
                    );
                }

            });
        } else {
            Swal.fire(
                'Peringatan!',
                "Pengajuan tidak dapat di ubah, karena masih dalam proses ajuan atau sudah di verifikasi oleh admin.",
                'warning'
            );
        }
    }

    function actionHapus(id, status, pegawai) {
        if (status === 0 || status === 3) {
            Swal.fire({
                title: 'Apakah anda yakin ingin menghapus data ini?',
                text: "Hapus Tagihan : " + pegawai,
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "./hapusTagihanPegawai",
                        type: 'POST',
                        data: {
                            id: id,
                            status: status,
                            pegawai: pegawai,
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

        } else {
            Swal.fire(
                'Peringatan!',
                "Pengajuan tidak dapat di hapus, karena masih dalam proses ajuan atau sudah di verifikasi oleh admin.",
                'warning'
            );
        }
    }

    $(document).on('click', '.btnaddtagihan', function(e) {
        e.preventDefault();

        $.ajax({
            url: "./tambahdatatagihanbaru",
            type: 'POST',
            data: {
                id: '<?= $tw_active ?>',
            },
            dataType: "json",
            beforeSend: function() {
                $('div.main-content').block({
                    message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                });
            },
            complete: function() {
                $('div.main-content').unblock();
            },
            success: function(response) {
                if (response.status == 200) {
                    $('#content-detailModalLabel').html('TAMBAH TAGIHAN BARU');
                    $('.contentBodyModal').html(response.data);
                    $('.content-detailModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    });
                    $('.content-detailModal').modal('show');
                } else {
                    Swal.fire(
                        'Failed!',
                        "gagal mengambil data",
                        'warning'
                    );
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                Swal.fire(
                    'Failed!',
                    "gagal mengambil data (" + xhr.status.toString + ")",
                    'warning'
                );
            }

        });
    });

    $(document).on('click', '.btnuploadtagihan', function(e) {
        e.preventDefault();

        $.ajax({
            url: "./upload",
            type: 'POST',
            data: {
                id: '<?= $tw_active ?>',
            },
            dataType: "json",
            beforeSend: function() {
                $('div.main-content').block({
                    message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                });
            },
            complete: function() {
                $('div.main-content').unblock();
            },
            success: function(response) {
                if (response.status == 200) {
                    $('#content-uploadModalLabel').html('UPLOAD TAGIHAN BARU');
                    $('.contentBodyUploadModal').html(response.data);
                    $('.content-uploadModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    });
                    $('.content-uploadModal').modal('show');
                } else {
                    Swal.fire(
                        'Failed!',
                        "gagal mengambil data",
                        'warning'
                    );
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                Swal.fire(
                    'Failed!',
                    "gagal mengambil data (" + xhr.status.toString + ")",
                    'warning'
                );
            }

        });
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.nama);
        $container.find(".select2-result-repository__description").text(repo.nip + " - " + repo.nama_instansi + " ( Kec. " + repo.nama_kecamatan + ")");

        return $container;
    }

    function formatRepoSelection(repo) {
        $(repo.element).attr('data-custom-idpegawai', repo.id);
        $(repo.element).attr('data-custom-nip', repo.nip);
        $(repo.element).attr('data-custom-instansi', repo.nama_instansi);
        $(repo.element).attr('data-custom-kecamatan', repo.nama_kecamatan);
        return repo.nama || repo.text;
    }

    function formatRepoEdit(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.nama);
        $container.find(".select2-result-repository__description").text(repo.nip + " - " + repo.nama_instansi + " ( Kec. " + repo.nama_kecamatan + ")");

        return $container;
    }

    function formatRepoSelectionEdit(repo) {
        $(repo.element).attr('data-custom-idpegawai-edit', repo.id);
        $(repo.element).attr('data-custom-nip-edit', repo.nip);
        $(repo.element).attr('data-custom-instansi-edit', repo.nama_instansi);
        $(repo.element).attr('data-custom-kecamatan-edit', repo.nama_kecamatan);
        return repo.nama || repo.text;
    }
</script>

<!-- <script>
    function changePegawai(event) {
        const getId = $(event).data('id');
        const getNip = $('#_filter_pegawai_' + getId).find(':selected').data('custom-nip');
        const getInstansi = $('#_filter_pegawai_' + getId).find(':selected').data('custom-instansi');
        const getKecamatan = $('#_filter_pegawai_' + getId).find(':selected').data('custom-kecamatan');

        $('#nip_' + getId).val(getNip);
        $('#instansi_' + getId).val(getInstansi);
        $('#kecamatan_' + getId).val(getKecamatan);
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    let rowBody = 1;

    $(document).ready(function(e) {
                $('.btnaddform').click(function(e) {
                            rowBody++;
                            e.preventDefault();
                            $('.formtambah').append(`
            <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" id="formCheck1">
                    </td>
                    <td>
                        <select class="form-control filter-pegawai" id="_filter_pegawai_` + rowBody + `" name="_filter_pegawai[]" data-id="` + rowBody + `" onchange="changePegawai(this)" required>
                            <option value="">&nbsp;</option>
                        </select>
                        <script>
                            $('#_filter_pegawai_` + rowBody + `').select2({
                                dropdownParent: ".data-contens",
                                allowClear: true,
                                ajax: {
                                    url: "./getPegawai",
                                    type: 'POST',
                                    dataType: 'json',
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            keyword: params.term,
                                        };
                                    },
                                    processResults: function(data, params) {
                                        if (data.status === 200) {
                                            return {
                                                results: data.data
                                            };
                                        } else {
                                            return {
                                                results: []
                                            };
                                        }
                                    },
                                    cache: true
                                },
                                placeholder: 'Cari Pegawai',
                                minimumInputLength: 3,
                                templateResult: formatRepo,
                                templateSelection: formatRepoSelection
                            });
</script>
</td>
<td>
    <input class="form-control" type="text" value="" id="nip_` + rowBody + `" name="nip[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" id="instansi_` + rowBody + `" name="instansi[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" id="kecamatan_` + rowBody + `" name="kecamatan[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" onchange="aksiChangeInput(this)" id="jumlah_pinjaman_` + rowBody + `" name="jumlah_pinjaman[]" required />
    <script>
        let jumlah_pinjaman_` + rowBody + ` = document.getElementById('jumlah_pinjaman_` + rowBody + `');
        jumlah_pinjaman_` + rowBody + `.addEventListener('keyup', function(e) {
            jumlah_pinjaman_` + rowBody + `.value = formatRupiah(this.value);
        });
    </script>
</td>
<td>
    <input class="form-control" type="text" value="" id="jumlah_tagihan_` + rowBody + `" name="jumlah_tagihan[]" required />
</td>
<td>
    <input class="form-control" type="text" value="" id="jumlah_bulan_angsuran_` + rowBody + `" name="jumlah_bulan_angsuran[]" required />
</td>
<td>
    <input class="form-control" type="text" value="" id="angsuran_ke_` + rowBody + `" name="angsuran_ke[]" required />
</td>
<td>
    <button type="button" onclick="aksiTambah(this)" class="btn btn-primary btn-rounded waves-effect waves-light">+</button>
</td>
</tr>
`);
});
});
</script> -->