<?php

namespace Modules\Image\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Http\Controllers\Controller;
use Modules\Image\Models\Image;

class ImageController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);
        $images = Image::paginate($perPage);

        return $this->respondSuccessData($images);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('images')) {
            abort(500, 'Ảnh không tồn tại');
        }

        $imagePaths = [];
        foreach ($request->file('images') as $file) {
            if ($file->getClientSize() < 1024) {
                abort(500, 'Ảnh phải có kích thước nhỏ hơn 1Mb');
            }

            $path = $file->store('upload/' . date('Y/m/d'));
            $arrPath = explode('/', $path);
            $sortPath = 'upload/' . date('Y/m/d') . '/' . last($arrPath);
            array_push($imagePaths, $sortPath);
        }

        return $this->respondSuccessData($imagePaths, 'Thêm ảnh thành công');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        if (!Storage::delete($image->path)) {
            abort(500, 'Xảy ra lỗi khi xoá ảnh');
        }

        $image->delete();

        return $this->respondSuccessData([], 'Xóa ảnh thành công');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $path = $request->input('image');
        if (!Storage::delete($path)) {
            abort(500, 'Xảy ra lỗi khi xoá ảnh');
        }

        return $this->respondSuccessData([], 'Xóa ảnh thành công');
    }

}
