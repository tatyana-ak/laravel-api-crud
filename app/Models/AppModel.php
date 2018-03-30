<?php
/**
 * Created by PhpStorm.
 * User: tanchik
 * Date: 3/30/18
 * Time: 7:31 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class AppModel extends Model
{
    use SoftDeletes;
}
