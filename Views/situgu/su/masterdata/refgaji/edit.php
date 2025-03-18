<?php if (isset($data)) { ?>
    <form id="formEditModalData" action="./editSave" method="post" enctype="multipart/form-data">
        <input type="hidden" value="<?= $data->id ?>" id="_id" name="_id" />
        <div class="modal-body loading-get-data">
            <div class="row">
                <div class="col-lg-6">
                    <label for="_pangkat" class="col-form-label">Pangkat:</label>
                    <input type="text" class="form-control pangkat" value="<?= $data->pangkat ?>" id="_pangkat" name="_pangkat" placeholder="Pangkat..." onfocusin="inputFocus(this);" required />
                    <div class="help-block _pangkat"></div>
                </div>
                <div class="col-lg-6">
                    <label for="_masa_kerja" class="col-form-label">Masa Kerja:</label>
                    <input type="number" class="form-control masa_kerja" value="<?= $data->masa_kerja ?>" id="_masa_kerja" name="_masa_kerja" onfocusin="inputFocus(this);" required />
                    <div class="help-block _masa_kerja"></div>
                </div>
                <div class="col-lg-6">
                    <label for="_gaji_pokok" class="col-form-label">Gaji Pokok:</label>
                    <input type="number" class="form-control gaji_pokok" value="<?= $data->gaji_pokok ?>" id="_gaji_pokok" name="_gaji_pokok" onfocusin="inputFocus(this);" required />
                    <div class="help-block _gaji_pokok"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="col-8">
                <div>
                    <progress id="progressBar" value="0" max="100" style="width:100%; display: none;"></progress>
                </div>
                <div>
                    <h3 id="status" style="font-size: 15px; margin: 8px auto;"></h3>
                </div>
                <div>
                    <p id="loaded_n_total" style="margin-bottom: 0px;"></p>
                </div>
            </div>
            <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan</button>
        </div>
    </form>

    <script>
        $("#formEditModalData").on("submit", function(e) {
            e.preventDefault();
            const id = document.getElementsByName('_id')[0].value;
            const pangkat = document.getElementsByName('_pangkat')[0].value;
            const masa_kerja = document.getElementsByName('_masa_kerja')[0].value;
            const gaji_pokok = document.getElementsByName('_gaji_pokok')[0].value;

            if (pangkat === "") {
                $("input#_pangkat").css("color", "#dc3545");
                $("input#_pangkat").css("border-color", "#dc3545");
                $('._pangkat').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">Fullname tidak boleh kosong.</li></ul>');
                return false;
            }

            if (masa_kerja === "") {
                $("input#_masa_kerja").css("color", "#dc3545");
                $("input#_masa_kerja").css("border-color", "#dc3545");
                $('._masa_kerja').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">Email tidak boleh kosong.</li></ul>');
                return false;
            }

            if (gaji_pokok === "") {
                $("input#_gaji_pokok").css("color", "#dc3545");
                $("input#_gaji_pokok").css("border-color", "#dc3545");
                $('._gaji_pokok').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">No Handphone tidak boleh kosong.</li></ul>');
                return false;
            }

            const formUpload = new FormData();

            formUpload.append('id', id);
            formUpload.append('pangkat', pangkat);
            formUpload.append('masa_kerja', masa_kerja);
            formUpload.append('gaji_pokok', gaji_pokok);

            $.ajax({
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            ambilId("loaded_n_total").innerHTML = "Uploaded " + evt.loaded + " bytes of " + evt.total;
                            var percent = (evt.loaded / evt.total) * 100;
                            ambilId("progressBar").value = Math.round(percent);
                            // ambilId("status").innerHTML = Math.round(percent) + "% uploaded... please wait";
                        }
                    }, false);
                    return xhr;
                },
                url: "./editSave",
                type: 'POST',
                data: formUpload,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function() {
                    ambilId("progressBar").style.display = "block";
                    // ambilId("status").innerHTML = "Mulai mengupload . . .";
                    ambilId("status").style.color = "blue";
                    ambilId("progressBar").value = 0;
                    ambilId("loaded_n_total").innerHTML = "";
                    $('div.modal-content-loading').block({
                        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    });
                },
                success: function(resul) {
                    $('div.modal-content-loading').unblock();

                    if (resul.status !== 200) {
                        ambilId("status").innerHTML = "";
                        ambilId("status").style.color = "red";
                        ambilId("progressBar").value = 0;
                        ambilId("loaded_n_total").innerHTML = "";
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
                        ambilId("status").innerHTML = "";
                        ambilId("status").style.color = "green";
                        ambilId("progressBar").value = 100;
                        Swal.fire(
                            'SELAMAT!',
                            resul.message,
                            'success'
                        ).then((valRes) => {
                            reloadPage();
                        })
                    }
                },
                error: function(erro) {
                    console.log(erro);
                    ambilId("status").innerHTML = "";
                    ambilId("status").style.color = "red";
                    $('div.modal-content-loading').unblock();
                    Swal.fire(
                        'PERINGATAN!',
                        "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                        'warning'
                    );
                }
            });
        });
    </script>
<?php } ?>