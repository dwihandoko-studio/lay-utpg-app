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
    async function getSyncDapoLocal(event) {
        const token = document.getElementsByName('_token')[0].value;
        const npsn = '<?= isset($sekolah) ? $sekolah->npsn : '' ?>';

        if (token === "") {
            Swal.fire(
                'PERINGATAN!',
                "Token Api Dapodik tidak valid. Silahkan hubungi Operator Sekolah anda.",
                'warning'
            );
        }

        const url = "http://localhost:5774/WebService/getGtk?npsn=" + npsn;

        // Buat XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Setup event handlers
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                handleResponse(xhr);
            }
        };

        xhr.onerror = function() {
            $('#result').html(`
            <h3>Network Error</h3>
            <p>Failed to connect to server</p>
        `);
        };

        xhr.ontimeout = function() {
            $('#result').html(`
            <h3>Timeout Error</h3>
            <p>Request took too long to complete</p>
        `);
        };

        // Open connection
        xhr.open('GET', url, true);

        // Set timeout (30 detik)
        xhr.timeout = 30000;

        // Set headers persis seperti Postman
        const headers = {
            'Authorization': 'Bearer ' + token,
            'Cache-Control': 'no-cache',
            'Postman-Token': generatePostmanToken(),
            'User-Agent': 'PostmanRuntime/7.49.0',
            'Accept': '*/*',
            'Accept-Encoding': 'gzip, deflate, br',
            'Connection': 'keep-alive',
            'Content-Type': 'application/json',
            'Cookie': 'killme=dont',
            'Origin': window.location.origin, // Origin current page
            'Referer': window.location.href // Referer current page
        };

        // Apply semua headers
        Object.keys(headers).forEach(key => {
            xhr.setRequestHeader(key, headers[key]);
        });

        console.log('Sending request to:', url);
        console.log('With headers:', headers);

        // Kirim request
        xhr.send();
    }

    function handleResponse(xhr) {
        const resultDiv = $('#result');

        if (xhr.status >= 200 && xhr.status < 300) {
            // Success
            try {
                const response = JSON.parse(xhr.responseText);
                resultDiv.html(`
                <div style="background: #d4edda; padding: 15px; border-radius: 5px;">
                    <h3 style="color: #155724;">✅ Request Successful</h3>
                    <p><strong>Status:</strong> ${xhr.status} ${xhr.statusText}</p>
                    <h4>Response Data:</h4>
                    <pre style="background: white; padding: 10px;">${JSON.stringify(response, null, 2)}</pre>
                </div>
            `);
                console.log('Success Response:', response);
            } catch (e) {
                // Jika response bukan JSON
                resultDiv.html(`
                <div style="background: #d4edda; padding: 15px; border-radius: 5px;">
                    <h3 style="color: #155724;">✅ Request Successful (Raw Response)</h3>
                    <p><strong>Status:</strong> ${xhr.status} ${xhr.statusText}</p>
                    <h4>Raw Response:</h4>
                    <pre style="background: white; padding: 10px;">${xhr.responseText}</pre>
                </div>
            `);
            }
        } else {
            // Error
            resultDiv.html(`
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px;">
                <h3 style="color: #721c24;">❌ Request Failed</h3>
                <p><strong>Status:</strong> ${xhr.status} ${xhr.statusText}</p>
                <h4>Error Response:</h4>
                <pre style="background: white; padding: 10px;">${xhr.responseText || 'No response body'}</pre>
            </div>
        `);
            console.error('Error Response:', xhr.status, xhr.statusText, xhr.responseText);
        }

        // Log response headers
        console.log('Response Headers:');
        console.log(xhr.getAllResponseHeaders());
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