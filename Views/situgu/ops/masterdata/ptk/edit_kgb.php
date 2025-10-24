<?php if (isset($data)) { ?>
    <form id="formEditKgbModalData" action="./editKgbSave" method="post">
        <input type="hidden" id="_id" name="_id" value="<?= $data->id ?>" />
        <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
            <!-- <div class="mb-3">
                <label for="_pangkat" class="col-form-label">Pilih Pangkat Terakhir:</label>
                <select class="select2 form-control select2" id="_pangkat" name="_pangkat" style="width: 100%" data-placeholder="Pilih Pangkat ...">
                    <option value="" selected>--Pilih Pangkat--</option>
                    <?php if (isset($data->pangkats)) { ?>
                        <?php if (count($data->pangkats) > 0) { ?>
                            <?php foreach ($data->pangkats as $key => $value) { ?>
                                <option value="<?= $value->pangkat ?>" <?= $data->pangkat_golongan == $value->pangkat ? 'selected' : '' ?>><?= $value->pangkat ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
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
            </div> -->
            <div class="mb-3">
                <label for="_kgb" class="col-form-label">Pilih Pangkat KGB Terakhir:</label>
                <select class="select2 form-control select2" id="_kgb" name="_kgb" style="width: 100%" data-placeholder="Pilih Pangkat KGB ...">
                    <option value="" selected>--Pilih Pangkat KGB--</option>
                    <?php if (isset($data->pangkats)) { ?>
                        <?php if (count($data->pangkats) > 0) { ?>
                            <?php foreach ($data->pangkats as $key => $value) { ?>
                                <option value="<?= $value->pangkat ?>" <?= $data->pangkat_golongan_kgb == $value->pangkat ? 'selected' : '' ?>><?= $value->pangkat ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
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
            <button type="submit" class="btn btn-primary waves-effect waves-light">Ajukan Perubahan</button>
        </div>
    </form>

    <script>
        $("#formEditKgbModalData").on("submit", function(e) {
            e.preventDefault();
            const id = document.getElementsByName('_id')[0].value;
            // const pangkat = document.getElementsByName('_pangkat')[0].value;
            // const no_sk_pangkat = document.getElementsByName('_no_sk_pangkat')[0].value;
            // const tgl_pangkat = document.getElementsByName('_tgl_pangkat')[0].value;
            // const tmt_pangkat = document.getElementsByName('_tmt_pangkat')[0].value;
            // const mkt_pangkat = document.getElementsByName('_mkt_pangkat')[0].value;
            // const mkb_pangkat = document.getElementsByName('_mkb_pangkat')[0].value;
            const kgb = document.getElementsByName('_kgb')[0].value;
            const no_sk_kgb = document.getElementsByName('_no_sk_kgb')[0].value;
            const tgl_kgb = document.getElementsByName('_tgl_kgb')[0].value;
            const tmt_kgb = document.getElementsByName('_tmt_kgb')[0].value;
            const mkt_kgb = document.getElementsByName('_mkt_kgb')[0].value;
            const mkb_kgb = document.getElementsByName('_mkb_kgb')[0].value;

            // if (pangkat === "") {
            //     $("select#_pangkat").css("color", "#dc3545");
            //     $("select#_pangkat").css("border-color", "#dc3545");
            //     $('._pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Pilih Pangkat.</li></ul>');
            //     return false;
            // }
            // if (no_sk_pangkat === "") {
            //     $("input#_no_sk_pangkat").css("color", "#dc3545");
            //     $("input#_no_sk_pangkat").css("border-color", "#dc3545");
            //     $('._no_sk_pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No SK Pangkat tidak boleh kosong.</li></ul>');
            //     return false;
            // }
            // if (tgl_pangkat === "") {
            //     $("input#_tgl_pangkat").css("color", "#dc3545");
            //     $("input#_tgl_pangkat").css("border-color", "#dc3545");
            //     $('._tgl_pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Tanggal SK Pangkat tidak boleh kosong.</li></ul>');
            //     return false;
            // }
            // if (tmt_pangkat === "") {
            //     $("input#_tmt_pangkat").css("color", "#dc3545");
            //     $("input#_tmt_pangkat").css("border-color", "#dc3545");
            //     $('._tmt_pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">TMT SK Pangkat tidak boleh kosong.</li></ul>');
            //     return false;
            // }
            // if (mkt_pangkat === "") {
            //     $("input#_mkt_pangkat").css("color", "#dc3545");
            //     $("input#_mkt_pangkat").css("border-color", "#dc3545");
            //     $('._mkt_pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Masa Kerja Tahun Pangkat tidak boleh kosong.</li></ul>');
            //     return false;
            // }
            // if (mkb_pangkat === "") {
            //     $("input#_mkb_pangkat").css("color", "#dc3545");
            //     $("input#_mkb_pangkat").css("border-color", "#dc3545");
            //     $('._mkb_pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Masa Kerja Bulan Pangkat tidak boleh kosong.</li></ul>');
            //     return false;
            // }
            if (kgb === "") {
                $("select#_kgb").css("color", "#dc3545");
                $("select#_kgb").css("border-color", "#dc3545");
                $('._kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Pilih Pangkat KGB.</li></ul>');
                return false;
            }
            if (no_sk_kgb === "") {
                $("input#_no_sk_kgb").css("color", "#dc3545");
                $("input#_no_sk_kgb").css("border-color", "#dc3545");
                $('._no_sk_kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No SK KGB tidak boleh kosong.</li></ul>');
                return false;
            }
            if (tgl_kgb === "") {
                $("input#_tgl_kgb").css("color", "#dc3545");
                $("input#_tgl_kgb").css("border-color", "#dc3545");
                $('._tgl_kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Tanggal SK KGB tidak boleh kosong.</li></ul>');
                return false;
            }
            if (tmt_kgb === "") {
                $("input#_tmt_kgb").css("color", "#dc3545");
                $("input#_tmt_kgb").css("border-color", "#dc3545");
                $('._tmt_kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">TMT SK KGB tidak boleh kosong.</li></ul>');
                return false;
            }
            if (mkt_kgb === "") {
                $("input#_mkt_kgb").css("color", "#dc3545");
                $("input#_mkt_kgb").css("border-color", "#dc3545");
                $('._mkt_kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Masa Kerja Tahun KGB tidak boleh kosong.</li></ul>');
                return false;
            }
            if (mkb_kgb === "") {
                $("input#_mkb_kgb").css("color", "#dc3545");
                $("input#_mkb_kgb").css("border-color", "#dc3545");
                $('._mkb_kgb').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Masa Kerja Bulan KGB tidak boleh kosong.</li></ul>');
                return false;
            }

            Swal.fire({
                title: 'Apakah anda yakin ingin mengupdate data ini?',
                text: "Update Data Pangkat & KGB PTK: <?= $data->nama ?>",
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "./editKgbSave",
                        type: 'POST',
                        data: {
                            id: id,
                            // pangkat: pangkat,
                            // no_sk_pangkat: no_sk_pangkat,
                            // tgl_pangkat: tgl_pangkat,
                            // tmt_pangkat: tmt_pangkat,
                            // mkt_pangkat: mkt_pangkat,
                            // mkb_pangkat: mkb_pangkat,
                            kgb: kgb,
                            no_sk_kgb: no_sk_kgb,
                            tgl_kgb: tgl_kgb,
                            tmt_kgb: tmt_kgb,
                            mkt_kgb: mkt_kgb,
                            mkb_kgb: mkb_kgb,
                        },
                        dataType: 'JSON',
                        beforeSend: function() {
                            $('div.modal-content-loading-tolak').block({
                                message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                            });
                        },
                        success: function(resul) {
                            $('div.modal-content-loading-tolak').unblock();

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
                            $('div.modal-content-loading-tolak').unblock();
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