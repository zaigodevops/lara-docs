<?php

namespace DMS\DocumentManagementSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }
}