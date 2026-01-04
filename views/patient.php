<?php
require "../app/config/database.php";
require "../app/core/auth.php";
require "../app/models/Doctor.php";
require "../app/models/Booking.php";

// Get patient info
$q = $conn->prepare("SELECT id, name, phone FROM patients WHERE user_id=?");
$q->execute([$_SESSION['user_id']]);
$patient = $q->fetch(PDO::FETCH_ASSOC);

$doctorModel  = new Doctor($conn);
$bookingModel = new Booking($conn);

$doctors = $doctorModel->all();
$bookedDoctors = array_column($bookingModel->myBookings($patient['id']), 'id');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../public/assets/css/patient_dashboard.css">
</head>
<body>

<nav>
    <div class="nav-left">Patient View</div>
    <div class="nav-right">
        <a href="./patient_dashboard.php">Doctors</a>
        <a href="./my_bookings.php">My Bookings</a>
                <a href="./medicines.php">Medicines</a>


        <div class="dropdown">
            <span><?= htmlspecialchars($patient['name']) ?> ▼</span>
            <div class="dropdown-content">
                <a href="../public/logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Available Doctors</h2>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search doctors by expertise or description...">
    </div>

    <div class="cards" id="doctorCards">
        <?php foreach ($doctors as $d): ?>
        <div class="card" data-expertise="<?= htmlspecialchars(strtolower($d['degree'] . ' ' . $d['description'])) ?>">
            <h3><?= htmlspecialchars($d['name']) ?></h3>
            <p><strong>Phone:</strong> <?= htmlspecialchars($d['phone']) ?></p>
            <p><strong>Degree/Expertise:</strong> <?= htmlspecialchars($d['degree']) ?></p>
            <p><strong>BMDC:</strong> <?= htmlspecialchars($d['bmdc'] ?? 'N/A') ?></p>
            <p><strong>Chamber:</strong> <?= htmlspecialchars($d['chamber'] ?? 'N/A') ?></p>
            <p><strong>Available Days:</strong> <?= htmlspecialchars($d['available_days']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($d['description'] ?? '') ?></p>

            <button
                class="book"
                onclick="bookDoctor(<?= (int)$d['id'] ?>)"
                <?= in_array($d['id'], $bookedDoctors) ? 'disabled' : '' ?>
            >Book</button>

            <button
                class="unbook"
                onclick="unbookDoctor(<?= (int)$d['id'] ?>)"
                <?= in_array($d['id'], $bookedDoctors) ? '' : 'disabled' ?>
            >Unbook</button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function bookDoctor(id){
    fetch("../public/book.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ doctor: id })
    }).then(r => r.json()).then(() => location.reload());
}

function unbookDoctor(id){
    fetch("../public/unbook.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ doctor: id })
    }).then(() => location.reload());
}

// Search filter
document.getElementById('searchInput').addEventListener('input', function(){
    let val = this.value.toLowerCase();
    document.querySelectorAll('.card').forEach(card => {
        let expertise = card.dataset.expertise;
        card.style.display = expertise.includes(val) ? '' : 'none';
    });
});
</script>

</body>
</html>
