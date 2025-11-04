<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $guarded = [];

    //Realationships

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}
