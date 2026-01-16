<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchManager extends Model
{
    protected $fillable = ['branch_id', 'manager_id'];
    protected $table = 'branch_managers';
}
