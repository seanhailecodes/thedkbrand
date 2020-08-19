<?php

namespace WP_Smart_Image_Resize;

class Helpers
{


    /**
     * Determine if a given $extension is JPG format.
     *
     * @param string $extension
     *
     * @return bool@
     */
    public static function isJPG($extension)
    {
        return $extension === 'jpg' || $extension === 'jpeg';
    }

    /**
     * Replace a given file extension with an another given extension.
     *
     * @param string $file File name/base path.
     * @param string|null $extension
     *
     * @return string
     */
    public static function replaceFileExtension($file, $extension = null)
    {

        $file            = strval($file);
        $strippedExtension = substr($file, 0, strrpos($file, '.'));

        if (empty(trim(strval($extension)))) {
            return $strippedExtension;
        }

        return $strippedExtension . '.' . $extension;
    }
}