<?php

namespace App\Models;

use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;

class ActivityLogger extends SpatieActivityLogger
{
    protected $table = 'activity_log';


}
