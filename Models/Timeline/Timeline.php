<?php namespace App\Modules\Tenant\Models\Timeline;

use Illuminate\Database\Eloquent\Model;
use DB;

class Timeline extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timelines';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'timeline_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'created_date', 'timeline_icon_id', 'message', 'added_by'];

    /**
     * Disable default timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

}
