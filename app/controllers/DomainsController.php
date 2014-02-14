<?php

use Powergate\Domain;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Powergate\Validators\ValidationException;
use Powergate\Validators\DomainValidator;

class DomainsController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $domains = Domain::all();
        return Response::json(array(
                    'errors' => false,
                    'domains' => $domains->toArray(),
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
            $validator = new DomainValidator(Input::all());
            $validator->checkValidation();
            $domain = new Domain;
            $domain->saveNew();
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
                    'domain' => $domain->toArray(),
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
            $domain = Domain::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'message' => "Not found",
                            ), 404);
        }
        return Response::json(array(
                    'errors' => false,
                    'domain' => $domain->toArray(),
                        ), 200);
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
            $validator = new DomainValidator(Input::all(), false);
            $validator->checkValidation();
            $domain = Domain::findOrFail($id);
            $domain->saveUpdate();
        } catch (ValidationException $ex) {
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
                    'domain' => $domain->toArray(),
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
            $domain = Domain::findOrFail($id);
            $domain->delete();
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
