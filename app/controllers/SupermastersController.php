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
        return $this->apiResponse(200, 'supermasters', $supermasters->toArray());
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
            return $this->apiResponse(400);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }
        
        return $this->apiResponse(201, 'supermaster', $supermaster->toArray());
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
            
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }
        
        return $this->apiResponse(200, 'supermaster', $supermaster->toArray());
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
            return $this->apiResponse(400);
        } catch (ModelNotFoundException $ex) {
            return $this->apiResponse(404);
        }

        return $this->apiResponse(200, 'supermaster', $supermaster->toArray());
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
            return $this->apiResponse(404);
        } catch (Exception $ex) {
            return $this->apiResponse(500);
        }
        
        return $this->apiResponse(200, 'message', 'Deleted successfully');
    }

}
