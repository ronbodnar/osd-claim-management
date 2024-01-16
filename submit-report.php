<?php

require 'header.php';

?>
<?php if (!isLoggedIn()) {
    include 'login-form.php';
} else { ?>
    <div class="container-fluid pt-4" id="default">
        <div class="overlay-inner">
            <h3 class="text-light text-center fw-bold">OS&D Reporting Form</h3>
        </div>
        <div class="row d-flex align-items-center justify-content-center">
            <div class="col-md-4">
                <div class="card content">
                    <div class="card-body">
                        <form class="form-signin" id="osdForm" action="process.php" method="POST" enctype='multipart/form-data' novalidate>

                            <label for="tripNumber" class="form-label fw-bold">Trip Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tripNumber" name="tripNumber" placeholder="1324221" value="" autofocus>

                                <div class="invalid-feedback">
                                    You must enter a trip number
                                </div>
                            </div>

                            <label for="fbNumber" class="form-label pt-3 fw-bold">Freight Bill Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded" id="fbNumber" name="fbNumber" placeholder="G432312" value="">

                                <div class="invalid-feedback">
                                    You must enter a freight bill number
                                </div>
                            </div>

                            <label for="cases" class="form-label pt-3 fw-bold">Number of Cases</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cases" name="cases" placeholder="30" value="">

                                <div class="invalid-feedback">
                                    You must enter number of cases
                                </div>
                            </div>

                            <label for="productNumber" class="form-label pt-3 fw-bold">Product SKU</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="productNumber" name="productNumber" placeholder="CD11V06-02014" value="">

                                <div class="invalid-feedback">
                                    You must enter a product number
                                </div>
                            </div>

                            <label for="trailerNumber" class="form-label pt-3 fw-bold">Trailer Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="trailerNumber" name="trailerNumber" placeholder="53448" value="">

                                <div class="invalid-feedback">
                                    You must enter a trailer number
                                </div>
                            </div>

                            <div class="pt-4 pb-1 fw-bold">What type of claim are you reporting?</div>

                            <div id="claimTypes">
                                <label class="checkbox-wrap" id="overageLabel">Overage
                                    <input type="checkbox" name="overage" id="oCheck" class="checkbox">
                                    <span class="checkmark" id="overageCheck"></span>
                                </label>

                                <label class="checkbox-wrap" id="shortageLabel">Shortage
                                    <input type="checkbox" name="shortage" id="sCheck" class="checkbox">
                                    <span class="checkmark" id="shortageCheck"></span>
                                </label>

                                <label class="checkbox-wrap" id="damageLabel">Damage
                                    <input type="checkbox" name="damage" id="dCheck" class="checkbox">
                                    <span class="checkmark" id="damageCheck"></span>
                                </label>
                                <div class="invalid-feedback pt-1">
                                    You must select a claim type
                                </div>
                            </div>

                            <div class="pt-3 pb-1 fw-bold">Did the receiver accept the product(s)?</small></div>

                            <div id="productReceived">
                                <label class="checkbox-wrap" id="yesLabel">Yes
                                    <input type="checkbox" name="yes" id="yCheck" class="checkbox">
                                    <span class="checkmark" id="yesCheck"></span>
                                </label>
                                <label class="checkbox-wrap" id="noLabel">No
                                    <input type="checkbox" name="no" id="nCheck" class="checkbox">
                                    <span class="checkmark" id="noCheck"></span>
                                </label>
                                <div class="invalid-feedback">
                                    You must select an option
                                </div>
                            </div>

                            <div class="pt-1">
                                <label for="cameraInput" class="form-label pt-1 fw-bold">Pictures <br /><small class="fw-normal" style="color: #7f7f7f;">(Include pictures of the bill(s) and any damage)</small></label>
                                <input type="file" class="form-control" id="cameraInput" names="pictures[]" accept="image/*" multiple required>
                                <div class="invalid-feedback">
                                    You must select at least 1 picture to upload
                                </div>
                            </div>

                            <div class="row border mt-3" id="selectedFiles"></div>

                            <button type="submit" class="btn btn-mron-fw mt-4 text-light">Submit</button>
                            <button type="button" class="btn btn-danger mt-4 text-light" id="clearForm" style="width: 100%;">Clear Form</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container form-signin" id="success" hidden>
        <div class="text-center"><i class="bi bi-check-circle-fill text-success" style="font-size: 150px;"></i></div>
        <h1 class="h3 m-3 fw-bold text-center">Success!</h1>
        <p class="text-center pt-4">Your OS&D report has been successfully submitted and you will be contacted for further information if needed.</p>
        <p class="text-center pt-4">If you need to make another report, you can <a href=".">submit a new report</a></p>
    </div>

    <div class="container form-signin" id="failure" hidden>
        <div class="text-center"><i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 150px;"></i></div>
        <h1 class="h3 m-3 fw-bold text-center">Uh oh :(</h1>
        <p class="text-center pt-4">We ran into an error while trying to process your OS&D report. This could be due to a loss of network connection or a server issue. </p>
        <p class="text-center pt-4">Please try and <a href="#" onclick="resubmitForm()">submit your report again</a></p>
    </div>
<?php } ?>

<?php include 'footer.php'; ?>