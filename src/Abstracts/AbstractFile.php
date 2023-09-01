<?php

namespace Leomarriel\FaceDetector\Abstracts;

use Leomarriel\FaceDetector\Contracts\FileContract;
use Leomarriel\FaceDetector\Exceptions\NotValidFileException;
use Leomarriel\FaceDetector\Exceptions\NotValidExtensionException;

abstract class AbstractFile implements FileContract
{   
    /**
    * Handle the make file
    *
    * @param string $target
    * @param array $settings
    *
    * @return array|false
    */
    public function make($target, $settings)
    {        
        if ($this->makeValidate($target, $settings)) {
            switch($this->getExtension($target)) {
                case "jpeg":
                    return imagecreatefromjpeg($target);
                break;
                case "png":
                    return imagecreatefrompng($target);
                break;
                default:
                throw new NotValidFileException(
                    "This File ({$target}) is not a valid."
                );
                break;
            }
        }
        return false;
    }

    /**
     * Validate the make file for extension & size
     *
     * @return bool
     */
    protected function makeValidate($makeFile, $settings){
        if (!$this->isValid($makeFile)) {
            throw new NotValidFileException(
                "This File ({$makeFile}) is not a valid."
            );
        }

        if (!in_array($this->getExtension($makeFile), $settings['allowedExtensions'])) {
            throw new NotValidExtensionException(
                "Extension {$this->getExtension($makeFile)} is not allowed"
            );
        }

        return true;
    }

    protected function isValid($makeFile){
        return true;
    }
    
    /**
     * Return the file extension
     *
     * @param string $makeFile
     *
     * @return string
     */
    abstract protected function getExtension($makeFile);
    
}