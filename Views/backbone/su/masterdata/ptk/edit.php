<?php if (isset($data)) { ?>
    <form id="formEditModalData" action="./editSave" method="post">
        <input type="hidden" id="_id" name="_id" value="<?= $data->id ?>" />
        <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
            <div class="mb-3">
                <label for="_pendidikan" class="col-form-label">Pilih Pendidikan:</label>
                <select class="select2 form-control select2" id="_pendidikan" name="_pendidikan" style="width: 100%" data-placeholder="Pilih Pendidikan ...">
                    <option value="" selected>--Pilih Pendidikan--</option>
                    <option value="SMA" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'SMA' ? 'selected' : '') ?>>SMA</option>
                    <option value="D1" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'D1' ? 'selected' : '') ?>>D1</option>
                    <option value="D2" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'D2' ? 'selected' : '') ?>>D2</option>
                    <option value="D3" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'D3' ? 'selected' : '') ?>>D3</option>
                    <option value="D4" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'D4' ? 'selected' : '') ?>>D4</option>
                    <option value="S1" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'S1' ? 'selected' : '') ?>>S1</option>
                    <option value="S2" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'S2' ? 'selected' : '') ?>>S2</option>
                    <option value="S3" <?= $data->pendidikan == NULL || $data->pendidikan == "" ? '' : ($data->pendidikan == 'S3' ? 'selected' : '') ?>>S3</option>
                </select>
                <div class="help-block _pendidikan"></div>
            </div>
            <div class="mb-3">
                <label for="_nrg" class="form-label">NRG</label>
                <input type="text" class="form-control nrg" value="<?= $data->nrg ?>" id="_nrg" name="_nrg" placeholder="NRG..." onfocusin="inputFocus(this);">
                <div class="help-block _nrg"></div>
            </div>
            <div class="mb-3">
                <label for="_no_peserta" class="form-label">No Peserta</label>
                <input type="text" class="form-control no_peserta" value="<?= $data->no_peserta ?>" id="_no_peserta" name="_no_peserta" placeholder="No Peserta..." onfocusin="inputFocus(this);">
                <div class="help-block _no_peserta"></div>
            </div>
            <div class="mb-3">
                <label for="_bidang_studi_sertifikasi" class="form-label">Bidang Studi Sertifikasi</label>
                <input type="text" class="form-control bidang-studi-sertifikasi" value="<?= $data->bidang_studi_sertifikasi ?>" id="_bidang_studi_sertifikasi" name="_bidang_studi_sertifikasi" placeholder="Bidang studi sertifikasi..." onfocusin="inputFocus(this);">
                <div class="help-block _bidang_studi_sertifikasi"></div>
            </div>
            <div class="mb-3">
                <label for="_pangkat" class="form-label">Pangkat Terakhir</label>
                <input type="text" class="form-control pangkat" value="<?= $data->pangkat_golongan ?>" id="_pangkat" name="_pangkat" placeholder="Pangkat terakhir (example: IV/A)..." onfocusin="inputFocus(this);">
                <div class="help-block _pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_no_sk_pangkat" class="form-label">No SK Pangkat Terakhir</label>
                <input type="text" class="form-control no-sk-pangkat" value="<?= $data->nomor_sk_pangkat ?>" id="_no_sk_pangkat" name="_no_sk_pangkat" placeholder="No SK Pangkat terakhir..." onfocusin="inputFocus(this);">
                <div class="help-block _no_sk_pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_tgl_pangkat" class="form-label">Tanggal Pangkat Terakhir</label>
                <input type="date" class="form-control tgl-pangkat" value="<?= $data->tgl_sk_pangkat ?>" id="_tgl_pangkat" name="_tgl_pangkat" onfocusin="inputFocus(this);">
                <div class="help-block _tgl_pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_tmt_pangkat" class="form-label">TMT Pangkat Terakhir</label>
                <input type="date" class="form-control tmt-pangkat" value="<?= $data->tmt_pangkat ?>" id="_tmt_pangkat" name="_tmt_pangkat" onfocusin="inputFocus(this);">
                <div class="help-block _tmt_pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_mkt_pangkat" class="form-label">Masa Kerja Tahun Pangkat Terakhir</label>
                <input type="number" class="form-control mkt-pangkat" value="<?= $data->masa_kerja_tahun ?>" id="_mkt_pangkat" name="_mkt_pangkat" onfocusin="inputFocus(this);">
                <div class="help-block _mkt_pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_mkb_pangkat" class="form-label">Masa Kerja Bulan Pangkat Terakhir</label>
                <input type="number" class="form-control mkt-pangkat" value="<?= $data->masa_kerja_bulan ?>" id="_mkb_pangkat" name="_mkb_pangkat" onfocusin="inputFocus(this);">
                <div class="help-block _mkb_pangkat"></div>
            </div>
            <div class="mb-3">
                <label for="_kgb" class="form-label">Pangkat KGB Terakhir</label>
                <input type="text" class="form-control kgb" value="<?= $data->pangkat_golongan_kgb ?>" id="_kgb" name="_kgb" placeholder="Pangkat KGB terakhir (example: IV/A)..." onfocusin="inputFocus(this);">
                <div class="help-block _kgb"></div>
            </div>
            <div class="mb-3">
                <label for="_no_sk_kgb" class="form-label">No SK KGB Terakhir</label>
                <input type="text" class="form-control no-sk-kgb" value="<?= $data->sk_kgb ?>" id="_no_sk_kgb" name="_no_sk_kgb" placeholder="No SK Pangkat terakhir..." onfocusin="inputFocus(this);">
                <div class="help-block _no_sk_kgb"></div>
            </div>
            <div class="mb-3">
                <label for="_tgl_kgb" class="form-label">Tanggal KGB Terakhir</label>
                <input type="date" class="form-control tgl-kgb" value="<?= $data->tgl_sk_kgb ?>" id="_tgl_kgb" name="_tgl_kgb" onfocusin="inputFocus(this);">
                <div class="help-block _tgl_kgb"></div>
            </div>
            <div class="mb-3">
                <label for="_tmt_kgb" class="form-label">TMT KGB Terakhir</label>
                <input type="date" class="form-control tmt-kgb" value="<?= $data->tmt_sk_kgb ?>" id="_tmt_kgb" name="_tmt_kgb" onfocusin="inputFocus(this);">
                <div class="help-block _tmt_kgb"></div>
            </div>
            <div class="mb-3">
                <label for="_mkt_kgb" class="form-label">Masa Kerja Tahun KGB Terakhir</label>
                <input type="number" class="form-control mkt-kgb" value="<?= $data->masa_kerja_tahun_kgb ?>" id="_mkt_kgb" name="_mkt_kgb" onfocusin="inputFocus(this);">
                <div class="help-block _mkt_kgb"></div>
            </div>
            <div class="mb-3">
                <label for="_mkb_kgb" class="form-label">Masa Kerja Bulan KGB Terakhir</label>
                <input type="number" class="form-control mkt-kgb" value="<?= $data->masa_kerja_bulan_kgb ?>" id="_mkb_kgb" name="_mkb_kgb" onfocusin="inputFocus(this);">
                <div class="help-block _mkb_kgb"></div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
        </div>
    </form>

    <script>
        $("#formEditModalData").on("submit", function(e) {
            e.preventDefault();
            const id = document.getElementsByName('_id')[0].value;
            const nrg = document.getElementsByName('_nrg')[0].value;
            const pendidikan = document.getElementsByName('_pendidikan')[0].value;
            const no_peserta = document.getElementsByName('_no_peserta')[0].value;
            const bidang_studi_sertifikasi = document.getElementsByName('_bidang_studi_sertifikasi')[0].value;
            const pangkat = document.getElementsByName('_pangkat')[0].value;
            const no_sk_pangkat = document.getElementsByName('_no_sk_pangkat')[0].value;
            const tgl_pangkat = document.getElementsByName('_tgl_pangkat')[0].value;
            const tmt_pangkat = document.getElementsByName('_tmt_pangkat')[0].value;
            const mkt_pangkat = document.getElementsByName('_mkt_pangkat')[0].value;
            const mkb_pangkat = document.getElementsByName('_mkb_pangkat')[0].value;
            const kgb = document.getElementsByName('_kgb')[0].value;
            const no_sk_kgb = document.getElementsByName('_no_sk_kgb')[0].value;
            const tgl_kgb = document.getElementsByName('_tgl_kgb')[0].value;
            const tmt_kgb = document.getElementsByName('_tmt_kgb')[0].value;
            const mkt_kgb = document.getElementsByName('_mkt_kgb')[0].value;
            const mkb_kgb = document.getElementsByName('_mkb_kgb')[0].value;

            // if (pangkat === "") {
            //     $("input#_nrg").css("color", "#dc3545");
            //     $("input#_nrg").css("border-color", "#dc3545");
            //     $('._nrg').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">NRG tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
            //     return false;
            // }
            // if (no_peserta === "") {
            //     $("input#_no_peserta").css("color", "#dc3545");
            //     $("input#_no_peserta").css("border-color", "#dc3545");
            //     $('._no_peserta').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No Peserta tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
            //     return false;
            // }
            // if (npwp === "") {
            //     $("input#_npwp").css("color", "#dc3545");
            //     $("input#_npwp").css("border-color", "#dc3545");
            //     $('._npwp').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">NPWP tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
            //     return false;
            // }
            // if (no_rekening === "") {
            //     $("input#_no_rekening").css("color", "#dc3545");
            //     $("input#_no_rekening").css("border-color", "#dc3545");
            //     $('._no_rekening').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No Rekening tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
            //     return false;
            // }
            // if (cabang_bank === "") {
            //     $("input#_cabang_bank").css("color", "#dc3545");
            //     $("input#_cabang_bank").css("border-color", "#dc3545");
            //     $('._cabang_bank').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Cabang Bank tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
            //     return false;
            // }

            Swal.fire({
                title: 'Apakah anda yakin ingin mengupdate data ini?',
                text: "Update Data PTK: <?= $data->nama ?>",
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "./editSave",
                        type: 'POST',
                        data: {
                            id: id,
                            nrg: nrg,
                            pendidikan: pendidikan,
                            no_peserta: no_peserta,
                            bidang_studi_sertifikasi: bidang_studi_sertifikasi,
                            pangkat: pangkat,
                            no_sk_pangkat: no_sk_pangkat,
                            tgl_pangkat: tgl_pangkat,
                            tmt_pangkat: tmt_pangkat,
                            mkt_pangkat: mkt_pangkat,
                            mkb_pangkat: mkb_pangkat,
                            kgb: kgb,
                            no_sk_kgb: no_sk_kgb,
                            tgl_kgb: tgl_kgb,
                            tmt_kgb: tmt_kgb,
                            mkt_kgb: mkt_kgb,
                            mkb_kgb: mkb_kgb,
                        },
                        dataType: 'JSON',
                        beforeSend: function() {
                            $('div.modal-content-loading-edit').block({
                                message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                            });
                        },
                        success: function(resul) {
                            $('div.modal-content-loading-edit').unblock();

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
                            $('div.modal-content-loading-edit').unblock();
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

<?php } ?>