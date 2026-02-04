<?php 

namespace App\Models;
use CodeIgniter\Model;

class GalleryModel extends Model{
    
    protected $table = 'tbl_poster_gallery';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct(){
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table); // Dynamically get all fields
    }
}