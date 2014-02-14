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
            // Its all good in the hood!
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
