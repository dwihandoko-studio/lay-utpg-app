<form id="formEditModalData" class="formEditModalData" action="./editSaveDataTagihanNew" method="post">
    <input type="hidden" id="_id_tagihan_edit" name="_id_tagihan_edit" value="<?= $data->id ?>" />
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="mb-3">
            <label for="_fullname_edit" class="col-form-label">NAMA LENGKAP</label>
            <select style="width: 100%" class="form-control filter-fullname-edit" id="_fullname_edit" name="_fullname_edit" data-id="1" onchange="changePegawaiEditNew(this)" required>
                <option value="">&nbsp;</option>
                <option value="<?= $data->id_pegawai; ?>" selected><?= $data->nama; ?></option>
            </select>
            <script>
                $('#_fullname_edit').select2({
                    dropdownParent: ".formEditModalData",
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
                    templateResult: formatRepoEdit,
                    templateSelection: formatRepoSelectionEdit
                });
            </script>
            <div class="help-block _fullname_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_nip_edit" class="form-label">NIP</label>
            <input type="text" class="form-control nip_add" id="_nip_edit" name="_nip_edit" value="<?= $data->nip ?>" placeholder="NIP..." onfocusin="inputFocus(this);" readonly>
            <div class="help-block _nip_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_nama_instansi_edit" class="form-label">Instansi</label>
            <input type="text" class="form-control nama_instansi_edit" id="_nama_instansi_edit" name="_nama_instansi_edit" value="<?= $data->instansi ?>" placeholder="Nama instansi..." onfocusin="inputFocus(this);" readonly>
            <div class="help-block _nama_instansi_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_kecamatan_edit" class="form-label">Kecamatan</label>
            <input type="text" class="form-control kecamatan_edit" id="_kecamatan_edit" name="_kecamatan_edit" value="<?= $data->kecamatan ?>" placeholder="Kecamatan..." onfocusin="inputFocus(this);" readonly>
            <div class="help-block _kecamatan_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_jumlah_pinjaman_edit" class="form-label">Jumlah Pinjaman</label>
            <input type="text" class="form-control jumlah-pinjaman jumlah_pinjaman_edit" id="_jumlah_pinjaman_edit" name="_jumlah_pinjaman_edit" value="<?= $data->besar_pinjaman ?>" required>
            <div class="help-block _jumlah_pinjaman_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_jumlah_tagihan_edit" class="form-label">Jumlah Tagihan</label>
            <input type="text" class="form-control jumlah-pinjaman jumlah_tagihan_edit" id="_jumlah_tagihan_edit" name="_jumlah_tagihan_edit" value="<?= $data->jumlah_tagihan ?>" required>
            <div class="help-block _jumlah_tagihan_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_jumlah_bulan_angsuran_edit" class="form-label">Jumlah Bulan Angsuran</label>
            <input type="number" class="form-control jumlah_bulan_angsuran_edit" id="_jumlah_bulan_angsuran_edit" name="_jumlah_bulan_angsuran_edit" value="<?= $data->jumlah_bulan_angsuran ?>" required>
            <div class="help-block _jumlah_bulan_angsuran_edit"></div>
        </div>
        <div class="mb-3">
            <label for="_angsuran_ke_edit" class="form-label">Angsuran Ke</label>
            <input type="number" class="form-control angsuran_ke_edit" id="_angsuran_ke_edit" name="_angsuran_ke_edit" value="<?= $data->ansuran_ke ?>" required>
            <div class="help-block _angsuran_ke_edit"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary waves-effect waves-light">UPDATE</button>
    </div>
</form>

<script>
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

    function changePegawaiEditNew(event) {
        const selectedOption = $('#_fullname_edit').find(':selected');

        $('#_nip_edit').val(selectedOption.data('custom-nip-edit'));
        $('#_nama_instansi_edit').val(selectedOption.data('custom-instansi-edit'));
        $('#_kecamatan_edit').val(selectedOption.data('custom-kecamatan-edit'));
    }

    $('.formEditModalData').on('keyup', '.jumlah-pinjaman', function() {
        $(this).val(formatRupiah($(this).val()));
    });

    $("#formEditModalData").on("submit", function(e) {
        e.preventDefault();
        const id_tagihan = document.getElementsByName('_id_tagihan_edit')[0].value;
        const fullname = document.getElementsByName('_fullname_edit')[0].value;
        const nip = document.getElementsByName('_nip_edit')[0].value;
        const nama_instansi = document.getElementsByName('_nama_instansi_edit')[0].value;
        const kecamatan = document.getElementsByName('_kecamatan_edit')[0].value;
        const jumlah_pinjaman = document.getElementsByName('_jumlah_pinjaman_edit')[0].value;
        const jumlah_tagihan = document.getElementsByName('_jumlah_tagihan_edit')[0].value;
        const jumlah_bulan_angsuran = document.getElementsByName('_jumlah_bulan_angsuran_edit')[0].value;
        const angsuran_ke = document.getElementsByName('_angsuran_ke_edit')[0].value;

        Swal.fire({
            title: 'Apakah anda yakin ingin menyimpan data ini?',
            text: "Simpan Data Baru Tagihan " + nip,
            showCancelButton: true,
            icon: 'question',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, SIMPAN!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "./editSaveDataTagihanNew",
                    type: 'POST',
                    data: {
                        id: id_tagihan,
                        fullname: fullname,
                        nip: nip,
                        nama_instansi: nama_instansi,
                        kecamatan: kecamatan,
                        jumlah_pinjaman: jumlah_pinjaman,
                        jumlah_tagihan: jumlah_tagihan,
                        jumlah_bulan_angsuran: jumlah_bulan_angsuran,
                        angsuran_ke: angsuran_ke,
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('div.modal-content-loading').block({
                            message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                        });
                    },
                    complete: function() {
                        $('div.modal-content-loading').unblock();
                    },
                    success: function(resul) {

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
                                'BERHASIL!',
                                resul.message,
                                'success'
                            ).then((valRes) => {
                                reloadPage();
                            })
                        }
                    },
                    error: function() {
                        $('div.modal-content-loading').unblock();
                        Swal.fire(
                            'PERINGATAN!',
                            "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                            'warning'
                        );
                    }
                });
            }
        })
    });
</script>