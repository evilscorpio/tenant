<?php namespace App\Modules\Tenant\Models\Client;

use Illuminate\Database\Eloquent\Model;
use DB;


class ClientEmail extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'client_emails';

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
    protected $fillable = ['email', 'subject', 'body', 'status', 'user_id', 'client_id', 'created_at'];

    public $timestamps = false;

    function storeMail(array $request)
    {

    }

}

