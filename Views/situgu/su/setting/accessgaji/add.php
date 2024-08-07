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
    let changedRole;
    // initSelect2("_role", ".content-detailModal");
    // initSelect2WithSearch("_pengguna", ".content-detailModal", changedRole);
    // initSelect2("_pengguna", ".content-detailModal");

    function changeRole(event) {
        const color = $(event).attr('name');
        $(event).removeAttr('style');
        $('.' + color).html('');
        // $('.pengguna').html("");

        changedRole = event.value;

        // if (event.value !== "") {
        //     $.ajax({
        //         url: './getPengguna',
        //         type: 'POST',
        //         data: {
        //             id: event.value,
        //         },
        //         dataType: 'JSON',
        //         beforeSend: function() {
        //             $('div._pengguna-block').block({
        //                 message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        //             });
        //         },
        //         success: function(msg) {
        //             $('div._pengguna-block').unblock();
        //             if (msg.status == 200) {
        //                 let html = "";
        //                 html += '<option value="">--Pilih--</option>';
        //                 if (msg.data.length > 0) {
        //                     for (let step = 0; step < msg.data.length; step++) {
        //                         html += '<option value="';
        //                         html += msg.data[step].id;
        //                         html += '">';
        //                         html += msg.data[step].fullname;
        //                         html += ' (';
        //                         html += msg.data[step].email;
        //                         html += ')</option>';
        //                     }

        //                 }

        //                 $('.pengguna').html(html);
        //             }
        //         },
        //         error: function(data) {
        //             $('div._pengguna-block').unblock();
        //         }
        //     })
        // }
    }

    $('#_pengguna').select2({
        dropdownParent: ".content-detailModal",
        ajax: {
            url: "./getPengguna",
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    keyword: params.term,
                    id: changedRole,
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                // params.page = params.page || 1;
                if (data.status === 200) {
                    return {
                        results: data.data
                    };
                } else {
                    return {
                        results: []
                    };
                }

                // return {
                //     results: data.items,
                //     pagination: {
                //         more: (params.page * 30) < data.total_count
                //     }
                // };
            },
            cache: true
        },
        placeholder: 'Cari Pengguna',
        minimumInputLength: 3,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
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

        $container.find(".select2-result-repository__title").text(repo.fullname);
        $container.find(".select2-result-repository__description").text(repo.npsn + " (" + repo.email + ")");

        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.fullname || repo.text;
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