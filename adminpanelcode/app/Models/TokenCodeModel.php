<?php 

namespace App\Models;
use CodeIgniter\Model;

class TokenCodeModel extends Model{
    
    protected $table = 'tbl_token_code';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }
    
    // Method to check if a token is valid
    public function isTokenValid($token_id = '') {
        // Use query builder to fetch token where the status is '1' and token matches
        $builder = $this->builder();
        $builder->where('status', '1');
        $builder->where('token_code', $token_id);
        $query = $builder->get();
        
        // If rows are found, return false, meaning token is already used
        if ($query->getNumRows() > 0) {
            return false;
        } else {
            return true;
        }
    }
}