<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QurbanGroup extends Model
{
    protected $fillable = ['name', 'animal_type', 'purchase_price'];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'group_id');
    }
}
