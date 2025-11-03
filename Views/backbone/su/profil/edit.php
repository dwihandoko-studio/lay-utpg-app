<?php if (isset($data)) { ?>
    <form id="formEditModalData" action="./editSave" method="post">
        <input type="hidden" id="_id" name="_id" value="<?= $data->id ?>" />
        <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
            <div class="mb-3">
                <label for="_fullname" class="form-label">Nama</label>
                <input type="text" class="form-control fullname" value="<?= $data->fullname ?>" id="_fullname" name="_fullname" placeholder="Nama..." onfocusin="inputFocus(this);">
                <div class="help-block _fullname"></div>
            </div>
            <div class="mb-3">
                <label for="_nohp" class="form-label">No Handphone</label>
                <input type="text" class="form-control nohp" value="<?= $data->no_hp ?>" id="_nohp" name="_nohp" placeholder="No handphone..." onfocusin="inputFocus(this);">
                <div class="help-block _nohp"></div>
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
            const nama = document.getElementsByName('_fullname')[0].value;
            const nohp = document.getElementsByName('_nohp')[0].value;

            if (nama === "") {
                $("input#_fullname").css("color", "#dc3545");
                $("input#_fullname").css("border-color", "#dc3545");
                $('._fullname').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Nama tidak boleh kosong.</li></ul>');
                return false;
            }
            if (nohp === "") {
                $("input#_nohp").css("color", "#dc3545");
                $("input#_nohp").css("border-color", "#dc3545");
                $('._nohp').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">No HP tidak boleh kosong.</li></ul>');
                return false;
            }

            Swal.fire({
                title: 'Apakah anda yakin ingin mengupdate data ini?',
                text: "Update Data Akun: <?= $data->fullname ?>",
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
                            nama: nama,
                            nohp: nohp,
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