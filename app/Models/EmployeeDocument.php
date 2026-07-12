<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class EmployeeDocument extends Model
{
    //
    protected $fillable = ["employee_id", "file_original_name", "file_mime_type", "file_size", "file_path", "document_type", "status"];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
