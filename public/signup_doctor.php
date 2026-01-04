<?php
session_start();
require "../app/config/database.php";
require "../app/models/User.php";
require "../app/models/Doctor.php";

$error = '';

if($_POST){
    $user = new User($conn);

    // Try to create user first
    if(!$user->create($_POST['email'], $_POST['password'], 'doctor')){
        $error = "Email already exists!";
    } else {
        // Create doctor profile
        $doctor = new Doctor($conn);
        $doctor->create($conn->lastInsertId(), [
            'name'=>$_POST['name'],
            'degree'=>$_POST['degree'], // array from checkboxes
            'phone'=>$_POST['phone'],
            'bmdc'=>$_POST['bmdc'],
            'nid'=>$_POST['nid'],
            'address'=>$_POST['address'],
            'chamber'=>$_POST['chamber'],
            'days'=>$_POST['days'], // array from checkboxes
            'desc'=>$_POST['desc']
        ]);
        header("Location: login.php");
        exit;
    }
}
?>

<h2>Doctor Signup</h2>

<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
Name: <input type="text" name="name" required><br>
Degree: 
<input type="checkbox" name="degree[]" value="MBBS">MBBS
<input type="checkbox" name="degree[]" value="MPhil">MPhil
<input type="checkbox" name="degree[]" value="FCPS">FCPS<br>
Email: <input type="email" name="email" required><br>
Phone: <input type="text" name="phone" required><br>
BMDC ID: <input type="text" name="bmdc" required><br>
NID: <input type="text" name="nid" required><br>
Address: <input type="text" name="address" required><br>
Chamber Location: <input type="text" name="chamber" required><br>
Days:
<input type="checkbox" name="days[]" value="Sun">Sun
<input type="checkbox" name="days[]" value="Mon">Mon
<input type="checkbox" name="days[]" value="Tue">Tue
<input type="checkbox" name="days[]" value="Wed">Wed
<input type="checkbox" name="days[]" value="Thu">Thu
<input type="checkbox" name="days[]" value="Fri">Fri
<input type="checkbox" name="days[]" value="Sat">Sat<br>
Description:<br>
<textarea name="desc"></textarea><br>
Password: <input type="password" name="password" required><br>
<button>Signup</button>
</form>
<a href="login.php">Back to Login</a>
