<form id="formAddModalData" action="./uploadSave" method="post" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mt-3">
                            <label for="_file" class="form-label">Upload Data Pangkat KGB: </label>
                            <input class="form-control" type="file" id="_file" name="_file" onFocus="inputFocus(this);" accept=".xls, .xlsx" onchange="loadFile()">
                            <p class="font-size-11">Format : <code data-toggle="tooltip" data-placement="bottom" title="xls, xlsx">Files</code> and Maximum File Size <code>5 Mb</code></p>
                            <div class="help-block _file" for="_file"></div>
                        </div>
                    </div>
                </div>
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
    function loadFile() {
        const input = document.getElementsByName('_file')[0];
        if (input.files && input.files[0]) {
            var file = input.files[0];

            var mime_types = ['application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/vnd.ms-office', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

            if (mime_types.indexOf(file.type) == -1) {
                input.value = "";
                Swal.fire(
                    'Warning!!!',
                    "Hanya file type xls dan xlsx yang diizinkan.",
                    'warning'
                );
                return false;
            }

            if (file.size > 10 * 1024 * 1000) {
                input.value = "";
                Swal.fire(
                    'Warning!!!',
                    "Ukuran file tidak boleh lebih dari 10 Mb.",
                    'warning'
                );
                return false;
            }
        } else {
            console.log("failed Load");
        }
    }

    $("#formAddModalData").on("submit", function(e) {
        e.preventDefault();
        const fileName = document.getElementsByName('_file')[0].value;

        const formUpload = new FormData();
        if (fileName !== "") {
            const file = document.getElementsByName('_file')[0].files[0];
            formUpload.append('_file', file);
        }

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
            url: "./uploadSave",
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
                    // ambilId("status").innerHTML = "gagal";
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
                    // ambilId("status").innerHTML = resul.message;
                    // ambilId("status").style.color = "green";
                    // ambilId("progressBar").value = 100;
                    // Swal.fire(
                    //     'SELAMAT!',
                    //     resul.message,
                    //     'success'
                    // ).then((valRes) => {
                    //     reloadPage();
                    // })

                    $('.contentBodyModal').html(resul.data);
                }
            },
            error: function(erro) {
                console.log(erro);
                // ambilId("status").innerHTML = "Upload Failed";
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