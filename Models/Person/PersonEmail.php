<?php namespace App\Modules\Tenant\Models\Person;

use App\Modules\Tenant\Models\Phone;
use Illuminate\Database\Eloquent\Model;
use DB;

class PersonEmail extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'person_emails';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'person_email_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email_id', 'person_id', 'is_primary'];


    /**
     * Disable the default time stamp feature
     *
     * @var boolean
     */
    public $timestamps = false;

}
