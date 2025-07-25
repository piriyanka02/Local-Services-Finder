<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

$provider_id = $_SESSION['id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>My Bookings</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Service Type</th>
                <th>User Name</th>
                <th>Preferred Date</th>
                <th>Preferred Time</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT b.*, u.name as user_name 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                WHERE b.provider_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $provider_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $i = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookingId = $row['id'];
                // Lowercase status for select option matching
                $status_lower = strtolower($row['status']);
                echo "<tr>
                        <td>{$i}</td>
                        <td>".htmlspecialchars($row['service_type'])."</td>
                        <td>".htmlspecialchars($row['user_name'])."</td>
                        <td>".htmlspecialchars($row['preferred_date'])."</td>
                        <td>".htmlspecialchars($row['preferred_time'])."</td>
                        <td>".htmlspecialchars($row['message'])."</td>
                        <td>
                          <select class='form-select status-dropdown' data-booking-id='{$bookingId}'>
                            <option value='pending' ".($status_lower === 'pending' ? 'selected' : '').">Pending</option>
                            <option value='approved' ".($status_lower === 'approved' ? 'selected' : '').">Approved</option>
                            <option value='rejected' ".($status_lower === 'rejected' ? 'selected' : '').">Rejected</option>
                          </select>
                        </td>
                        <td>
                          <button class='btn btn-primary btn-update' data-booking-id='{$bookingId}'>Update</button>
                        </td>
                      </tr>";
                $i++;
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No bookings found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function(){
    $('.btn-update').click(function(){
        const bookingId = $(this).data('booking-id');
        const status = $(this).closest('tr').find('.status-dropdown').val();

        $.ajax({
            url: 'update_booking_status.php',  // call separate AJAX handler
            method: 'POST',
            data: {booking_id: bookingId, status: status},
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert('Booking status updated successfully!');
                } else {
                    alert('Error: ' + (response.message || 'Update failed'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('AJAX error occurred: ' + error);
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
