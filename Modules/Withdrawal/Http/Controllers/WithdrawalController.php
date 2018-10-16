<?php

namespace Modules\Withdrawal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Withdrawal\Models\Withdrawal;
use Validator;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('withdrawal::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('withdrawal::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('withdrawal::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('withdrawal::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
