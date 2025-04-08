<?php
session_start();
session_destroy();
header("Location: http://localhost/Web2/FrontEnd/PublicUI/Trangchu/index.php?page=home");
exit();