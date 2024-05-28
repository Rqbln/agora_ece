<?php
session_start();
session_unset();
session_destroy();
header("Location: /agora_ece/login.php");
exit();
?>
