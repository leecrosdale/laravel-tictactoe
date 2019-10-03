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

    public function getCurrentTeamAttribute()
    {
        if ($this->isEven($this->turn_number)) {
            return 'x';
        }

        return 'o';
    }

    private function isEven($value) {
        return $value % 2 === 0;
    }

}
