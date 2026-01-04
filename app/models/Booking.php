<?php
class Booking {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    // Check if patient already has an active booking with this doctor
    public function hasBooking($doctor_id, $patient_id){
        $q = $this->conn->prepare(
            "SELECT * FROM bookings 
             WHERE doctor_id=? AND patient_id=? AND status='booked'"
        );
        $q->execute([$doctor_id, $patient_id]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    // Book a doctor
    public function book($doctor_id, $patient_id){
        $existing = $this->hasBooking($doctor_id, $patient_id);
        if ($existing) {
            return false; // already booked this doctor
        }

        // Check if there is a cancelled booking for this doctor
        $q = $this->conn->prepare(
            "SELECT id FROM bookings 
             WHERE doctor_id=? AND patient_id=? AND status='cancelled'"
        );
        $q->execute([$doctor_id, $patient_id]);
        $cancelled = $q->fetch(PDO::FETCH_ASSOC);

        if ($cancelled) {
            // Reactivate cancelled booking
            $q = $this->conn->prepare(
                "UPDATE bookings SET status='booked' WHERE id=?"
            );
            return $q->execute([$cancelled['id']]);
        }

        // First-time booking
        $q = $this->conn->prepare(
            "INSERT INTO bookings (doctor_id, patient_id, status) 
             VALUES (?, ?, 'booked')"
        );
        return $q->execute([$doctor_id, $patient_id]);
    }

    // Unbook a specific doctor
    public function unbook($doctor_id, $patient_id){
        $q = $this->conn->prepare(
            "UPDATE bookings 
             SET status='cancelled' 
             WHERE doctor_id=? AND patient_id=? AND status='booked'"
        );
        return $q->execute([$doctor_id, $patient_id]);
    }

    // Get all doctors booked by a patient
    public function myBookings($patient_id){
        $q = $this->conn->prepare(
            "SELECT d.*, b.status
             FROM bookings b
             JOIN doctors d ON b.doctor_id = d.id
             WHERE b.patient_id=? AND b.status='booked'"
        );
        $q->execute([$patient_id]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all patients who booked this doctor
    public function forDoctor($user_id){
        $q = $this->conn->prepare(
            "SELECT p.*
             FROM bookings b
             JOIN patients p ON b.patient_id=p.id
             JOIN doctors d ON b.doctor_id=d.id
             WHERE d.user_id=? AND b.status='booked'"
        );
        $q->execute([$user_id]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
