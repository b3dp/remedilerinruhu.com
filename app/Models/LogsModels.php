<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class LogsModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'logs';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array(), $pagination = '') {
            $builder = $this->db->table($this->table . ' l');
            $builder->select("l.*, la.activity_name, la.activity_description, la.error_status");
            $builder->join("logs_activity la", "l.activity_id = la.id");
            $builder->where($where);
            $builder->orderBy('l.id DESC');
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            return $builder->get()->getResult();
        }

        public function count ($where = array()) {
            $builder = $this->db->table($this->table . ' l');
            $builder->select("l.*, la.activity_name, la.activity_description, la.error_status");
            $builder->join("logs_activity la", "l.activity_id = la.id");
            $builder->where($where);
            $builder->orderBy('l.id DESC');
            return $builder->countAllResults();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function edit ($where = array(), $set = array()) {
            return $this->db->table($this->table)->where($where)->update($set);
        }

    }
?>