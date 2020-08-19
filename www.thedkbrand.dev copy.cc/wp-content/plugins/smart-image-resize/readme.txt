=== Smart Image Resize - Make WooCommerce Images the Same Size ===
Contributors: nlemsieh
Donate link: https://paypal.me/nlemsieh
Tags: woocommerce, product image resize, square image, same image size, cut-off image, cropped image, fix image crop, square thumbnail, resize image, picture resize, uniform image, same size, category image size, image resize without cropping, image resize, resize thumbnails, resize images in WooCommerce, aspect ratio image
Requires at least: 4.0
Tested up to: 5.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires PHP: 5.4
Stable tag: 1.3.7

Make WooCommerce product images the same size and uniform without cropping. No more manual image editing and photo resizing.

== Description ==

[Smart Image Resize](https://sirplugin.com/) Makes your store look professional with product images that are all uniform and the same size without cropping.

- Zero-configuration.
- No more manual image editing and photo resizing.

### Lite Features

- Resize up to 150 images.
- Remove unwanted whitespace around image.
- Set a custom background color of the emerging area.
- Compress thumbnails for faster page load.
- Select which sizes to regenerate.

### Pro Features

- **♾ Unlimited Images:**  Unlimited images resizing.

-  **✈️ Convert to JPG format:** Reduce image file size and boost page speed.

- **🚀 Use WebP Images:** Speed up page load by reduce image file up to 90% while still providing transparency and the same quality.

- **🔒Insert watermark (coming soon):** Insert logo, name, SKU, and other info on all images, attracting new potential customers through search engines, and keep images safe from unauthorized use (especially if you sell digital products or if you want to keep the copyright safe for the images you publish online such as photos, pictures, comic strips, etc.)
- **👨‍💻 Get priority support:**
Get faster chat and email support.

[Check out Smart Image Resize PRO!](https://sirplugin.com?utm_source=wp&utm_medium=link&utm_campaign=lite_version)

### Usage

After installation, new images will be automatically resized and adjusted on upload.

If you have already uploaded product images to Media Library, follow these steps to regenerate thumbnails:

1. Install Regenerate Thumbnails plugin.
2. Navigate to Tools > Regenerate Thumbnails.
3. Uncheck Skip regenerating existing correctly sized thumbnails (faster) option.
4. Finally, click on Regenerate Thumbnails for the N Featured Images Only button.

Optionally, you can change settings to fit your needs under WooCommerce > Smart Image Resize.

To learn more, [check out the guide](https://sirplugin.com/guide.html?utm_source=wp&utm_medium=link&utm_campaign=lite_version).

== Installation ==

1. Upload `smart-image-resize` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

 _**Note**: Make sure PHP Fileinfo extension is enabled in you server._

 == Frequently Asked Questions ==

= How can I regenerate thumbnails I already added to the media library? =

To regenerate thumbnails:
1. Install <a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails plugin</a>.
2. Navigate to Tools > Regenerate Thumbnails.
3. Uncheck **Skip regenerating existing correctly sized thumbnails (faster)**
4. Finally, click on **Regenerate Thumbnails for the N Featured Images Only** button.

= I get an error when I upload an image =

Make sure PHP extension `fileinfo` is enabled.


== Screenshots ==

1. Before and after using the plugin.
2. Settings page.
3. Select sizes to generated.
4. Add custom background color of the new area.

== Changelog ==

= 1.3.7 =

 * Stability improvement.

= 1.3.6 =

 * Fix a minor issue with image parent post type detection.
 * Added a new filter `wp_sir_regeneratable_post_status` to change regeneratable product status. Default: `publish`

= 1.3.5 =

 * Regenerate thumbnails speed improvement.


= 1.3.4 =

 * Stability improvement

= 1.3.3 =

 * fixed a minor issue with settings page.

= 1.3.2 =
 * Added thumbnails regeneration steps under "Regenerate Thumbnails" tab.

= 1.3.1 =
 * Fixed a minor bug in Regenerate Thumbnails tool.

= 1.3 =
 * Added a built-in tool to regenerate thumbnails.
 * woocommerce_single size is now selected by default.
 * Stability improvement.

= 1.2.4 =
 * Fix srcset images not loaded when WebP is enabled.

= 1.2.3 =
 * Set GD driver as default.
 * Stability improvement.

= 1.2.2 =
 * Prevent black background when converting transparent PNG to JPG.
 * Fixed random issue that causes WebP images fail to load.
 * Stability improvement.

= 1.2.1 =
* Added settings page link under Installed Plugins.

= 1.2.0 =
* Added Whitespace Trimming feature.
* Various improvements. 

= 1.1.12 =

* Fixed crash when Fileinfo extension is disabled. 

= 1.1.11 =

* Added support for Jetpack. 

= 1.1.10 =

* Fixed conflict with some plugins. 

= 1.1.9 =

* Prevent dynamic resize in WooCommerce.

= 1.1.8 =

* Handle WebP not installed.

= 1.1.7 =

* Fixed mbstring polyfill conflict with WP `mb_strlen` function


= 1.1.6 =
* Added polyfill for PHP mbstring extension

= 1.1.5 =
* Force square image when height is set to auto.

= 1.1.4 =
* Fixed empty sizes list 

= 1.1.3 =
* Fixed empty sizes list 

= 1.1.2 =

* Added settings improvements
* Added processed images notice.

= 1.1.1 =

* Added fileinfo and PHP version notices
* Improved settings page experience.

= 1.1.0 =

* Introducing Smart Image Resize Pro features
* Various improvements

= 1.0.13 =

* Fixed some images not resized correctly.

= 1.0.12 =

* Minor bugfix

= 1.0.11 =

* Errors messages now are displayed in media uploader. This will help debug occured problems while resizing.

= 1.0.10 =

* The PHP Fileinfo extension is required. Now you can see notice when it isn't enabled. 

= 1.0.9 =

* Fixed bug that prevents upload of non-image files to the media library. 

= 1.0.8 =

* Skip woocommerce_single resize

= 1.0.7 =

* Stability improvement

= 1.0.6 =

* Bugfix


= 1.0.5 =

* Bugfix

= 1.0.4 =

* Removed deprecated option.

= 1.0.3 =

* Small images resize improvement.

= 1.0.2 =

Improve stability

= 1.0.1 =

- Add ability to add custom color in settings.
- Fixbug for some PHP versions.

= 1.0.0 =

* Public Release

 == Upgrade Notice ==
 
= 1.3.1 =

* A new built-in tool to regenerate thumbnails.
