<?php

namespace App\Models;

use CodeIgniter\Model;

class TsrModel extends Model
{
    protected $table      = 'tsrs';
    protected $primaryKey = 'id';

    // Includes the unique employee_id for future tracking
    protected $allowedFields = ['user_id', 'full_name', 'employee_id'];
}