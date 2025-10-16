<form id="formAddModalData" action="./addSave" method="post">
    <div class="modal-body">
        <div class="mb-3">
            <label for="_role" class="col-form-label">Role:</label>
            <select class="form-control" id="_role" name="_role" onchange="changeRole(this)" required>
                <option value="">--Pilih--</option>
                <?php if (isset($roles)) {
                    if (count($roles) > 0) {
                        foreach ($roles as $key => $value) { ?>
                            <option value="<?= $value->id ?>"><?= $value->role ?></option>
                <?php }
                    }
                } ?>
            </select>
            <div class="help-block _role"></div>
        </div>
        <div class="mb-3 _pengguna-block">
            <label for="_name" class="col-form-label">Pengguna:</label>
            <select class="form-control pengguna" id="_pengguna" name="_pengguna" style="width: 100%">
                <option value="">&nbsp;</option>
            </select>
            <div class="help-block _pengguna"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan</button>
    </div>
</form>

<script>
    // initSelect2("_role", ".content-detailModal");
    initSelect2("_pengguna", ".content-detailModal");

    function changeRole(event) {
        const color = $(event).attr('name');
        $(event).removeAttr('style');
        $('.' + color).html('');

        if (event.value !== "") {
            $.ajax({
                url: './getPengguna',
                type: 'POST',
                data: {
                    id: event.value,
                },
                dataType: 'JSON',
                beforeSend: function() {
                    $('div._pengguna-block').block({
                        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    });
                },
                success: function(msg) {
                    $('div._pengguna-block').unblock();
                    if (msg.status == 200) {
                        let html = "";
                        html += '<option value="">--Pilih--</option>';
                        if (msg.data.length > 0) {
                            for (let step = 0; step < msg.data.length; step++) {
                                html += '<option value="';
                                html += msg.data[step].id;
                                html += '">';
                                html += msg.data[step].fullname;
                                html += ' (';
                                html += msg.data[step].email;
                                html += ')</option>';
                            }

                        }

                        $('.pengguna').html(html);
                    }
                },
                error: function(data) {
                    $('div._pengguna-block').unblock();
                }
            })
        }
    }

    $("#formAddModalData").on("submit", function(e) {
        e.preventDefault();
        const role = document.getElementsByName('_role')[0].value;
        const pengguna = document.getElementsByName('_pengguna')[0].value;

        if (role === "") {
            $("select#_role").css("color", "#dc3545");
            $("select#_role").css("border-color", "#dc3545");
            $('._role').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">Silahkan pilih role.</li></ul>');
            return false;
        }
        if (pengguna === "") {
            $("select#_pengguna").css("color", "#dc3545");
            $("select#_pengguna").css("border-color", "#dc3545");
            $('._pengguna').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">Silahkan pilih pengguna.</li></ul>');
            return false;
        }

        $.ajax({
            url: "./addSave",
            type: 'POST',
            data: {
                role: role,
                pengguna: pengguna,
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
    });
</script>