<?php
namespace PostGenerator;
class Logger {
    public static function logToFile($data) {
        $log_file = plugin_dir_path(__DIR__, 1) . 'debug.log';
        $timestamp = date("Y-m-d H:i:s");

        if (!is_string($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $message = "{$timestamp} | {$data}\n";
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}