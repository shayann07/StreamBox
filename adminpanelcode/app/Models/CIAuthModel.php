<?php 

namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;

class CIAuthModel {
    
    protected $db;
    
    public function __construct(ConnectionInterface & $db){
        $this->db =& $db;
    }
    
    function validateAdmin($username){
        $query = $this->db->table('tbl_admin')->where('username', $username)->get();
        if($query->getNumRows() > 0) {  
            return $query->getRow();
        } else {
            return null;
        }
    }
}