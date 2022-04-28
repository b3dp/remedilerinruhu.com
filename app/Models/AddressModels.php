<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class AddressModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
        }

        public function c_one ($table, $where = array()) {
            return $this->db->table($table)->where($where)->get()->getRow();
        }

        public function c_all ($table, $where = array(), $orderBy = '' ) {
            return $this->db->table($table)->where($where)->orderBy($orderBy)->get()->getResult();
        }

        public function neighborhood_all ($where = array()) {
            $builder = $this->db->table('neighborhood n');
            $builder->select('n.*');
            $builder->join('district d', 'n.DistrictID = d.DistrictID');
            $builder->where($where);
            $builder->orderBy("n.NeighborhoodName ASC");
            $query = $builder->get()->getResult();
            return $query;
        }

        public function add ($table, $set = array()) {
            return $this->db->table($table)->insert($set);
        }

        public function edit ($table, $where = array(), $set = array()) {
            return $this->db->table($table)->where($where)->update($set);
        }

        public function deleteRow ($table, $where = array()) {
            return $this->db->table($table)->where($where)->delete();
        }
        
    }
?>