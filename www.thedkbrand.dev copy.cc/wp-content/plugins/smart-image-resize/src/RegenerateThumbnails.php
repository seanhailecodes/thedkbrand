<?php

namespace WP_Smart_Image_Resize;

use ConditionalAddToCart\Core\Settings;
use WC_Regenerate_Images;
use WC_Regenerate_Images_Request;
use Exception;

class RegenerateThumbnails
{

    /**
     * The number of images to generate per chunk.
     * @var int PER_PAGE
     */
    const PER_PAGE = 10;


    public function isRegeneratable( $attachmentId )
    {
        if ( 'site-icon' === get_post_meta( $attachmentId, '_wp_attachment_context', true ) ) {
            return false;
        }

        if ( ! wp_sir_is_attached_to( $attachmentId, 'product' ) ) {
            return false;
        }

        return wp_attachment_is_image( $attachmentId );

    }


    /**
     * Send image attachments to regenerate.
     * GET /admin-ajax.php?action=wp_sir_get_image_attachments
     * @return void
     */
    public function sendImageAttachments()
    {
        $page   = isset( $_GET[ 'page' ] ) ? max( intval( $_GET[ 'page' ] ), 1 ) : 1;
        $offset = ( $page - 1 ) * SELF::PER_PAGE;
        wp_send_json_success( [
            'total_count'     => $this->countImageAttachments(),
            // TODO: resume after page was loaded using `use WC_Regenerate_Images;.
            'processed_count' => $this->countProcessedImages(),
            'next_page'       => $page + 1,
            'attachments'     => $this->getImageAttachments( $offset )
        ] );
        wp_die();
    }

    public function countProcessedImages()
    {

        return 0;
    }

    public function getProcessedImages()
    {
        return [];
    }


    /**
     * Count all image attachments.
     *
     *
     * @return int
     *
     */
    public function countImageAttachments()
    {
        global $wpdb;
        $sql = "SELECT ID, post_title as title
        FROM $wpdb->posts
        WHERE post_type = 'attachment'
        AND post_mime_type LIKE 'image/%'";

        $attachments = $wpdb->get_results( $sql, ARRAY_A );

        $attachments = array_filter( $attachments, function ( $attachment ) {
            return $this->isRegeneratable( ( $attachment[ 'ID' ] ) );
        } );

        return count( $attachments );
    }

    /**
     * Get all image attachments Ids.
     *
     * @param int $offset
     *
     * @return array
     *
     */

    public function getImageAttachments( $offset )
    {
        global $wpdb;

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            ini_set( 'display_errors', '1' );
        }

        $sql = "SELECT ID, post_title as title
        FROM $wpdb->posts
        WHERE post_type = 'attachment'
        AND post_mime_type LIKE 'image/%'
        ORDER BY ID LIMIT %d OFFSET %d";

        $attachments = $wpdb->get_results( $wpdb->prepare( $sql, self::PER_PAGE, $offset ), ARRAY_A );

        $filtered_attachments = [];
        foreach ( $attachments as $attachment ) {
            if ( $this->isRegeneratable( ( $attachment[ 'ID' ] ) ) ) {
                $filtered_attachments[] = $attachment;
            }
        }

        return $filtered_attachments;

    }

    /**
     * Regenerate thumbnails.
     * POST /admin-ajax.php?action=wp_sir_regenerate_thumbnails
     */
    public function regenerate()
    {

        try {
            check_ajax_referer( 'wp-sir-ajax', 'nonce' );
            $attachmentId = intval( $_POST[ 'attachment_id' ] );
            $attachment   = get_post( $attachmentId );
            $newMetadata  = wp_generate_attachment_metadata( $attachmentId, get_attached_file( $attachmentId ) );

            wp_update_attachment_metadata( $attachmentId, $newMetadata );

            wp_send_json_success( [ 'title' => $attachment->post_title ] );

            wp_die();
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
            wp_die();
        }

    }

    public function completed()
    {
        $this->notifyUponCompletion();
    }

    public function notifyUponCompletion()
    {

        if ( Settings::get( 'notify_after_regenerate' ) ) {
            @mail( wp_get_current_user()->email, 'Thumbnails regenerated!', 'Thumbnails regenerated!' );
        }

    }

    public function runInBackground()
    {
        try {
            WC_Regenerate_Images::queue_image_regeneration();
            wp_send_json_success( [ 'message' => 'Thumbnail regeneration is running in the background. Depending on the amount of images in your store this may take a while.' ] );
            wp_die();
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
            wp_die();
        }
    }

    public static function isRunningInBackground()
    {
        return ! ( new WC_Regenerate_Images_Request() )->is_running();
    }

    public function cancelRunInBackground()
    {
        try {
            WC_Regenerate_Images::dismiss_regenerating_notice();
            wp_send_json_success( [ 'message' => 'Thumbnail regeneration has been cancelled.' ] );
            wp_die();
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
            wp_die();
        }
    }
}
