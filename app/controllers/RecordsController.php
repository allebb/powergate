<?php

use Powergate\Record;
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
            $validator = new RecordValidator(Input::all());
            $validator->checkValidation();
            $record = new Record;
            $record->saveNew();
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
            $validator = new RecordValidator(Input::all(), false);
            $validator->checkValidation();
            $record = Record::findOrFail($id);
            $record->saveUpdate();
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
                    'record' => $domain->toArray(),
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
