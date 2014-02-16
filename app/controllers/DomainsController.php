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
        return $this->apiResponse(200, 'domains', $domains->toArray());
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
            return $this->apiResponse(400);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }

        return $this->apiResponse(201, 'domain', $domain->toArray());
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
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'domain', $domain->toArray());
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
            return $this->apiResponse(400);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'domain', $domain->toArray());
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
            return $this->apiResponse(404);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }

        return $this->apiResponse(200, 'message', 'Deleted successfully');
    }

    public function domainRecords($id)
    {
        try {

            $domain = Domain::with('records')->findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'domain', $domain->toArray());
    }

}
