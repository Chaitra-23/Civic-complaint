<?php
// Set headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to index.php with a timestamp parameter to force a refresh
header("Location: index.php?t=" . time() . "&debug=1");
exit;
?>