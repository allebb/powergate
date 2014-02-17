<?php

use Powergate\Domain;
use Powergate\Validators\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

            $domain = new Domain;
            $domain->serviceNew(Input::all());

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

            $domain = Domain::findOrFail($id);
            $domain->serviceUpdate(Input::all());

        } catch (ValidationException $ex) {
            return $this->apiResponse(400);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
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

    /**
     * A non-standard resource route, mapped in the routes file to display all records under the requested domain.
     * @param type $id
     * @return type
     */
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
