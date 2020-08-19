<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://nabillemsieh.com
 * @since      1.0.0
 *
 * @package    WP_Smart_Image_Resize
 * @subpackage WP_Smart_Image_Resize/templates
 */


$activeTab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

?>
<div class="wrap">
    <?php /* block-p */
/* #block-p */ ?>
    <?php /* block-f */ ?>
    <h1>Smart Image Resize for WooCommerce</h1>
    <?php /* #block-f */ ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=wp-smart-image-resize&tab=general"
           class="nav-tab <?php echo $activeTab === 'general' ? 'nav-tab-active' : '' ?>">General Settings</a>
        <a href="?page=wp-smart-image-resize&tab=regenerate_thumbnails"
           class="nav-tab <?php echo $activeTab === 'regenerate_thumbnails' ? 'nav-tab-active' : '' ?>">Regenerate
            Thumbnails</a>
    </h2>

    <?php if ( $activeTab === 'general' ): ?>
        <form method="post" action="options.php">
            <?php
            settings_fields( WP_SIR_NAME );
            do_settings_sections( WP_SIR_NAME );
            submit_button();
            ?>
        </form>

    <?php else: ?>
        <div class="wp-sir-regenerate-thumbnails" style="padding:10px">
            <p style="margin-bottom:5px">Follow these steps to regenerate your shop thumbnails to match your
                settings.</p>
            <ol>
                <?php if ( ! wp_sir_regen_thumb_active() ): ?>
                    <li>Install <a
                                href="<?php echo admin_url( 'plugin-install.php?s=Regenerate+Thumbnails&tab=search&type=term' ) ?>">Regenerate
                            Thumbnails plugin</a>.
                    </li>
                <?php endif; ?>
                <li>Navigate to
                    <?php if ( wp_sir_regen_thumb_active() ): ?>
                    <a href="<?php echo admin_url() ?>tools.php?page=regenerate-thumbnails">Tools > Regenerate
                        Thumbnails</a>.
            <?php else: ?>
                Tools > Regenerate Thumbnails.
            <?php endif; ?>
                </li>
                <li>Uncheck <b>Skip regenerating existing correctly sized thumbnails (faster)</b> option.</b></li>
                <li>Click on <b>Regenerate Thumbnails For All N Attachments</b> button.</li>

            </ol>
        </div>


    <?php endif; ?>

</div>