<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const STATUS_SUCCESS = 'success';
    const STATUS_INVALID = 'invalid';
    
    public function getPerPage($request, $default = 10)
    {
        return $request->get('per_page', $default);
    }

    /**
     * @param boolean $status
     * @param MessageBag|string $messages
     * @param Collection|array $data
     * @return \Illuminate\Http\Response
     * */
    public function getResponseJson($status, $messages, $data = [])
    {
        if ($messages instanceof MessageBag) {
            $msgs = [];
            foreach ($messages->messages() as $attr => $msg) {
                $msgs[$attr] = data_get($msg, 0, '');
            }
            $messages = $msgs;
        }

        return response()->json([
            'status' => $status,
            'messages' => $messages,
            'data' => $data,
        ]);
    }

    public function destroyImages($id, $modelName)
    {
        try {
            Image::where('imagetable_id', $id)
                ->where('imagetable_type', '=', $modelName)
                ->delete();
            return true;
        } catch (\Exception $ex) {
            return false;
        }

    }
}

