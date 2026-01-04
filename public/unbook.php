<?php
require "../app/config/database.php";
require "../app/models/Booking.php";
require "../app/core/auth.php";

$data = json_decode(file_get_contents("php://input"), true);
$doctor_id = $data['doctor'] ?? null;

$q = $conn->prepare("SELECT id FROM patients WHERE user_id=?");
$q->execute([$_SESSION['user_id']]);
$patient = $q->fetch(PDO::FETCH_ASSOC);

$booking = new Booking($conn);
$booking->unbook($doctor_id, $patient['id']);

echo json_encode(['success'=>true]);
