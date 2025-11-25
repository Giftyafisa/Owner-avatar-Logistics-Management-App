<?php
// Image proxy to serve package images
if (!isset($_GET['img']) || empty($_GET['img'])) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$filename = basename($_GET['img']); // Sanitize filename
$filepath = __DIR__ . '/../admin/pages/pimages/' . $filename;

// Check if file exists
if (!file_exists($filepath)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Get file extension
$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

// Set appropriate content type
$content_types = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml'
];

$content_type = isset($content_types[$ext]) ? $content_types[$ext] : 'application/octet-stream';

// Output image
header('Content-Type: ' . $content_type);
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: public, max-age=86400'); // Cache for 1 day

readfile($filepath);
exit;
