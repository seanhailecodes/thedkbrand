<?php

/**
 * Uninstall plugin.
 * Remove related configuration and data.
 */
defined('WP_UNINSTALL_PLUGIN') || exit;

//  Delete WebP thumbnails.
$attachments = get_posts([
    'post_type' => 'attachment',
    'posts_per_page' => -1,
]);

foreach ($attachments as $attachment) {

    if (!wp_attachment_is_image($attachment->ID)) {
        continue;
    }

    $metadata = wp_get_attachment_metadata($attachment->ID);

    if(empty($metadata)){
        continue;
    }

    $origin_file = pathinfo($metadata['file']);

    $uploadDir = wp_upload_dir()['basedir'];
    foreach ($metadata['sizes'] as $size) {
        $filename = pathinfo($size['file'], PATHINFO_FILENAME);
        @wp_delete_file($uploadDir. '/' . "{$origin_file['dirname']}/$filename.webp");
    }

    // Delete original WebP image.
    @wp_delete_file($uploadDir . '/' . "{$origin_file['dirname']}/{$origin_file['filename']}.webp");

    // Delete settings.
}
delete_option('wp_sir_settings');

@wp_cache_flush();

