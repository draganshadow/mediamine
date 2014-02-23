<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use Doctrine\ORM\Query;
use Netsyos\Common\Controller\AbstractController;

class ImageController extends AbstractController
{
    const DEFAULT_FORMAT = 'jpg';
    const DEFAULT_SAVE_PATH = 'public/images/';

    protected $mimeTypes = array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    );

    public function indexAction()
    {
        $pathKey = $this->params('pathKey', null);
        if (!$pathKey) {
            return $this->notFoundAction();
        }
        $files = $this->getRepository('File\File')->findFullBy(null, null, null, null, null, null, $pathKey);
        if (!count($files)){
            return $this->notFoundAction();
        } else {
            $file = $files[0];
            $filePath    = $file->getPath();
            $format  = $this->params('format', self::DEFAULT_FORMAT);

            if (!in_array($format, array_keys($this->mimeTypes))) {
                $format = self::DEFAULT_FORMAT;
            }
            $cacheName = $pathKey . '.' . $format;
            $height  = (int) $this->params('height', 0);
            if ($height) {
                $cacheName = $height . '-' . $cacheName;
            }
            $width   = (int) $this->params('width', 0);
            if ($width) {
                $cacheName = $width . '-' . $cacheName;
            }
            $transformations   = $this->params('transformations', null);
            if ($transformations) {
                $cacheName = $transformations . '-' . $cacheName;
            }
            $imagine = $this->getServiceLocator()->get('imagine-service');
            $image = $imagine->open($filePath);
            if ($width && $height) {
                $image = $image->thumbnail(new \Imagine\Image\Box($width, $height));
            }

            $response = $this->getResponse();
            $response->setContent($image->get($format));
            $image->save(self::DEFAULT_SAVE_PATH . $cacheName);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $this->mimeTypes[$format]);
            return $response;
        }
    }
}
