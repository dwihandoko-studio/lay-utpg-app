<?php if (isset($data)) { ?>
    <div class="modal-body">
        <div class="row">
            <?php foreach ($data as $key => $value) { ?>
                <div class="col-lg-6">
                    <label class="col-form-label"><?= $key ?> :</label>
                    <input type="text" class="form-control" value="<?= $value ?>" readonly />
                </div>
            <?php } ?>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
    </div>
<?php } ?>