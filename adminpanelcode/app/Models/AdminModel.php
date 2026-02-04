<?php 

namespace App\Models;
use CodeIgniter\Model;

class AdminModel extends Model{
    
    protected $table = 'tbl_admin';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }
    
    // Method to check if user already exists by email
    public function getUserByEmail($email) {
        return $this->db->table($this->table)  // Use $this->table to reference the table defined in the model
                        ->where('email', $email)
                        ->get()
                        ->getRowArray(); // Returns the user row if found, null if not
    }
    
    public static function getUserdata($user_id = '0') {
        if ($user_id == '0') {
            return null; // Return null if no user ID is provided
        }
        // Get instance of the model
        $model = new self();
        return $model->where('id', $user_id)->first();
    }
}