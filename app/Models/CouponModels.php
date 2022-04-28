<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class CouponModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'coupon';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array(), $FIND_IN_SET = '') {
            $builder = $this->db->table($this->table);
            $builder->where($where);
            if ($FIND_IN_SET) {
                $builder->where("FIND_IN_SET($FIND_IN_SET, 'user_id')");
            }
            $builder->orderBY('rank ASC, id DESC');
            return $builder->get()->getResult();
        }
        
        public function c_one_index ($where = array()) {
            $builder = $this->db->table($this->table);
            $builder->select('*'); 
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->where($where);
            $builder->where("((`start_at` != 'NULL' AND `start_at` <= '".nowDate()."') OR `start_at` IS NULL)");
            $builder->where("((`end_at` != 'NULL' AND `end_at` >= '".nowDate()."') OR `end_at` IS NULL)");
            $builder->orderBY('rank ASC, id DESC');
            return $builder->get()->getRow();
        }

        public function c_all_index ($where = array(), $pagination = array()) {
            $builder = $this->db->table($this->table);
            $builder->select('*'); 
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->where($where);
            $builder->where("((`start_at` != 'NULL' AND `start_at` <= '".nowDate()."') OR `start_at` IS NULL)");
            $builder->where("((`end_at` != 'NULL' AND `end_at` >= '".nowDate()."') OR `end_at` IS NULL)");
            $builder->orderBY('rank ASC, id DESC');
            return $builder->get()->getResult();
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