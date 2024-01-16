<?php

require 'header.php';

$facility = $database->getFacility($_GET['id']);

$claims = $database->getClaimsByFacilityId($_GET['id']);

?>

<?php if (isLoggedIn()) { ?>
    <div class="container-fluid pt-3">
        <div class="overlay-inner">
            <h3 class="text-light align-self-left fw-bold pt-3">Facility Details</h3>
        </div>
        <div class="card content mt-5">
            <div class="card-body table-responsive">
                <div class="pt-2">
                    <div class="row g-0">
                        <div class="col-sm-1 text-center"><i class="bi bi-person-circle" style="font-size: 3rem;"></i></div>
                        <div class="col-sm-2">
                            <h4 class="fw-bold"><?php echo $facility['name']; ?></h4>
                            <p style="font-size: 1rem;"><?php echo $facility['city'] . ', ' . $facility['state']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="row d-flex justify-content-between align-items-center pt-4">
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Total Claims</div>
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Phone Number</div>
                </div>

                <div class="row d-flex justify-content-start align-items-center pb-4" style="border-bottom: 1px solid var(--separator-line-color);">
                    <div class="col text-center"><?php echo $database->getOSDClaimCountByFacility($_GET['id']); ?></div>
                    <div class="col text-center"><?php echo $facility['phone_number']; ?></div>
                </div>

                <div class="pt-3 pb-3">
                    <ul class="nav nav-tabs user-tabs d-flex justify-content-start align-items-center text-center" role="tablist">
                        <li class="nav-item user-tab" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <table class="table table-sm table-striped" id="facilityClaimsTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Driver</th>
                                    <th>Trip Number</th>
                                    <th>Cases</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                foreach ($claims as $claim) {
                                    // Format claim date and add time
                                    $date = new DateTimeImmutable($claim['date']);

                                    $claim['date'] = $date->format('m/d/Y');
                                    $claim['time'] = $date->format('H:i A');

                                    // Format status cell with colored pill badges
                                    $statusColors = array(
                                        1 => 'bg-danger',
                                        2 => 'bg-info',
                                        3 => 'bg-warning',
                                        4 => 'bg-success'
                                    );

                                    $statusId = $claim['status_id'];
                                    $statusText = '<span class="badge rounded-pill ' . $statusColors[$statusId] . ' w-100">' . ucfirst($claim['status']) . '</span>';

                                    echo '<tr>';
                                    echo '<td></td>';
                                    echo '<td>' . $claim['id'][0] . '</td>';
                                    echo '<td>' . $claim['date'] . '</td>';
                                    echo '<td>' . $claim['time'] . '</td>';
                                    echo '<td><a class="text-mron" href="driver?id=' . $claim['driver_id'] . '">' . $database->getUserData($claim['driver_id'])->getFullName() . '</a></td>';
                                    echo '<td>' . $claim['trip_number'] . '</td>';
                                    echo '<td>' . $claim['cases'] . '</td>';
                                    echo '<td>' . ucfirst($claim['type']) . '</td>';
                                    echo '<td>' . $statusText . '</td>';
                                    echo '</td>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php
} else {
    include 'login-form.php';
} ?>

    <?php include 'footer.php'; ?>