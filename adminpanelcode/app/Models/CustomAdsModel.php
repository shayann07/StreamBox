<?php 

namespace App\Models;
use CodeIgniter\Model;

class CustomAdsModel extends Model{
    
    protected $table = 'tbl_custom_ads';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }
}