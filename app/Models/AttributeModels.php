<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class AttributeModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'attribute';
        }

        public function c_one ($where = array()) {
            $builder = $this->db->table($this->table. ' a');
            $builder->select('a.*, ag.id AS ag_id, ag.title AS ag_title, ag.slug AS ag_slug, ag.is_color');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->where($where);
            $builder->orderBY('is_active DESC');
            $query = $builder->get()->getRow();
            return $query;
        }

        public function lastRank ($where = array()) {
            $builder = $this->db->table($this->table. ' a');
            $builder->select('a.*, ag.id AS ag_id, ag.title AS ag_title');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->where($where);
            $builder->orderBY('a.rank DESC');
            $query = $builder->get()->getRow();
            return $query;
        }

        public function attribute_change_c_one ($where = array()) {
            $builder = $this->db->table('attribute_change');
            $builder->select('*');
            $builder->where($where);
            $query = $builder->get()->getRow();
            return $query;
        }

        public function c_all ($where = array(), $inWhere = '0', $inWhere2 = '0', $pagination = array()) {
            $builder = $this->db->table($this->table. ' a');
            $builder->select('a.*, ag.id AS ag_id, ag.title AS ag_title');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->join('attribute_categories ac', 'a.id = ac.attribute_id', 'left');
            $builder->where($where);
            if ($inWhere) {
                $builder->where($inWhere);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->groupBy("a.id");
            $builder->orderBY("a.rank ASC, a.title ASC, a.id DESC");
            $query = $builder->get()->getResult();
            return $query;
        }

        public function c_all_change ($where = array(), $inWhere = '0', $inWhere2 = '0', $pagination = array()) {
            $builder = $this->db->table($this->table. ' a');
            $builder->select('a.*, ag.id AS ag_id, ag.title AS ag_title');
            $builder->join('product_feature pf', 'a.id = pf.attribute_id');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->join('attribute_categories ac', 'a.id = ac.attribute_id', 'left');
            $builder->where($where);
            if ($inWhere != 0) {
                $builder->whereIN("ag.id", $inWhere);
                if ($inWhere2 != 0) {
                    $builder->whereIN("( ac.category_id", $inWhere2); 
                    $builder->orWhere("ac.category_id is NULL )", NULL); 
                }
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->where('a.is_active IN("1","99")');
            $builder->groupBy("a.id");
            $builder->orderBY("a.rank ASC, a.title ASC, a.id DESC");
            $query = $builder->get()->getResult();
            return $query;
        }

        public function c_all_list ($veri = 0, $kategori_ust = 0) {
            $query = $this->db->query("SELECT id, title, parent_id FROM categories WHERE parent_id = $kategori_ust ")->getResult("array");;
            $veriler = array();
            foreach ($query as $kategori) {
                if ($kategori['parent_id'] == $kategori_ust) {
                    $cocuk =  $this->c_all_list($veri, $kategori['id']);
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
        
        public function count ($where = array()) {
            $builder = $this->db->table($this->table. ' a');
            $builder->select('a.*, ag.id AS ag_id, ag.title AS ag_title');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->where($where);
            $query = $builder->countAllResults();
            return $query;
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function attribute_change_add ($set = array()) {
            return $this->db->table('attribute_change')->insert($set);
        }

        public function addCategory ($set = array()) {
            return $this->db->table("attribute_categories")->insert($set);
        }
        
        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function rank ($id, $value, $oldRank, $attribute_group_id, $status) {
            if ($status == '1') {
                $sql = "UPDATE attribute SET attribute.rank = (attribute.rank +1) WHERE attribute.rank BETWEEN ? AND ? AND attribute_group_id = ? AND id != ? ";
                $this->db->query($sql, [$value, $oldRank, $attribute_group_id, $id]);
            }else{
                $sql = "UPDATE attribute SET attribute.rank = (attribute.rank -1) WHERE attribute.rank BETWEEN ? AND ? AND attribute_group_id = ? AND id != ? ";
                $this->db->query($sql, [$oldRank, $value, $attribute_group_id, $id]);
            }
            
        }

        public function deleteRow ($id) {
            return $this->db->table($this->table)->where("id", $id)->delete();
        }

        public function deleteAttrCategory ($id) {
            return $this->db->table("attribute_categories")->where("id", $id)->delete();
        }
        
    }
?>