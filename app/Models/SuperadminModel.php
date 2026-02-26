<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperadminModel extends Model
{
    protected $table      = 'superadmins';
    protected $primaryKey = 'id';

    // user_id links back to the UserModel
    protected $allowedFields = ['user_id', 'admin_name', 'is_master'];
}