<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class SliderModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'slider';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array()) {
            return $this->db->table($this->table)->where($where)->orderBY('rank ASC, id DESC')->get()->getResult();
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