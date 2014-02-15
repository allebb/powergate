<?php

class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function apiResponse($code = 200, $key = null, $data = null)
    {

        if ($code >= 400) {
            $error = true;
        } else {
            $error = false;
        }

        if ($code == 400 and $data == null) {
            $key = 'message';
            $data = 'Data validation failed';
        }
        if ($code == 404 and $data == null) {
            $key = 'message';
            $data = 'Not found';
        }
        if ($code == 500 and $data == null) {
            $key = 'message';
            $data = 'Server error';
        }

        return Response::json(array(
                    'errors' => $error,
                    $key => $data,
                        ), $code);
    }

}
