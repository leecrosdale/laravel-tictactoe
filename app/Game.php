<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['turn_number'];

    protected $with = ['picks'];

    public function picks()
    {
        return $this->hasMany(Pick::class);
    }

}
