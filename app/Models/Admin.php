<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function listLeadActive($status = "all"){
        $list = $this->hasMany(Lead::class)->where("position", "active");
        if($status != 'all'){
            $list = $list->where("status", $status);
        }
        return $list;
    }
}
