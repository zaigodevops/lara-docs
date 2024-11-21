<?php

namespace DMS\DocumentManagementSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasFactory;
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
