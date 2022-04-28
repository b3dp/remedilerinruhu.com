<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class SpecialModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'special_page';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }
        public function c_one_image ($where = array()) {
            return $this->db->table('special_page_image')->where($where)->get()->getRow();
        }

        public function c_all ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getResult();
        }
        public function c_all_image ($where = array()) {
            return $this->db->table('special_page_image')->where($where)->orderBy('rank ASC, id ASC')->get()->getResult();
        }

        public function count ($where = array()) {
            return $this->db->table($this->table)->where($where)->countAllResults();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function addImage ($set = array()) {
            return $this->db->table('special_page_image')->insert($set);
        }

        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function deleteRow ($id) {
            return $this->db->table($this->table)->where("id", $id)->delete();
        }

        public function deleteImage ($id = array()) {
            return $this->db->table('special_page_image')->where($id)->delete();
        }

        public function pictureRankEdit ($id, $set = array()) {
            return $this->db->table('special_page_image')->where("id", $id)->update($set);
        }
        
    }
?>