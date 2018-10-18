<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Base\Http\Controllers\Controller;
use Modules\Blog\Models\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Blog::class);

        $perPage = $this->getPerPage($request);

        $Blogs = Blog ::whereFullLike('name',$request->input('name'))
        ->whereFullLike('address',$request->input('address'))
        ->filterWhere('type','=',$request->input('type'))
        ->orderBy('id','desc')
        ->paginate($perPage);

        return $this->respondSuccessData($Blogs);
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function list(Request $request)
    {
        $this->authorize('list', Blog::class);

        $query = Blog::query()->limit(20);
        $query->whereFullLike('name',$request->input('q'))
        ->filterWhere('type','=',$request->input('type'));

        return ['results'=> $query->get(['id'],'name as text')];

    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Blog::class);

        $requestData= $request ->input();
        $validator =$this->validateRequestData($requestData);

        if($validator->fails()){
            return $this->respondInvalidData($validator->messages());
        }
         
        $blog = new Blog();
        $blog ->fill($requestData);
        $blog ->save();

        
        return $this->respondSuccessData($blog,'Thêm Blog Thành Công');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        //return view('blog::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        //return view('blog::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $this->authorize('update', Blog::class);

        $blog = Blog::findOrFail($id);

        $requestData= $request->input();
        $validator = $this->validateRequestData($requestData, $id);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $blog->fill($requestData);
        $blog->save();

        return $this->respondSuccessData($blog);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Blog::class);

        $blog = Blog::findOrFail($id);
        $blog->delete();

        return $this->respondSuccessData([],'Delete successfully');
    }

    private function validateRequestData($requestData, $modelId = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'name' => 'bail|required|string|max:255|unique:blogs,name,' . $modelId,
                'address' => 'bail|required|string|max:255',
                'type' => 'required'
            ],
            [
                'name.required' => 'Chưa nhập tên ',
                'name.unique' => 'Tên  đã tồn tại trong hệ thống',
                'name.max' => 'Tên  chứa tối đa 225 ký tự',
                'address.required' => 'Chưa nhập địa chỉ ',
                'address.max' => 'Địa chỉ  chứa tối đa 225 ký tự',
                'type.required' => 'Chưa chọn loại '
            ]
        );
    }
}
