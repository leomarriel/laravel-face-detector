<?php

namespace Leomarriel\FaceDetector\Services;

use Leomarriel\FaceDetector\Abstracts\AbstractFile;

class ByFile extends AbstractFile
{
    /**
     * Return the valid file
     *
     * @param string $makeFile
     *
     * @return bool
     */
    protected function isValid($makeFile){
        return (is_file($makeFile)) ? true : false;
    }

    /**
     * Return the file extension as extracted from the origin file name
     *
     * @param string $makeFile
     *
     * @return string
     */
    protected function getExtension($makeFile){
        $mimeType = image_type_to_mime_type(exif_imagetype($makeFile));
        return substr($mimeType, strrpos($mimeType, '/') + 1);
    }
}