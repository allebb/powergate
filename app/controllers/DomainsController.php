<?php

use Powergate\Domain;

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
        //dd($domains);
        return Response::json(array(
                    'errors' => false,
                    'domains' => $domains,
                        ), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
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
            return Response::json(array(
                        'errors' => false,
                        'domain' => $domain,
                            ), 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return Response::json(array(
                        'errors' => true,
                        'domain' => "Not found",
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
