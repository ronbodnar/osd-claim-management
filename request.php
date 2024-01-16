<?php

require 'Database.class.php';

$database = new Database();

$action = $_GET['action'];

if (!isset($action)) {
    die('Invalid action');
}

if (strpos($action, 'list') !== false) {
    $output = array();
    $claims = $database->getAllClaims();

    foreach ($claims as $claim) {
        $date = new DateTimeImmutable($claim['date']);

        $claim['date'] = $date->format('m/d/Y');

        // Display link to Facility page
        $claim['name'] = '<a href="facility?id=' . $claim['id'][3] . '" class="text-mron">' . $claim['name'] . ' / ' . $claim['city'] . '</a>';

        // Display Driver name based on ID
        $driverId = $claim['driver_id'];
        $driverName = $database->getUserData($claim['driver_id'])->getFullName();
        $claim['driver_id'] = '<a href="driver?id=' . $driverId . '" class="text-mron">' . $driverName . '</a>';

        // Format whether product was received by warehouse
        $claim['received'] = ($claim['received'] == 0 ? '<span style="color: red;"><i class="bi bi-x-lg"></i></span>' : '<span style="color: green;"><i class="bi bi-check-lg"></i></span>');
        //$claim['received'] = ($claim['received'] == 0 ? 'No' : 'Yes');

        // Format status cell with colored pill badges
        $statusColors = array(
            1 => 'bg-danger',
            2 => 'bg-info',
            3 => 'bg-warning',
            4 => 'bg-success'
        );

        $statusId = $claim['status_id'];
        $statusText = '<span class="badge rounded-pill ' . $statusColors[$statusId] . ' w-100">' . ucfirst($claim['status']) . '</span>';

        $claim['status'] = $statusText;

        // Format claim type
        $claim['type'] = ucfirst($claim['type']);

        $claim['id'] = $claim['id'][0];

        array_push($output, $claim);
    }

    echo json_encode(array('draw' => 1, 'recordsTotal' => count($output), 'recordsFiltered' => count($output), 'data' => $output));
} else if (strpos($action, 'count-type') !== false) {
    $overage = $database->getOSDClaimCountByTypeId(1);
    $shortage = $database->getOSDClaimCountByTypeId(2);
    $damage = $database->getOSDClaimCountByTypeId(3);
    echo json_encode(array('overage' => $overage, 'shortage' => $shortage, 'damage' => $damage, 'total' => ($overage + $shortage + $damage)));
}