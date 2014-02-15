<?php

use Powergate\Supermaster;
use Powergate\Validators\SupermasterValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Powergate\Validators\ValidationException;

class SupermastersController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $supermasters = Supermaster::all();
        return Response::json(array(
                    'errors' => false,
                    'supermasters' => $supermasters->toArray(),
                        ), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        try {
            $supermaster = new Supermaster;
            $supermaster->ip = Input::get('ip');
            $supermaster->nameserver = strtolower(Input::get('nameserver'));
            $supermaster->account = strtolower(Input::get('account'));

            $validator = new SupermasterValidator($supermaster->toArray());
            $validator->checkValidation();

            $supermaster->save();
        } catch (ValidationException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Data validation failed',
                            ), 400);
        } catch (Exception $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Server error',
                            ), 500);
        }
        return Response::json(array(
                    'errors' => false,
                    'supermaster' => $supermaster->toArray(),
                        ), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $supermaster = Supermaster::findOrFail($id);
            return Response::json(array(
                        'errors' => false,
                        'supermaster' => $supermaster->toArray(),
                            ), 200);
        } catch (ModelNotFoundException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => "Not found",
                            ), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        try {
            $supermaster = new Supermaster;
            $supermaster->ip = Input::get('ip');
            $supermaster->nameserver = strtolower(Input::get('nameserver'));
            $supermaster->account = strtolower(Input::get('account'));

            $validator = new SupermasterValidator($supermaster->toArray(), false);
            $validator->checkValidation();

            $supermaster->save();
        } catch (ValidationException $ex) {
            dd($ex);
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Data validation failed',
                            ), 400);
        } catch (ModelNotFoundException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Not found',
                            ), 404);
        }
        return Response::json(array(
                    'errors' => false,
                    'record' => $supermaster->toArray(),
                        ), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $supermaster = Supermaster::findOrFail($id);
            $supermaster->delete();
        } catch (ModelNotFoundException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Not found',
                            ), 404);
        } catch (Exception $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => 'Server error',
                            ), 500);
        }
        return Response::json(array(
                    'errors' => false,
                    'message' => 'Deleted successfully'
                        ), 200);
    }

}
