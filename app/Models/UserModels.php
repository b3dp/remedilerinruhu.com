<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class UserModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'users';
        }

        public function c_one ($where = array()) {
            $builder = $this->db->table('users');
            $builder->select('users.*, ut.name AS ut_name');
            $builder->join("user_type ut", "ut.id = users.role");
            $builder->where($where);
            return $builder->get()->getRow();
        }

        public function c_all ($where = array()) {
            $builder = $this->db->table('users');
            $builder->select('users.*, ut.name AS ut_name');
            $builder->join("user_type ut", "ut.id = users.role");
            $builder->where($where);
            return $builder->get()->getResult();
        }

        public function c_all_type ($where = array()) {
            $builder = $this->db->table('user_type');
            $builder->select('*');
            $builder->where($where);
            return $builder->get()->getResult();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function edit ($where = array(), $set = array()) {
            return $this->db->table($this->table)->where($where)->update($set);
        }

        public function favorite_one ($where = array()) {
            return $this->db->table('user_product_favorite')->where($where)->get()->getRow();
        }
        
        public function favorite_all ($where = array(), $pagination = array()) {
            $builder = $this->db->table('user_product_favorite')->where($where)->orderBy('id DESC');
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            return $builder->get()->getResult();
        }

        public function favorite_count ($where = array()) {
            return $this->db->table('user_product_favorite')->where($where)->countAllResults();
        }
        
        public function favoriteProduct ($set = array()) {
            return $this->db->table('user_product_favorite')->insert($set);
        }

        public function favoriteProductRemove ($where = array()) {
            return $this->db->table('user_product_favorite')->where($where)->delete();
        }
        
        public function role_all ($where = array()) {
            return $this->db->table('user_type')->where($where)->get()->getResult();
        }

        public function c_one_shopping_centre ($where = array()) {
            $builder = $this->db->table('shopping_centre');
            if ($where) {
                $builder->where($where);
            }
            return $builder->get()->getRow();
        }

        public function c_all_shopping_centre ($where = array()) {
            $builder = $this->db->table('shopping_centre');
            if ($where) {
                $builder->where($where);
            }
            return $builder->get()->getResult();
        }
        
    }
?>