<?php
require "../app/config/database.php";
require "../app/models/User.php";
require "../app/models/Patient.php";

$error = '';

if($_POST){
    $user = new User($conn);

    if(!$user->create($_POST['email'], $_POST['password'], 'patient')){
        $error = "Email already exists!";
    } else {
        $patient = new Patient($conn);
        $patient->create($conn->lastInsertId(), [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'health_issues' => $_POST['health_issues'] ?? [],
            'emergency' => $_POST['emergency'],
            'nid' => $_POST['nid']
        ]);
        header("Location: login.php");
        exit;
    }
}
?>

<h2>Patient Signup</h2>

<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
Name: <input type="text" name="name" required><br>
Phone: <input type="text" name="phone" required><br>
Address: <input type="text" name="address" required><br>
Health Issues:<br>
<input type="checkbox" name="health_issues[]" value="diabetes">Diabetes
<input type="checkbox" name="health_issues[]" value="bp">BP
<input type="checkbox" name="health_issues[]" value="allergy">Allergy<br>
Emergency Contact: <input type="text" name="emergency" required><br>
NID: <input type="text" name="nid" required><br>
Email: <input type="email" name="email" required><br>
Password: <input type="password" name="password" required><br>
<button>Signup</button>
</form>

<a href="login.php">Back to Login</a>
