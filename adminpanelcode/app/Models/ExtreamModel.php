<?php 

namespace App\Models;
use CodeIgniter\Model;

class ExtreamModel extends Model{
    
    protected $table = 'tbl_xui_dns';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }
}