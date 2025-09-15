<?php
// Legacy path retained only for backward compatible links.
// Redirect to new Laravel register route preserving vt param.
$qs = $_SERVER['QUERY_STRING'] ? ('?'.$_SERVER['QUERY_STRING']) : '';
header('Location: /register'.$qs, true, 302);
exit; 
