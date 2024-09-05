<?php

require '../header.php';

$claim = $database->getClaim($_GET['id']);

// Format claim date and add time
$date = new DateTimeImmutable($claim['date']);

$claim['date'] = $date->format('m/d/Y');
$claim['time'] = $date->format('H:i A');

$driverName = $database->getUserData($claim['driver_id'])->getFullName();
$driverLink = '<a href="driver.php?id=' . $claim['driver_id'] . '" class="text-mron">' . $driverName . '</a>';

$facilityLink = '<a href="facility.php?id=' . $claim['id'][3] . '" class="text-mron">' . $claim['name'] . ' / ' . $claim['city'] . '</a>';

//$imageDirectory = getcwd() .  "/uploads/" . $claim['id'][0] . "/";
$imageDirectory = getcwd() .  "/uploads/1/";

$fileCount = 0;

$files = glob($imageDirectory . "*");

if ($files) {
    $fileCount = count($files) / 2;
}

?>

<?php if (isLoggedIn()) { ?>
    <div class="container-fluid pt-3">
        <div class="overlay-inner">
            <h3 class="text-light align-self-left fw-bold pt-3">Claim Details</h3>
        </div>
        <div class="card content mt-3">
            <div class="card-body table-responsive">
                <div class="pt-2">
                    <div class="row g-0">
                        <div class="col-sm-1 text-center"><i class="bi bi-truck" style="font-size: 3rem;"></i></div>
                        <div class="col-sm-2 pt-4">
                            <h5 class="fw-bold">Claim #: <?php echo $_GET['id']; ?></h5>
                        </div>
                    </div>
                </div>

                <div class="row d-flex justify-content-between align-items-center pt-4">
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Status</div>
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Type</div>
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Rejected?</div>
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Date</div>
                    <div class="col text-center fw-bold" style="font-size: 0.9rem;">Time</div>
                </div>

                <div class="row d-flex justify-content-start align-items-center pb-4" style="border-bottom: 1px solid var(--separator-line-color);">
                    <div class="col text-center">
                        <div class="dropdown">
                            <?php
                            $outlines = array('not processed' => 'danger', 'processing' => 'info', 'pending' => 'warning', 'complete' => 'success');
                            $outline = $outlines[$claim['status']];
                            ?>
                            <button type="button" class="btn btn-outline-<?php echo $outline; ?> dropdown-toggle" id="claimStatusDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo ucfirst($claim['status']); ?>
                            </button>
                            <ul class="dropdown-menu" id="claimStatusDropdown" aria-labelledby="claimStatusDropdown">
                                <li><a href="#" class="dropdown-item" id="processing">Processing</a></li>
                                <li><a href="#" class="dropdown-item" id="processing">Pending</a></li>
                                <li><a href="#" class="dropdown-item" id="processing">Complete</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col text-center"><?php echo ucfirst($claim['type']); ?></div>
                    <div class="col text-center"><?php echo ($claim['received'] == 0 ? "Yes" : "No"); ?></div>
                    <div class="col text-center"><?php echo $claim['date']; ?></div>
                    <div class="col text-center"><?php echo $claim['time']; ?></div>
                </div>

                <div class="pt-3 pb-3">
                    <ul class="nav nav-tabs user-tabs d-flex justify-content-start align-items-center text-center" role="tablist">
                        <li class="nav-item user-tab" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                        </li>
                        <li class="nav-item user-tab" role="presentation">
                            <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab" aria-controls="images" aria-selected="true">Images (<?php echo $fileCount; ?>)</button>
                        </li>
                        <li class="nav-item user-tab" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="true">Notes</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="row pt-2 d-flex justify-content-between align-items-center text-center">
                                    <div class="col-md-3 pt-2 pb-2 fw-bold" style="font-size: 0.9rem;">Trip Number</div>
                                    <div class="col-md-3 pt-2 pb-2 fw-bold" style="font-size: 0.9rem;">Freight Bill Number</div>
                                    <div class="col-md-3 pt-2 pb-2 fw-bold" style="font-size: 0.9rem;">Product Code</div>
                                    <div class="col-md-3 pt-2 pb-2 fw-bold" style="font-size: 0.9rem;">Number of Cases</div>
                                    <div class="col-md-3 pb-3" id="averageShipments" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $claim['trip_number']; ?></div>
                                    <div class="col-md-3 pb-3" id="averageBackhauls" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $claim['freight_bill_number']; ?></div>
                                    <div class="col-md-3 pb-3" id="averageShipments" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $claim['product_code']; ?></div>
                                    <div class="col-md-3 pb-3" id="averageBackhauls" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $claim['cases']; ?></div>

                                    <div class="col-md-6 pt-4 pb-2 fw-bold" style="font-size: 0.9rem;">Driver Name</div>
                                    <div class="col-md-6 pt-4 pb-2 fw-bold" style="font-size: 0.9rem;">Facility Name</div>
                                    <div class="col-md-6 pb-3" id="averageShipments" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $driverLink; ?></div>
                                    <div class="col-md-6 pb-3" id="averageBackhauls" style="border-bottom: 1px solid var(--separator-line-color);"><?php echo $facilityLink; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade show" id="images" role="tabpanel" aria-labelledby="images-tab">
                        <?php
                        $directory = "uploads/" . $_GET['id'];
                        $directory = "uploads/1";
                        $files = scandir($directory);
                        $index = 0;
                        foreach ($files as $file) {
                            if (strpos($file, ".png") === false || strpos($file, "_resized") === false) {
                                continue;
                            }
                            $index++;
                            $type = pathinfo($directory . '/' . $file, PATHINFO_EXTENSION);
                            $data = file_get_contents($directory . '/' . $file);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            echo '<a href="uploads/' . $_GET["id"] . '/' . $index . '.png" style="padding-right: 20px;" target="_blank"><img src="' . $base64 . '" width="200" height="200" ></a>';
                        }
                        ?>
                    </div>

                    <div class="tab-pane fade show" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="col-md-12 d-flex align-items-center justify-content-center">
                            <textarea id="notes" cols="100" rows="10"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
} else {
    include 'partial/login-form.php';
} ?>

    <?php include '../footer.php'; ?>