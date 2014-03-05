<?php

namespace Powergate\Filters;

use Config;
use Request;
use Response;

class PowergateAPIAuth
{

    public function filter()
    {
        return $this->checkAuthCredentials();
    }

    private function checkAuthCredentials()
    {
        if (Request::getUser() == Config::get('powergate.auth.user') and Request::getPassword() == Config::get('powergate.auth.key')) {
            if (Config::get('powergate.client.restricted', false)) {
                if (!in_array(Request::server('REMOTE_ADDR'), Config::get('powergate.clients.allowed_client_ips', ['127.0.0.1']))) {
                    return $this->respondUnathorisedRequest();
                }
            }
        } else {
            return $this->respondUnathorisedRequest();
        }
    }

    private function respondUnathorisedRequest()
    {
        return Response::json([
                    'error' => true,
                    'message' => 'Unauthorised request',
                        ], 401);
    }

}
