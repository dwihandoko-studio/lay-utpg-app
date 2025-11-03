<?php if (isset($data)) { ?>
    <form id="formEditModalData" action="./savePassword" method="post">
        <input type="hidden" id="_id" name="_id" value="<?= $data->id ?>" />
        <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
            <div class="mb-3">
                <label for="_password_lama" class="form-label">Kata Sandi Lama</label>
                <div class="input-group auth-pass-inputgroup">
                    <input type="password" class="form-control password-lama" id="_password_lama" name="_password_lama" placeholder="Kata sandi lama" aria-label="Kata Sandi Lama" aria-describedby="password-addon-old">
                    <button class="btn btn-light " onclick="showPassword('.password-lama')" type="button" id="password-addon-old"><i class="mdi mdi-eye-outline"></i></button>
                </div>
                <div class="help-block _password_lama"></div>
            </div>
            <div class="mb-3">
                <label for="_password_baru" class="form-label">Kata Sandi Baru</label>
                <div class="input-group auth-pass-inputgroup">
                    <input type="password" class="form-control password-baru" id="_password_baru" name="_password_baru" placeholder="Kata sandi baru" aria-label="Kata Sandi Baru" aria-describedby="password-addon-new">
                    <button class="btn btn-light " onclick="showPassword('.password-baru')" type="button" id="password-addon-new"><i class="mdi mdi-eye-outline"></i></button>
                </div>
                <div class="help-block _password_baru"></div>
            </div>
            <div class="mb-3">
                <label for="_ulangi_password_baru" class="form-label">Ulangi Kata Sandi Baru</label>
                <div class="input-group auth-pass-inputgroup">
                    <input type="password" class="form-control ulangi-password-baru" id="_ulangi_password_baru" name="_ulangi_password_baru" placeholder="Ulangi kata sandi baru" aria-label="Ulangi Kata Sandi Baru" aria-describedby="password-addon-re-new">
                    <button class="btn btn-light " onclick="showPassword('.ulangi-password-baru')" type="button" id="password-addon-re-new"><i class="mdi mdi-eye-outline"></i></button>
                </div>
                <div class="help-block _ulangi_password_baru"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary waves-effect waves-light">SIMPAN</button>
        </div>
    </form>

    <script>
        function showPassword(event) {
            ("password" == $(event).attr("type") ?
                $(event).attr("type", "input") :
                $(event).attr("type", "password"));
        };
        $("#formEditModalData").on("submit", function(e) {
            e.preventDefault();
            const id = document.getElementsByName('_id')[0].value;
            const old_password = document.getElementsByName('_password_lama')[0].value;
            const new_password = document.getElementsByName('_password_baru')[0].value;
            const re_new_password = document.getElementsByName('_ulangi_password_baru')[0].value;

            if (old_password === "") {
                $("input#_password_lama").css("color", "#dc3545");
                $("input#_password_lama").css("border-color", "#dc3545");
                $('._password_lama').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Kata sandi lama tidak boleh kosong.</li></ul>');
                return false;
            }
            if (new_password === "") {
                $("input#_password_baru").css("color", "#dc3545");
                $("input#_password_baru").css("border-color", "#dc3545");
                $('._password_baru').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Kata sandi baru tidak boleh kosong.</li></ul>');
                return false;
            }
            if (new_password.length < 6) {
                $("input#_password_baru").css("color", "#dc3545");
                $("input#_password_baru").css("border-color", "#dc3545");
                $('._password_baru').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Panjang kata sandi minimal 6 karakter.</li></ul>');
                return false;
            }
            if (re_new_password === "") {
                $("input#_ulangi_password_baru").css("color", "#dc3545");
                $("input#_ulangi_password_baru").css("border-color", "#dc3545");
                $('._ulangi_password_baru').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Ulangi kata sandi baru tidak boleh kosong.</li></ul>');
                return false;
            }

            if (re_new_password !== new_password) {
                $("input#_ulangi_password_baru").css("color", "#dc3545");
                $("input#_ulangi_password_baru").css("border-color", "#dc3545");
                $('._ulangi_password_baru').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Ulangi kata sandi baru tidak sama.</li></ul>');
                return false;
            }

            Swal.fire({
                title: 'Apakah anda yakin ingin mengupdate password akun ini?',
                text: "Update Password Akun: <?= $data->fullname ?>",
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "./savePassword",
                        type: 'POST',
                        data: {
                            id: id,
                            old_password: old_password,
                            new_password: new_password,
                            re_new_password: re_new_password,
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