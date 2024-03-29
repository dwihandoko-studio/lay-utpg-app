<?php if (isset($data)) { ?>
    <form id="formEditModalData" action="./editSave" method="post">
        <input type="hidden" id="_id" name="_id" value="<?= $data->id_ptk ?>" />
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
                <label for="_npwp" class="form-label">NPWP</label>
                <input type="text" class="form-control npwp" value="<?= $data->npwp ?>" id="_npwp" name="_npwp" placeholder="NPWP..." onfocusin="inputFocus(this);">
                <div class="help-block _npwp"></div>
            </div>
            <div class="mb-3">
                <label for="_no_rekening" class="form-label">No Rekening Bank</label>
                <input type="text" class="form-control no_rekening" id="_no_rekening" name="_no_rekening" value="<?= $data->no_rekening ?>" placeholder="No Rekening..." onfocusin="inputFocus(this);">
                <div class="help-block _no_rekening"></div>
            </div>
            <div class="mb-3">
                <label for="_cabang_bank" class="form-label">Cabang Bank</label>
                <input type="text" class="form-control cabang_bank" id="_cabang_bank" name="_cabang_bank" value="<?= $data->cabang_bank ?>" placeholder="Cabang Bank..." onfocusin="inputFocus(this);">
                <div class="help-block _cabang_bank"></div>
            </div>
            <div class="mb-3">
                <label for="_bidang_studi_sertifikasi" class="col-form-label">Bidang Studi Sertifikasi:</label>
                <select class="select2 form-control" id="_bidang_studi_sertifikasi" name="_bidang_studi_sertifikasi" style="width: 100%" data-placeholder="Bidang Studi Sertifikasi...">
                    <option value="">&nbsp;</option>
                    <?php if (isset($ref_serti)) {
                        if (count($ref_serti) > 0) {
                            foreach ($ref_serti as $key => $value) { ?>
                                <option value="<?= $value ?>" <?= $value == $data->bidang_studi_sertifikasi ? 'selected' : '' ?>><?= $value ?></option>
                    <?php }
                        }
                    } ?>
                </select>
                <p class="font-size-11">Keterangan : <code>Wajib diisi jika sudah mempunyai sertifikat pendidikan. Jika belum mempunyai sertifikat pendidik, silahkan kosongkan bagian ini.</code></p>
                <div class="help-block _bidang_studi_sertifikasi"></div>
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
            const no_peserta = document.getElementsByName('_no_peserta')[0].value;
            const npwp = document.getElementsByName('_npwp')[0].value;
            const no_rekening = document.getElementsByName('_no_rekening')[0].value;
            const cabang_bank = document.getElementsByName('_cabang_bank')[0].value;
            const bidang_studi_sertifikasi = document.getElementsByName('_bidang_studi_sertifikasi')[0].value;
            const pendidikan = document.getElementsByName('_pendidikan')[0].value;

            if (pendidikan === "") {
                $("select#_pendidikan").css("color", "#dc3545");
                $("select#_pendidikan").css("border-color", "#dc3545");
                $('._pendidikan').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Silahkan pilih jenjang pendidikan.</li></ul>');
                return false;
            }
            if (nrg === "") {
                $("input#_nrg").css("color", "#dc3545");
                $("input#_nrg").css("border-color", "#dc3545");
                $('._nrg').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">NRG tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
                return false;
            }
            if (no_peserta === "") {
                $("input#_no_peserta").css("color", "#dc3545");
                $("input#_no_peserta").css("border-color", "#dc3545");
                $('._no_peserta').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No Peserta tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
                return false;
            }
            if (npwp === "") {
                $("input#_npwp").css("color", "#dc3545");
                $("input#_npwp").css("border-color", "#dc3545");
                $('._npwp').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">NPWP tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
                return false;
            }
            if (no_rekening === "") {
                $("input#_no_rekening").css("color", "#dc3545");
                $("input#_no_rekening").css("border-color", "#dc3545");
                $('._no_rekening').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No Rekening tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
                return false;
            }
            if (cabang_bank === "") {
                $("input#_cabang_bank").css("color", "#dc3545");
                $("input#_cabang_bank").css("border-color", "#dc3545");
                $('._cabang_bank').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Cabang Bank tidak boleh kosong. Silahkan isi dengan tanda (-) jika tidak ada.</li></ul>');
                return false;
            }

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
                            no_peserta: no_peserta,
                            npwp: npwp,
                            no_rekening: no_rekening,
                            cabang_bank: cabang_bank,
                            bidang_studi_sertifikasi: bidang_studi_sertifikasi,
                            pendidikan: pendidikan,
                        },
                        dataType: 'JSON',
                        beforeSend: function() {
                            $('div.modal-content-loading').block({
                                message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                            });
                        },
                        success: function(resul) {
                            $('div.modal-content-loading').unblock();

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

<?php } ?>