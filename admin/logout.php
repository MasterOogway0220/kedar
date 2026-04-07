<?php
session_start();
session_unset();
session_destroy();
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Location: index.php');
exit;
