<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table      = 'clients';
    protected $primaryKey = 'id';

    // Captures company details for the chat app logic
    protected $allowedFields = ['user_id', 'company_name', 'hr_contact'];
}