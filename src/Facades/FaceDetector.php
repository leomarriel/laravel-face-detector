<?php

namespace Leomarriel\FaceDetector\Facades;

use Illuminate\Support\Facades\Facade;

class FaceDetector extends Facade
{

  protected static function getFacadeAccessor()
  {
    return 'FaceDetector';
  }

}
