<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class AttributeGroupModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'attribute_group';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getResult();
        }
        
        public function count ($where = array()) {
            return $this->db->table($this->table)->where($where)->countAllResults();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function deleteRow ($id) {
            return $this->db->table($this->table)->where("id", $id)->delete();
        }

    }
?>