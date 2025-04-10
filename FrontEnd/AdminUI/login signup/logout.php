<?php
session_start();
session_destroy();
header("Location: http://localhost/Web2/FrontEnd/AdminUI/login signup/login.php");
exit();