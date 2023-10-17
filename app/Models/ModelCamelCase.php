<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * This class allows to use properties in camel case.
 */
class ModelCamelCase extends Model
{
    public function setAttribute($key, $value)
    {
        parent::setAttribute(Str::snake($key), $value);
    }

    public function getAttribute($key)
    {
        return parent::getAttribute(Str::snake($key));
    }
}