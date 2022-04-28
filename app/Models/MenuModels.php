<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class MenuModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'menu';
        }

        public function c_one ($where = array()) {
            $builder = $this->db->table($this->table. ' f');
            $builder->select('f.*, fg.id AS fg_id, fg.title AS fg_title');
            $builder->join('menu_group fg', 'fg.id = f.menu_group_id', 'left');
            $builder->where($where);
            $query = $builder->get()->getRow();
            return $query;
        }

        public function c_all ($where = array(), $inWhere = '0', $inWhere2 = '0', $pagination = array()) {
            $builder = $this->db->table($this->table. ' f');
            $builder->select('f.*, fg.id AS fg_id, fg.title AS fg_title');
            $builder->join('menu_group fg', 'fg.id = f.menu_group_id', 'left');
            $builder->orderBy('rank ASC, id ASC');
            $builder->where($where);
            if ($inWhere != 0) {
                $builder->whereIN("fg.id", $inWhere);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->groupBy("f.id");
            $query = $builder->get()->getResult();
            return $query;
        }
        
        public function count ($where = array()) {
            $builder = $this->db->table($this->table. ' f');
            $builder->select('f.*, fg.id AS fg_id, fg.title AS fg_title');
            $builder->join('menu_group fg', 'fg.id = f.menu_group_id', 'left');
            $builder->where($where);
            $query = $builder->countAllResults();
            return $query;
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