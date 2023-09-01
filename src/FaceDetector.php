<?php

namespace Leomarriel\FaceDetector;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

use Leomarriel\FaceDetector\Services\ByFile;
use Leomarriel\FaceDetector\Services\ByUrl;
use Leomarriel\FaceDetector\Services\Detector;
use Leomarriel\FaceDetector\Exceptions\NotValidExtensionException;

class FaceDetector
{
    /**
    * Default settings
    *
    */
    protected $settings;

    /** @var string  */
    protected $file;

    /** @var string */
    protected $face;

    /** @var array */
    protected $padding;

    public function __construct()
    {
        $this->initializeDefaults();
    }

    /**
    * Initialize default settings
    *
    * @return void
    */
    protected function initializeDefaults()
    {
        $this->settings = config('facedetector');
        $this->padding = config('facedetector.padding');
    }

    /**
     * Face Detection from File Path
     */
    public function loadFile($file)
    {
        $make = new ByFile();
        $this->file = $make->make($file, $this->settings);

        $run = new Detector();
        $this->face = $run->detector($this->file);

        return $this;
    }

    /**
     * Face Detection from File Url
     */
    public function loadUrl($file)
    {
        $make = new ByUrl();
        $this->file = $make->make($file, $this->settings);

        $run = new Detector();
        $this->face = $run->detector($this->file);

        return $this;
    }

    /**
     * Did you find any faces in file?
     * 
     * @return bool
     */
    public function found() : bool
    {
        return ($this->face['w'] ?? 0 > 0) ? true : false;
    }

    /**
     * Return an array with the position of the found face
     * 
     * @return array
     */
    public function face() : array
    {
        return $this->face;
    }

    /**
     * Return found face with a tag
     */
    public function preview()
    {
        $color = imagecolorallocate($this->file, 255, 0, 0);

        imagerectangle(
            $this->file,
            $this->face['x'] - ($this->padding["width"] / 2),
            $this->face['y'] - ($this->padding["height"] / 2),
            $this->face['x'] + $this->face['w'] + $this->padding["width"],
            $this->face['y'] + $this->face['w'] + $this->padding["height"],
            $color
        );

        header('Content-type: image/jpeg');
        imagejpeg($this->file);
        imagedestroy($this->file);
    }

    /**
     * Return the found face in json format
     * 
     * @return string
     */    
    public function json() : string
    {
        return json_encode($this->face);
    }

    /**
     * Create a unique name for the file
     * 
     * @return string
     */ 
    protected function getUniqueName(string $ext = 'jpg') : string
    {
        $uniqueString = uniqid(rand(), true)."_".getmypid()."_".gethostname()."_".time();
        return md5($uniqueString).".".$ext;
    }

    /**
     * Return a response with found face cropped in JPG to show in browser
     */
    public function stream(): Response
    {
        $output = $this->output();
        return new Response($output, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' =>  'inline; filename="' . $this->getUniqueName() . '"',
        ]);
    }

    /**
     * Return a string file with the found face
     * 
     * @return string
     */
    public function output(string $ext = 'jpg') : string
    {
        if (!in_array($ext, $this->settings['allowedExtensions'])) {
            throw new NotValidExtensionException(
                "Extension {$ext} is not allowed for output"
            );
        }

        $canvas = imagecreatetruecolor($this->face['w'], $this->face['w']);
        imagecopy($canvas, $this->file, 0, 0, 
        $this->face['x'] - ($this->padding["width"] / 2), 
        $this->face['y'] - ($this->padding["height"] / 2), 
        $this->face['w'] + $this->padding["width"], 
        $this->face['w'] + $this->padding["height"]);  

        ob_start();
        ($ext == 'jpg' || $ext == 'jpeg' ) ? imagejpeg($canvas) : imagepng($canvas);
        imagedestroy($canvas);
        
        return ob_get_clean();
    }

    /**
     * Physically save the face found
     *
     * @param string $path, by default new folder "facedetector" in storage
     * @param string $name, by default create a unique name for the file with $this->getUniqueName()
     *
     * @return array
     */
    public function save(string $path = null, string $filename = null)
    {
        $storage = Storage::disk($this->settings['disk']);
        $filename = (is_null($filename)) ? $this->getUniqueName() : $filename;
        $path = (is_null($path)) ? 'facedetector' : $path;
        $ext = substr($filename, strrpos($filename, '.') + 1);

        $pathinfo = pathinfo($path."/".$filename);
        $pathFile = $pathinfo["dirname"].'/'.$filename;
        
        $storage->put($pathFile, $this->output($ext));

        return [
            'filename' => $filename,
            'size' => $storage->size($pathFile),
            'path' => $storage->path($pathFile),
            'url' => $storage->url($pathFile),
        ];
    }

}
