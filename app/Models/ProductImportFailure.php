<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportFailure extends Model
{
    protected $fillable = [
        'user_id',
        'import_key',
        'row_number',
        'row_data',
        'error_message',
        'error_class',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
