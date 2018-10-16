<?php

namespace Modules\PriceList\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\PriceList\Models\PriceList;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $priceList = PriceList::all();

        return $this->respondSuccessData($priceList);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('pricelist::create');
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
        return view('pricelist::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('pricelist::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $requestData = $request->input();
        $price = PriceList::find($requestData['id']);
        $price->update(['price' => $requestData['price']]);

        return $this->respondSuccessData($price, 'Cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
