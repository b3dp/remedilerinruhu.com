<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class Category extends Model
    {
        protected $db;
        public $veri = array();

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'categories';
            $this->veri = array();
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array()) {
            return $this->db->table($this->table)->where($where)->orderBY('parent_id ASC')->get()->getResult();
        }

        public function c_all_list ($veri = 0, $kategori_ust = 0, $type = "product") {
            $query = $this->db->query("SELECT id, menuPicture, slug, title, parent_id FROM categories WHERE parent_id = $kategori_ust AND type = '$type' ")->getResult("array");
            $veriler = array();
            foreach ($query as $kategori) {
                if ($kategori['parent_id'] == $kategori_ust) {
                    $cocuk =  $this->c_all_list($veri, $kategori['id'], $type);
                    if ($cocuk){
                        $kategori['cocuk'] = $cocuk;
                    }else{
                        $kategori['cocuk'] = array();
                    }
                    $veriler[] = $kategori;
                }
            }
            return $veriler;
        }

        public function c_top_all_list ($veriler = '', $kategori_ust = 0, $i = 0) {
            $query = $this->db->query("SELECT * FROM categories WHERE id = $kategori_ust ")->getResult("array");
           
            if ($i != 0) {
                $this->veri[$i] = $veriler;
            }
           
            foreach ($query as $kategori) {
                if ($kategori['id'] == $kategori_ust && $kategori['parent_id'] != 0) {
                   
                    ++$i;
                    $veriler = $kategori;
                    $parrent =  $this->c_top_all_list($veriler, $kategori['parent_id'], $i);
                }else{
                    ++$i;
                    $this->veri[$i] = $kategori;
                }
            }
            return $this->veri;
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