# Laravel Face Detector
> A Laravel package to handle face detection in an image

## Current Features

- reading a local or remote file
- external url support
- multiple ways available output the found face
- file validation

## Installation

```
composer require leomarriel/laravel-face-detector
```

## Configuration

### Laravel without auto-discovery:

If you don't use auto-discovery, add the `FaceDetectorServiceProvider` to the providers array in `config/app.php`

```php
'providers' => [
  ...
  Leomarriel\FaceDetector\FaceDetectorServiceProvider::class,
  ...
],
```

Copy configuration to your project:

```
php artisan vendor:publish --tag=facedetector-config
```

By executing above command the package configuration will be published to `config/facedetector.php`

### Config file
There are two configuration options available

`config/facedetector.php` is an asociative array with the following possible keys:   
- ```disk``` (string): the storage disk for file save on command output `save()`.  
- ```noFoundFaceException``` (bool): Disable the exception when a face is not found
- ```padding``` (array): Define a padding around the found face

## Usage

**Direct use, no facades**:

You can create a new ```FaceDetector``` instance and load a local or remote file  
```php
use Leomarriel\FaceDetector\FaceDetector;

$face = new FaceDetector();
$face->loadFile('path/to/image.jpg');
$face->preview();
```

If you want to use the facade to face detection, add this to your facades in `config/app.php`:

```php
'aliases' => [
  ...
  'FaceDetector' => Leomarriel\FaceDetector\Facades\FaceDetector::class,
  ...
],
```

## Use the facade to face detection
There are currently two ways to load the file:
### From Path

```php
use FaceDetector;

$face = FaceDetector::loadFile('path/to/image.jpg')->preview();
```

### From Url

```php
use FaceDetector;

$face = FaceDetector::loadUrl('https://url/to/image.jpg')->preview();
```

## Output details
Check all available output ways

```php
use FaceDetector;

$face = FaceDetector::loadFile('path/to/image.jpg');
```
```php
$face->found(); // (bool) Return a boolean whether or not a face was found in the file
```
```php
$face->face(); // (array) Return an array with the position of the found face
```
```php
$face->preview(); // Return a preview of the face found with a tag
```
```php
$face->json(); // Return the found face in json format
```
```php
$face->stream(); // Return a response with found face cropped in JPG to show in browser
```
```php
$face->output(); // Return a jpg string file with the found face
```
```php
$face->save(); // Physically save the face found
```
## Save details
You can save the found face quickly with `$face->save()` output.
The script will create a unique name for your file and store it in the `facedetector` folder in your storage.
```php
$face->save(); // Physically save the face found on folder "facedetector" in your storage
```
You can set your preferred storage location:
```php
$face->save('path/to/save'); // Physically save the found face with a random name in a defined path.
```
Take full control, set the storage path and file name to be saved. Save the file as JPG or PNG, it's your choice.
```php
$face->save('path/to/save', 'new-image.jpg'); // Physically save the found face with a defined path and name.
```
If the save succeeds, will return the following data:

```php
[
    'filename' => 'Saved file name',
    'size' => 'file size after cropping',
    'path' => 'path to file location relative to the disk storage',
    'url' => 'public url to access the file in browser'
]
```

## ☕️ Buy Me a Coffe

I'm happy to be able to contribute to your project, if this package helped you in any way, could you repay me with a coffee? Hugs!

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/leomarriel)
