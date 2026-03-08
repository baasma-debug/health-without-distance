
<?php
session_start();
session_destroy(); // End the session
header("Location:../../register1.php"); // Redirect to the login page
exit();
?>