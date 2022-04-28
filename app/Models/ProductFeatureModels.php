<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class ProductFeatureModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'product_feature';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function editAttr ($where = array(), $set = array()) {
            return $this->db->table($this->table)->where($where)->update($set);
        }

        public function deleteRow ($where = array(), $where2 = '') {
            $builder = $this->db->table($this->table);
            $builder->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->delete();
        }
    }
?>