<?php

use Powergate\Record;
use Powergate\Validators\RecordValidator;
use Powergate\Validators\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecordsController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $records = Record::all();
        return $this->apiResponse(200, 'records', $records->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {

        try {

            $record = new Record;
            $record->domain_id = Input::get('domain_id');
            $record->name = Input::get('name');
            $record->type = strtoupper(Input::get('type'));
            $record->content = strtolower(Input::get('content'));
            $record->ttl = Input::get('ttl');
            $record->prio = Input::get('prio');
            $record->change_date = time();

            $validator = new RecordValidator($record->toArray());
            $validator->checkValidation();

            $record->save();
        } catch (ValidationException $ex) {
            return $this->apiResponse(400);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }

        return $this->apiResponse(201, 'record', $record->toArray());
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

            $record = Record::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'record', $record->toArray());
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

            $record = Record::find($id);
            $record->domain_id = Input::get('domain_id');
            $record->name = Input::get('name');
            $record->type = strtoupper(Input::get('type'));
            $record->content = strtolower(Input::get('content'));
            $record->ttl = Input::get('ttl');
            $record->prio = Input::get('prio');
            $record->change_date = time();

            $validator = new RecordValidator($record->toArray(), false);
            $validator->checkValidation();

            $record->save();
        } catch (ValidationException $ex) {
            return $this->apiResponse(400);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'record', $record->toArray());
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

            $record = Record::findOrFail($id);
            $record->delete();

        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }

        return $this->apiResponse(200, 'message', 'Deleted successfully');
    }

    /**
     * A non-standard resource route, mapped in the routes file to display the parent domain  under the requested record.
     * @param type $id
     * @return type
     */
    public function recordDomain($id)
    {
        try {

            $domain = Record::with('domain')->findOrFail($id);

        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'record', $domain->toArray());
    }

}
