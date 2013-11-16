<?php
namespace MediaMine\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Http\Response as Response;
use Swagger\Swagger as Swagger;
use Swagger\Annotations as SWG;

class ApiController extends AbstractActionController
{
    /**
     * Display the documentation
     *
     * @return JsonModel
     */
    public function displayAction()
    {
        /** @var $swagger \Swagger\Swagger */
        $swagger = $this->serviceLocator->get('Swagger\Swagger');
        $jsonModel = new JsonModel();
        return $jsonModel->setVariables($swagger->getResourceList());
    }

    /**
     * Get the details of a resource
     *
     * @return JsonModel
     */
    public function detailsAction()
    {
        /** @var $swagger \Swagger\Swagger */
        $swagger = $this->serviceLocator->get('Swagger\Swagger');
        $resource = $swagger->getResource('/' . $this->params('resource', null));

        // Specify basepath
        $event = $this->getEvent();
        $request = $event->getRequest();
        $router = $event->getRouter();
        $uri = $router->getRequestUri();
        $data = Swagger::export($resource);
        $data['basePath'] = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl()) . $data['basePath'];
        if ($resource === false) {
            return new JsonModel();
        }

        $jsonModel = new JsonModel($data);

        return $jsonModel;
    }
}