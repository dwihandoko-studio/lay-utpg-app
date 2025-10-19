<form id="formTarikDataLocalModalData" method="post">
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="mb-3">
            <label for="_token" class="form-label">Token Api Local Dapodik</label>
            <input type="text" class="form-control token" <?= isset($data) ? ($data->token === NULL || $data->token === "" ? '' : 'value="' . $data->token . '"') : '' ?> id="_token" name="_token" placeholder="Token Api Dapodik Local..." onfocusin="inputFocus(this);" />
            <!-- <p style="padding: 5px 0px;">Silah isi Nomor Whatsapp dengan format: 08xxxxxxxxxx (Contoh: 081208120812)</p> -->
            <div class="help-block _token"></div>
        </div>
        <div id="result"></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="getSyncDapoLocal(this)" class="btn btn-primary waves-effect waves-light">Tarik Data Semua PTK Dari Dapodik Local</button>
    </div>
</form>

<script>
    function getSyncDapoLocal(event) {
        const token = document.getElementsByName('_token')[0].value;
        const npsn = '<?= isset($sekolah) ? $sekolah->npsn : '' ?>';

        if (token === "") {
            Swal.fire(
                'PERINGATAN!',
                "Token Api Dapodik tidak valid. Silahkan hubungi Operator Sekolah anda.",
                'warning'
            );
        }

        $.ajax({
            url: "http://localhost:5774/WebService/getGtk?npsn=" + npsn,
            type: 'GET',
            headers: {
                'Authorization': "Bearer " + token,
                'Cache-Control': 'no-cache',
                'Postman-Token': generatePostmanToken(),
                'User-Agent': 'PostmanRuntime/7.49.0',
                'Accept': '*/*',
                'Accept-Encoding': 'gzip, deflate, br',
                'Connection': 'keep-alive',
                'Content-Type': 'application/json',
                'Cookie': 'killme=dont'
            },
            // HAPUS dataType: 'jsonp' untuk request normal
            dataType: 'json', // Ganti dengan json untuk response JSON normal
            crossDomain: true, // Izinkan cross-domain
            xhrFields: {
                withCredentials: true // Include credentials/cookies
            },
            success: function(response) {
                $('#result').html(`
                <h3>Success Response:</h3>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            `);
                console.log('Success:', response);
            },
            error: function(xhr, status, error) {
                $('#result').html(`
                <h3>Error:</h3>
                <p>${error}</p>
                <p>Status: ${status}</p>
                <p>HTTP Status: ${xhr.status}</p>
                <pre>${xhr.responseText}</pre>
            `);
                console.error('Error:', error, 'Status:', status, 'HTTP Status:', xhr.status);
            },
            beforeSend: function(xhr) {
                console.log('All Headers being sent:');
                // Tambahkan header tambahan untuk meniru browser
                xhr.setRequestHeader('Sec-Fetch-Mode', 'cors');
                xhr.setRequestHeader('Sec-Fetch-Site', 'same-site');
            }
        });
    }

    // Generate random Postman-Token (mirip dengan yang di Postman)
    function generatePostmanToken() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';
        for (let i = 0; i < 36; i++) {
            token += chars[Math.floor(Math.random() * chars.length)];
        }
        return token;
    }
</script>