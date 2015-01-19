<?php
namespace MediaMine\CoreBundle\Controller;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageController extends AbstractController
{
    const DEFAULT_FORMAT = 'jpg';
    const DEFAULT_TEMPLATE_PATH = 'web/images/placeholder/';
    const DEFAULT_SAVE_PATH = 'web/images/resized/';
    const DEFAULT_LIBRARY_FOLDER = 'library/';
    const DEFAULT_TEMPLATE_FOLDER = 'template/';

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    protected $mimeTypes = array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    );

    use EntitityManagerAware;

    public function indexAction()
    {
        throw new NotFoundHttpException();
    }

    public function libraryAction($transformations, $width, $height, $pathKey, $format)
    {
        $files = $this->getRepository('File\File')->findFullBy(array('pathKey' => $pathKey));
        if (!count($files)) {
            throw new NotFoundHttpException();
        } else {
            $file = $files[0];
            $filePath = $file->getPath();
            return $this->processImage($filePath, $pathKey, self::DEFAULT_LIBRARY_FOLDER, $transformations, $width, $height, $format);
        }
    }

    public function processImage($readPath, $name, $writePath, $transformations, $width, $height, $format)
    {
        if (!in_array($format, array_keys($this->mimeTypes))) {
            $format = self::DEFAULT_FORMAT;
        }
        $cacheName = $name . '.' . $format;
        $height = (int)$height;
        if ($height) {
            $cacheName = $height . '-' . $cacheName;
        }
        $width = (int)$width;
        if ($width) {
            $cacheName = $width . '-' . $cacheName;
        }
        if ($transformations) {
            $cacheName = $transformations . '-' . $cacheName;
        }
        $imagine = new \Imagine\Gd\Imagine();
        /**
         * @var $image \Imagine\Image\ImageInterface
         */
        $image = $imagine->open($readPath);
        $transformations = explode('_', $transformations);
        switch ($transformations[0]) {
            case 'blank' :
                if ($width && $height) {
                    $box = new \Imagine\Image\Box($width, $height);
                    $hex = (count($transformations) > 1) ? $transformations[1] : '000';
                    $color = new \Imagine\Image\Color('#' . $hex, 100);
                    $image = $imagine->create($box, $color);
                } else {
                    throw new NotFoundHttpException();
                }
                break;
            case 'hrbox' :
                if ($width && $height) {
                    $size = $image->getSize()->widen($width);
                    if ($size->getHeight() < $height) {
                        $size = $image->getSize()->heighten($height);
                    }
                    $image = $image->copy()->resize($size);
                    $origin = ($size->getHeight() - $height) / 2;
                    $box = new \Imagine\Image\Box($width, $height);
                    if ($origin > -1) {
                        $image = $image->crop(new \Imagine\Image\Point(0, $origin), $box);
                    } else {
                        $hex = (count($transformations) > 1) ? $transformations[1] : '000';
                        $color = new \Imagine\Image\Color('#' . $hex, 100);
                        $nimage = $imagine->create($box, $color);
                        $origin = ($height - $size->getHeight()) / 2;
                        $image = $nimage->paste($image, new \Imagine\Image\Point(0, $origin));
                    }
                } else {
                    throw new NotFoundHttpException();
                }
                break;
            case 'vrbox' :
                if ($width && $height) {
                    $size = $image->getSize()->heighten($height);
                    if ($size->getWidth() < $width) {
                        $size = $image->getSize()->widen($width);
                    }
                    $image = $image->copy()->resize($size);
                    $origin = ($size->getWidth() - $width) / 2;
                    $box = new \Imagine\Image\Box($width, $height);
                    if ($origin > -1) {
                        $image = $image->crop(new \Imagine\Image\Point($origin, 0), $box);
                    } else {
                        $color = new \Imagine\Image\Color('#000', 100);
                        $nimage = $imagine->create($box, $color);
                        $origin = ($width - $size->getWidth()) / 2;
                        $image = $nimage->paste($image, new \Imagine\Image\Point($origin, 0));
                    }
                } else {
                    throw new NotFoundHttpException();
                }
                break;
            default :
                if ($width && $height) {
                    $image = $image->thumbnail(new \Imagine\Image\Box($width, $height));
                } else {
                    throw new NotFoundHttpException();
                }
                break;
        }
        $response = new Response();
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Type', $this->mimeTypes[$format]);
        $response->setContent($image->get($format));
        $image->save($this->rootDir . '/../' . self::DEFAULT_SAVE_PATH . $writePath . $cacheName);
        $response->send();
        return $response;
    }

    public function templateAction($transformations, $width, $height, $pathKey, $format)
    {
        $basePath = $this->rootDir . '/../' . self::DEFAULT_TEMPLATE_PATH . $pathKey;
        $filePath = '';
        foreach ($this->mimeTypes as $k => $m) {
            $p = $basePath . '.' . $k;
            if (file_exists($p)) {
                $filePath = $p;
                break;
            }
        }
        if ($filePath == '') {
            throw new NotFoundHttpException();
        }
        return $this->processImage($filePath, $pathKey, self::DEFAULT_TEMPLATE_FOLDER, $transformations, $width, $height, $format);
    }
}
