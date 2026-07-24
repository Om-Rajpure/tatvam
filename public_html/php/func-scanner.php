<?php
/**
 * Pluggable Content Scanner
 *
 * This is a stub implementation pending client confirmation of the scanner tool (REQ-020).
 * Interface contract: takes the absolute/relative file path of the uploaded PDF,
 * returns an array: ['pass' => bool, 'report' => string]
 *
 * To integrate a real scanner:
 * 1. Replace the function body below with the real API call
 * 2. Map the API response to the same return format
 * 3. No other files need to change
 */
function scan_file($filepath) {
    // STUB — always passes until real scanner is configured
    // TODO (REQ-020): Replace with real plagiarism/content scanner API call
    // once the tool is confirmed by the client.
    return [
        'pass'   => true,
        'report' => 'Scanner not yet configured (REQ-020 pending client confirmation). All submissions pass automatically.'
    ];
}
