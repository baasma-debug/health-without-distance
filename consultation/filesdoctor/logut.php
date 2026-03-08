
<?php
session_start();
session_destroy(); // End the session
header("Location:../../login1.php"); // Redirect to the login page
exit();
?>