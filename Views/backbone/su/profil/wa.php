<form id="formAktivasiWaModalData" action="./kirimAktivasiWa" method="post">
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="mb-3">
            <label for="_no_wa" class="form-label">Nomor WhatsApp</label>
            <input type="number" class="form-control no-wa" value="<?= is_numeric($user->no_hp) ? $user->no_hp : ''  ?>" id="_no_wa" name="_no_wa" placeholder="No WhatsApp..." onfocusin="inputFocus(this);">
            <p style="padding: 5px 0px;">Silah isi Nomor Whatsapp dengan format: 08xxxxxxxxxx (Contoh: 081208120812)</p>
            <div class="help-block _no_wa"></div>
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
        const nomor = document.getElementsByName('_no_wa')[0].value;

        if (nomor === "") {
            $("input#_no_wa").css("color", "#dc3545");
            $("input#_no_wa").css("border-color", "#dc3545");
            $('._no_wa').html('<ul role="alert" style="color: #dc3545; list-style-type: none; margin-block-start: 0px; padding-inline-start: 10px;"><li style="color: #dc3545;">Nomor Whatsapp tidak boleh kosong.</li></ul>');
            return false;
        }

        $.ajax({
            url: "./kirimAktivasiWa",
            type: 'POST',
            data: {
                nomor: nomor,
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