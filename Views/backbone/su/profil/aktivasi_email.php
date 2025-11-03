<?php if (isset($data)) { ?>

    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="alert alert-danger" role="alert">
            Akun anda terdeteksi belum melakukan aktivasi Email.\nSilahkan untuk melakukan aktivasi Email terlebih dahulu.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="aksiAktivasiEmail(this);" id="aktivasi-button-wa" class="btn btn-primary waves-effect waves-light aktivasi-button-wa">Aktivasi Sekarang</button>
    </div>

    <script>
        function aksiAktivasiEmail(event) {
            $.ajax({
                url: './getAktivasiEmail',
                type: 'POST',
                data: {
                    id: 'email',
                },
                dataType: 'JSON',
                beforeSend: function() {
                    $('.aktivasi-button-wa').attr('disabled', true);
                    $('div.modal-content-loading-aktivasi').block({
                        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    });
                },
                success: function(resul) {
                    $('div.modal-content-loading-aktivasi').unblock();
                    if (resul.status == 200) {
                        $('.contentAktivasiBodyModal').html(resul.data);
                    } else {
                        if (resul.status == 404) {
                            Swal.fire(
                                'PERINGATAN!',
                                resul.message,
                                'warning'
                            ).then((valRes) => {
                                reloadPage(resul.redirrect);
                            })
                        } else {
                            if (resul.status == 401) {
                                Swal.fire(
                                    'PERINGATAN!',
                                    resul.message,
                                    'warning'
                                ).then((valRes) => {
                                    reloadPage();
                                })
                            } else {
                                $('.aktivasi-button-wa').attr('disabled', false);
                                Swal.fire(
                                    'PERINGATAN!!!',
                                    resul.message,
                                    'warning'
                                );
                            }
                        }
                    }
                },
                error: function(data) {
                    $('.aktivasi-button-wa').attr('disabled', false);
                    $('div.modal-content-loading-aktivasi').unblock();
                    Swal.fire(
                        'PERINGATAN!',
                        "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                        'warning'
                    );
                }
            });
        }
    </script>

<?php } ?>