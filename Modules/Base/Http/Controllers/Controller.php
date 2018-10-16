<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Modules\Image\Models\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_INVALID = 'invalid';
    const RESPONSE_STATUS_ERROR = 'error';

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

    private function respondJson($data)
    {
        return response()->json($data);
    }

    /**
     * @param array $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondSuccessData($data = [], $message = 'success')
    {
        return $this->respondJson([
            'data' => $data,
            'message' => $message,
            'status' => self::RESPONSE_STATUS_SUCCESS,
        ]);
    }

    /**
     * @param MessageBag $messageBag
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondInvalidData($messageBag, $message = 'Dữ liệu không hợp lệ')
    {
        $messages = [];

        foreach ($messageBag->messages() as $attr => $msg) {
            $messages[$attr] = data_get($msg, 0);

            
            /////////////////////////////
            // quick solution. Convert from $msg['a.b'] = 'x' TO $msg['a']['b'] = 'x'
            // for array validate (Redux form)
            $a = explode('.', $attr);
            if (count($a) > 1) {
                unset($messages[$attr]);
                $messages[$a[0]][$a[1]] = data_get($msg, 0);
            }
            // end quick solution
            /////////////////////////////

        }

        return $this->respondJson([
            'data' => $messages,
            'message' => $message,
            'status' => self::RESPONSE_STATUS_INVALID,
        ])
            ->setStatusCode(422);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message = 'Lỗi hệ thống')
    {
        return $this->respondJson([
            'message' => $message,
            'status' => self::RESPONSE_STATUS_SUCCESS,
        ])
            ->setStatusCode(500);
    }

}

