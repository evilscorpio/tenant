<?php namespace App\Modules\Tenant\Models\Timeline;

use Illuminate\Database\Eloquent\Model;
use DB;

class ClientTimeline extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'client_timeline';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'timeline_id', 'application_id'];

    /**
     * Disable default timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    public function getDetails($client_id)
    {
        $logs = ClientTimeline::join('timelines', 'timelines.timeline_id', '=', 'client_timeline.timeline_id')
            ->join('timeline_types', 'timeline_types.type_id', '=', 'timelines.timeline_type_id')
            ->where('client_timeline.client_id', $client_id)
            ->select('timelines.*', 'timeline_types.image')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('created_date');
        //dd($logs->toArray());
        return $logs;
    }

}
