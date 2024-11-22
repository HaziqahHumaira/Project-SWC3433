<?php
session_start();
session_destroy(); // Destroy session
header("Location: Project.html"); // Redirect to homepage
exit();
?>
