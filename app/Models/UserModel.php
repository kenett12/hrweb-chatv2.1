<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Core fields required for authentication and role routing
    protected $allowedFields = ['email', 'full_name', 'password', 'role', 'status', 'remember_token', 'client_id', 'client_role', 'availability_status'];

    // Consistency: Dates are handled by the DB but accessible here
    protected $useTimestamps = false;
}