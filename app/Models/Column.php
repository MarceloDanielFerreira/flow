<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'column';

    protected $fillable = [
        'board_id',
        'nombre',
        'orden',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
