<?php 

namespace App\Models;
use CodeIgniter\Model;

class WebModel extends Model{
    
    protected $table = 'tbl_web_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }

    public function getSettings(){
        return $this->where('id', 1)->first(); // Uses Query Builder (Safer)
    }
}
