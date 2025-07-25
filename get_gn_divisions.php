<?php
require 'config.php';
header('Content-Type: application/json');

if (isset($_POST['district_id'])) {
    $district_id = intval($_POST['district_id']);
    $stmt = $conn->prepare("SELECT id, name FROM gn_divisions WHERE district_id = ?");
    $stmt->bind_param("i", $district_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $gn_divisions = [];
    while ($row = $result->fetch_assoc()) {
        $gn_divisions[] = [
            'id' => $row['id'],
            'gn_division_name' => $row['name'] // mapped name column as expected
        ];
    }

    echo json_encode($gn_divisions);
    exit;
} else {
    echo json_encode(['error' => 'District ID not provided']);
    exit;
}
?>
