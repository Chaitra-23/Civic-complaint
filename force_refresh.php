<?php
// Redirect to index.php with a timestamp parameter to force a refresh
header("Location: index.php?t=" . time());
exit;
?>