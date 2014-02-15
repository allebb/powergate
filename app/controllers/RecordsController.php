<?php

use Powergate\Record;
use Powergate\Validators\RecordValidator;
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
        return Response::json(array(
                    'errors' => false,
                    'records' => $records->toArray(),
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
                    'record' => $record->toArray(),
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
            $record = Record::findOrFail($id);
            return Response::json(array(
                        'errors' => false,
                        'record' => $record->toArray(),
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
            
            $record = new Record;
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
                    'record' => $record->toArray(),
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
            $record = Record::findOrFail($id);
            $record->delete();
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
