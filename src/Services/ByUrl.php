<?php

namespace Leomarriel\FaceDetector\Services;

use Leomarriel\FaceDetector\Abstracts\AbstractFile;

class ByUrl extends AbstractFile
{
    /**
     * Return the valid url
     *
     * @param string $makeFile
     *
     * @return bool
     */
    protected function isValid($makeFile){
        $headers   = get_headers($makeFile, true);
        $response  = ($headers && isset($headers[0])) ? $headers[0] : null;
        return (strpos($response, '200') === false) ? false : true;
    }

    /**
     * Return the file extension as extracted from string file
     *
     * @param string $makeFile
     *
     * @return bool
     */
    public function getExtension($makeFile){
        $mimeType = image_type_to_mime_type(exif_imagetype($makeFile));
        return substr($mimeType, strrpos($mimeType, '/') + 1);
    }
}