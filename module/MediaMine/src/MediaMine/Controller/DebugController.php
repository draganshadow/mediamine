<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use MediaMine\Parser\MovieParser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Entity\User;


class DebugController extends AbstractActionController
{
    public function indexAction()
    {
        $parser = new MovieParser();
        $result = $parser->parse('');


$NbreData = count($result);
if ($NbreData != 0) {

    echo '<table border="5">';
    foreach ($result as $key => $value){
           echo '<tr>';
           echo '<td>';
           echo '<th>';
           echo $key;
           echo '</th>';
           echo '</td>';

           if (is_array ($value)){
                $k=0;
                $Nbrelng = count($value);
                for ($i=1; $i<=$Nbrelng; $i++) {
                    echo '<td>';
                      if (is_array ($value[$k])){
                          echo '<pre>';
                          var_dump($value[$k]);
                          echo '</pre>';
                          echo '</td>';
                      }
                      else{
                           echo $value[$k];
                           echo '</td>';
                      }
                      $k++;
                }
           }
           else {
                  echo '<td>';
                  echo $value; //$value;
           }
    }
    echo '</td>';
    echo '</tr>';
    echo '</table>';


}
else {

    echo 'Error...';


}
      echo '<pre>';
        var_dump($result);

     echo '</pre>';

        $viewModel = new ViewModel();
        return $viewModel;
    }

    public function imageAction()
    {
        $file    = $this->params('file', ''); // @todo: apply STRICT validation!
        $width   = $this->params('width', 150); // @todo: apply validation!
        $height  = $this->params('height', 150); // @todo: apply validation!
        $imagine = $this->getServiceLocator()->get('imagine-service');
        $image   = $imagine->open($file);

        $path = 'public/images';
//        $transformation = new \Imagine\Filter\Transformation();

//        $transformation->thumbnail(new \Imagine\Image\Box($width, $height));
//        $transformation->apply($image);

        $response = $this->getResponse();
        $response->setContent($image->thumbnail(new \Imagine\Image\Box($width, $height))->get('jpg'));
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png');
            //->addHeaderLine('Content-Length', mb_strlen($imageContent));

        return $response;

//        $viewModel = new ViewModel();
//        $viewModel->setTerminal(true);
//        return $viewModel;
    }
}

