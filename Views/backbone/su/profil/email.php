<form id="formAktivasiWaModalData" action="./kirimAktivasiEmail" method="post">
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="mb-3">
            <label for="_email" class="form-label">Email</label>
            <input type="email" class="form-control email" value="<?= $user->email  ?>" id="_email" name="_email" placeholder="Email..." onfocusin="inputFocus(this);" readonly />
            <!-- <p style="padding: 5px 0px;">Silah isi Nomor Whatsapp dengan format: 08xxxxxxxxxx (Contoh: 081208120812)</p> -->
            <div class="help-block _email"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary waves-effect waves-light">Kirim Kode Aktivasi</button>
    </div>
</form>

<script>
    $("#formAktivasiWaModalData").on("submit", function(e) {
        e.preventDefault();
        const email = document.getElementsByName('_email')[0].value;

        if (email === "") {
            Swal.fire(
                'PERINGATAN!',
                "Email tidak valid. Silahkan hubungi Admin / Submit Pengaduan untuk permasalahan email anda.",
                'warning'
            );
        }

        $.ajax({
            url: "./kirimAktivasiEmail",
            type: 'POST',
            data: {
                email: email,
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
                    $('.contentAktivasiBodyModal').html(resul.data);
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

    });
</script>