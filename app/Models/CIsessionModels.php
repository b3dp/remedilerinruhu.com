<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class CIsessionModels extends Model
    {
        protected $db;
        public $veri = array();

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'ci_session';
            $this->veri = array();
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getResult();
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
