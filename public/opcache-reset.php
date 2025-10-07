<?php
// Simple OPcache reset script for XAMPP
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache has been reset successfully!";
} else {
    echo "OPcache is not enabled.";
}
