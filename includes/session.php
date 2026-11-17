<?php
// comment
// Unified session initializer
if (session_status() === PHP_SESSION_NONE) {
    $sessDir = __DIR__ . '/../sessions';
    if (!is_dir($sessDir)) {
        @mkdir($sessDir, 0777, true);
    }
    if (is_dir($sessDir) && is_writable($sessDir)) {
        @session_save_path($sessDir);
    }
    @session_start();
}
