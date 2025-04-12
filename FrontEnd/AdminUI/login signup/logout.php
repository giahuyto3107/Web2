<?php
session_start();
session_destroy();
header("Location: http://localhost/Web2/FrontEnd/AdminUI/index.php");
exit();