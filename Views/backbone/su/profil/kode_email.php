<form id="formKodeAktivasiWaModalData" action="./verifiAktivasiEmail" method="post">
    <input type="hidden" id="_id" name="_id" value="<?= $user->id ?>" />
    <input type="hidden" id="_k" name="_k" value="<?= $kode_aktivasi ?>" />
    <input type="hidden" id="_email" name="_email" value="<?= $email ?>" />
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="text-center">
            <div class="p-2 mt-4">
                <h4>Kode Aktivasi Telah Dikirim.</h4>
                <p class="mb-5">Silahkan masukkan 4 digit kode aktivasi yang telah dikirimkan ke Email: <span class="fw-semibold"><?= $email ?></span></p>
                <div class="row">
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="digit1-input" class="visually-hidden">Dight 1</label>
                            <input type="text" class="form-control form-control-lg text-center two-step" maxLength="1" data-value="1" id="digit1-input" name="digit1-input">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="mb-3">
                            <label for="digit2-input" class="visually-hidden">Dight 2</label>
                            <input type="text" class="form-control form-control-lg text-center two-step" maxLength="1" data-value="2" id="digit2-input" name="digit2-input">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="mb-3">
                            <label for="digit3-input" class="visually-hidden">Dight 3</label>
                            <input type="text" class="form-control form-control-lg text-center two-step" maxLength="1" data-value="3" id="digit3-input" name="digit3-input">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="mb-3">
                            <label for="digit4-input" class="visually-hidden">Dight 4</label>
                            <input type="text" class="form-control form-control-lg text-center two-step" maxLength="1" data-value="4" id="digit4-input" name="digit4-input">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="aksiSubmitVerifikasi(this);" class="btn btn-primary waves-effect waves-light">Konfirmasi</button>
    </div>
</form>

<script>
    function moveToNext(t, u) {
        0 < t.value.length && $("#digit" + u + "-input").focus()
    }
    var countKd = 1;
    $(".two-step").keyup(function(t) {
        0 == countKd && (countKd = 1), 8 === t.keyCode ? (5 == countKd && (countKd = 3), $("#digit" + countKd + "-input").focus(), countKd--) : 0 < countKd && (countKd++, $("#digit" + countKd + "-input").focus())
    });

    function aksiSubmitVerifikasi(e) {
        const id = document.getElementsByName('_id')[0].value;
        const email = document.getElementsByName('_email')[0].value;
        const _k = document.getElementsByName('_k')[0].value;
        const d1 = document.getElementsByName('digit1-input')[0].value;
        const d2 = document.getElementsByName('digit2-input')[0].value;
        const d3 = document.getElementsByName('digit3-input')[0].value;
        const d4 = document.getElementsByName('digit4-input')[0].value;

        const kode = d1 + d2 + d3 + d4;

        if (kode.length !== 4) {
            Swal.fire(
                'PERINGATAN!',
                "Inputan Kode Verifikasi Belum Valid",
                'warning'
            );
            return false;
        }

        $.ajax({
            url: "./verifiAktivasiEmail",
            type: 'POST',
            data: {
                id: id,
                email: email,
                kode: kode,
                fth: _k
            },
            dataType: 'JSON',
            beforeSend: function() {
                $('div.modal-content-loading-aktivasi').block({
                    message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                });
            },
            success: function(resul) {
                $('div.modal-content-loading-aktivasi').unblock();

                if (resul.status !== 200) {
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
                            'PERINGATAN!',
                            resul.message,
                            'warning'
                        );
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