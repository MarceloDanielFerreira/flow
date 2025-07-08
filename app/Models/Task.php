<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'task';

    protected $fillable = [
        'board_id',
        'column_id',
        'titulo',
        'descripcion',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function column()
    {
        return $this->belongsTo(Column::class);
    }
}
