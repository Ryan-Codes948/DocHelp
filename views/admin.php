<?php
require "../app/config/database.php";

// Handle delete requests
if(isset($_GET['delete_user'])){
    $id = (int)$_GET['delete_user'];
    $conn->prepare("DELETE FROM patients WHERE user_id=?")->execute([$id]);
    $conn->prepare("DELETE FROM doctors WHERE user_id=?")->execute([$id]);
    $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Fetch patients and doctors
$patients = $conn->query("
    SELECT p.*, u.email 
    FROM patients p 
    JOIN users u ON p.user_id = u.id
")->fetchAll(PDO::FETCH_ASSOC);

$doctors = $conn->query("
    SELECT d.*, u.email 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id
")->fetchAll(PDO::FETCH_ASSOC);

// Counts
$pCount = count($patients);
$dCount = count($doctors);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../public/assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="counts">
        <div class="count-box">Total Patients: <?= $pCount ?></div>
        <div class="count-box">Total Doctors: <?= $dCount ?></div>
    </div>

    <div class="toggle-buttons">
        <button id="showPatients">Patients</button>
        <button id="showDoctors">Doctors</button>
    </div>

    <span>Search</span>
<br>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by name...">
    </div>

    <div id="patientsSection" class="cards-section">
        <?php foreach($patients as $p): ?>
        <div class="card">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($p['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($p['phone'] ?? '-') ?></p>
            <p><strong>Health Issues:</strong> <?= htmlspecialchars($p['health_issues'] ?? '-') ?></p>
            <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($p['emergency'] ?? '-') ?></p>
            <p><strong>NID:</strong> <?= htmlspecialchars($p['nid'] ?? '-') ?></p>
            <a href="?delete_user=<?= $p['user_id'] ?>" class="delete-btn" onclick="return confirm('Delete this patient?')">Delete</a>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="doctorsSection" class="cards-section" style="display:none;">
        <?php foreach($doctors as $d): ?>
        <div class="card">
            <h3><?= htmlspecialchars($d['name']) ?></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($d['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($d['phone'] ?? '-') ?></p>
            <p><strong>Degree:</strong> <?= htmlspecialchars($d['degree'] ?? '-') ?></p>
            <p><strong>BMDC:</strong> <?= htmlspecialchars($d['bmdc'] ?? '-') ?></p>
            <p><strong>NID:</strong> <?= htmlspecialchars($d['nid'] ?? '-') ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($d['address'] ?? '-') ?></p>
            <p><strong>Chamber:</strong> <?= htmlspecialchars($d['chamber'] ?? '-') ?></p>
            <p><strong>Available Days:</strong> <?= htmlspecialchars($d['available_days'] ?? '-') ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($d['description'] ?? '-') ?></p>
            <a href="?delete_user=<?= $d['user_id'] ?>" class="delete-btn" onclick="return confirm('Delete this doctor?')">Delete</a>
        </div>
        <?php endforeach; ?>
    </div>

    <a href="../public/logout.php" class="logout-btn">Logout</a>
</div>

<script>
const patientsSection = document.getElementById('patientsSection');
const doctorsSection = document.getElementById('doctorsSection');
const showPatientsBtn = document.getElementById('showPatients');
const showDoctorsBtn = document.getElementById('showDoctors');
const searchInput = document.getElementById('searchInput');

// Toggle sections
showPatientsBtn.addEventListener('click', () => {
    patientsSection.style.display = 'flex';
    doctorsSection.style.display = 'none';
    searchInput.value = '';
    filterCards();
});

showDoctorsBtn.addEventListener('click', () => {
    doctorsSection.style.display = 'flex';
    patientsSection.style.display = 'none';
    searchInput.value = '';
    filterCards();
});

// Search function
searchInput.addEventListener('input', filterCards);

function filterCards() {
    const val = searchInput.value.toLowerCase();
    const activeSection = patientsSection.style.display !== 'none' ? patientsSection : doctorsSection;
    const cards = activeSection.querySelectorAll('.card');
    cards.forEach(card => {
        card.style.display = card.querySelector('h3').textContent.toLowerCase().includes(val) ? 'block' : 'none';
    });
}
</script>
</body>
</html>
