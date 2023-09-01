<?php

namespace Leomarriel\FaceDetector\Abstracts;

use Leomarriel\FaceDetector\Contracts\FaceDetectorContract;
use Leomarriel\FaceDetector\Exceptions\NotFoundFaceException;

abstract class AbstractFaceDetector implements FaceDetectorContract
{   
    /** @var string */
    private $reduced_canvas;

    /** @var string */
    protected $face;

    /**
    * Handle the run detector
    *
    * @param string $target
    *
    * @return array|false
    */
    public function detector($target)
    {    
        $im_width = imagesx($target);
        $im_height = imagesy($target);

        $diff_width = 320 - $im_width;
        $diff_height = 240 - $im_height;
        if ($diff_width > $diff_height) {
            $ratio = $im_width / 320;
        } else {
            $ratio = $im_height / 240;
        }

        if ($ratio != 0) {
            $this->reduced_canvas = imagecreatetruecolor($im_width / $ratio, $im_height / $ratio);

            imagecopyresampled(
                $this->reduced_canvas,
                $target,
                0,
                0,
                0,
                0,
                $im_width / $ratio,
                $im_height / $ratio,
                $im_width,
                $im_height
            );

            $stats = $this->getImgStats($this->reduced_canvas);

            $this->face = $this->doDetectGreedyBigToSmall(
                $stats['ii'],
                $stats['ii2'],
                $stats['width'],
                $stats['height']
            );

            if ($this->face['w'] ?? 0 > 0) {
                $this->face['x'] *= $ratio;
                $this->face['y'] *= $ratio;
                $this->face['w'] *= $ratio;
            }
        } else {
            $stats = $this->getImgStats($target);

            $this->face = $this->doDetectGreedyBigToSmall(
                $stats['ii'],
                $stats['ii2'],
                $stats['width'],
                $stats['height']
            );
        }

        if($this->face && $this->face['w'] ?? 0 > 0) {
            return $this->face; 
        }

        if($this->settings['noFoundFaceException']) {
            throw new NotFoundFaceException(
                "No faces detected in this file."
            );
        }

        return false;
    }
    
    abstract protected function getImgStats($tmpFile);

    abstract protected function computeII($tmpFile, $image_width, $image_height);

    abstract protected function doDetectGreedyBigToSmall($ii, $ii2, $width, $height);

    abstract protected function detectOnSubImage($x, $y, $scale, $ii, $ii2, $w, $iiw, $inv_area);
    
}