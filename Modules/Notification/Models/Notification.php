<?php

namespace Modules\Notification\Models;

use Modules\Base\Models\BaseModel;

class Notification extends BaseModel
{
    /** Values of is_read column */
    const IS_READ = 1;
    const IS_UNREAD = 0;

    protected $fillable = [
        'is_read'
    ];

    public function notificationtable()
    {
        $this->morphTo();
    }

    /**
     * Create new object notification
     * @param $modelType
     * @param int $modelID
     * @param int $toUserID
     * @param int $fromUserID
     * @return Notification
     */
    protected static function baseNotification($modelType, $modelID, $fromUserID = null, $toUserID = null)
    {
        $notification = new Notification(['is_read' => self::IS_UNREAD]);

        $notification->notificationtable_type = $modelType;
        $notification->notificationtable_id = $modelID;
        $notification->from_user_id = $fromUserID;
        $notification->to_user_id = $toUserID;

        return $notification;
    }
}
