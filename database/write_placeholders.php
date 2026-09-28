<?php
// Tiny valid binary 1x1 placeholder
$pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
$jpgBase64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

file_put_contents(__DIR__ . '/default-avatar.png', base64_decode($pngBase64));
file_put_contents(__DIR__ . '/default-cover.jpg', base64_decode($jpgBase64));
