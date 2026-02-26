<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Core fields required for authentication and role routing
    protected $allowedFields = ['email', 'password', 'role', 'status', 'remember_token'];

    // Consistency: Dates are handled by the DB but accessible here
    protected $useTimestamps = false;
}