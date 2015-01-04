<?php
namespace MediaMine\CoreBundle\Controller\OAuth;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;

class TokenController
{
    /**
     * @var OAuth2
     * @Inject("fos_oauth_server.server")
     */
    public $server;

    /**
     * @param  Request $request
     * @return type
     */
    public function tokenAction(Request $request)
    {
        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
