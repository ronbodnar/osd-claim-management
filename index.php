<?php

require 'header.php';

?>

<?php if (!isLoggedIn()) {
    include 'login-form.php';
} else { ?>
    <div class="container-fluid pt-4">

        <div class="overlay-inner">
            <h3 class="text-light align-self-left fw-bold">Dashboard</h3>
        </div>

        <div class="row d-flex justify-content-center">

            <div class="col-md-4">
                <div class="card content d-flex" style="height: 160px;">
                    <div class="card-header">
                        Total Claims
                    </div>
                    <div class="card-body justify-content-center overflow-auto">
                        <div class="row d-flex align-items-center">
                            <div class="col-sm-4">
                                <p id="totalClaims" class="fw-bold text-center" style="font-size: 3rem;"></p>
                            </div>
                            <div class="col-sm-8">
                                <canvas id="totalClaimsChart" style="height: 80px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card content d-flex" style="height: 160px;">
                    <div class="card-header">
                        Pending Claims
                    </div>
                    <div class="card-body justify-content-center overflow-auto">
                        <div class="row d-flex align-items-center">
                            <div class="col-sm-12">
                                <p class="fw-bold text-center" style="font-size: 3rem;"><?php echo $database->getOSDClaimCount(3); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card content d-flex" style="height: 160px;">
                    <div class="card-header">
                        New Claims
                    </div>
                    <div class="card-body justify-content-center overflow-auto">
                        <div class="row d-flex align-items-center">
                            <div class="col-sm-12">
                                <p class="fw-bold text-center" style="font-size: 3rem;"><?php echo $database->getOSDClaimCount(1); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card content">
                    <div class="card-body table-responsive" style="margin-top: 10px;">
                        <table class="table table-sm table-striped table-hover table-border" id="claimsTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Facility</th>
                                    <th>Trailer Number</th>
                                    <th>Trip Number</th>
                                    <th>Cases</th>
                                    <th>Type</th>
                                    <th>Received</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
<?php } ?>

<?php include 'footer.php'; ?>