<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Notification\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);

        $notifications = Notification::where('to_user_id', '=', auth()->id())->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($notifications);
    }

    /**
     * Get list user's notifications
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUserNotifications()
    {
        $notifications = Notification::where('to_user_id', '=', auth()->id())
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();

        $countNewNotification = Notification::where('to_user_id', '=', auth()->id())
            ->where('is_read', '=', Notification::IS_UNREAD)
            ->count();

        return $this->respondSuccessData([
            'notifications' => $notifications,
            'countNewNotification' => $countNewNotification,
        ]);
    }

    public function getSumNotificationsUnread()
    {
        $sumNotification = Notification::where('to_user_id', '=', auth()->id())
            ->where('is_read', '=', Notification::IS_UNREAD)
            ->count();

        return $this->respondSuccessData($sumNotification);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Notification $notification */
        $notification = Notification::findOrFail($id);

        $notification->fill($request->input());
        $notification->save();

        return $this->respondSuccessData($notification);
    }

}
