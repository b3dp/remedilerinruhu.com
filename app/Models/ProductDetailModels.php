<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class ProductDetailModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'product';
        }

        public function c_one ($where = array(), $find_in_set = '0') {
            $builder = $this->db->table('product p');
            $builder->select("p.*, pa.id AS pa_id, pa.barcode_no, b.id b_id, b.title b_title, b.slug b_slug, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR ' - ' ) AS attr "); 
            $builder->join("product_attribute pa", "p.id = pa.id_product", "left");
            $builder->join("brand b", "p.brand_id = b.id", "left");
            $builder->join("product_feature pf", "p.id = pf.product_id", "left");
            $builder->join("attribute a", "pf.attribute_id = a.id AND a.attribute_group_id IN('8', '6')", "left");
            $builder->join("attribute_group ag", "a.attribute_group_id = ag.id", "left");
            $builder->where($where);
            $builder->groupBy('p.id');
            $builder->orderBy('p.rank ASC, p.id DESC, ag.rank ASC');
            if ($find_in_set != 0 ) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", p.category_id)');
            }
            $builder = $builder->get()->getRow();
            return  $builder;
        }

        public function productCombinationOne ($where = array(), $filterWhereAttrIn = '', $findCombination = '') {

            if (isset($filterWhereAttrIn)) {
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQuery .= '(';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQuery .= 'FIND_IN_SET(\''.$value.'\' , attr_id) OR ';
                        }
                    }
                    $havingQuery = rtrim($havingQuery, 'OR ');
                    $havingQuery .= ') AND ';
                }
                $havingQuery = rtrim($havingQuery, ' AND ');
            }

            if ($findCombination) {
                $havingQueryFind .= '(';
                foreach ($findCombination as $key => $row) {
                    $havingQueryFind .= 'FIND_IN_SET(\''.$row.'\' , attr_id_where) AND ';
                }
                $havingQueryFind = rtrim($havingQueryFind, ' AND ');
                $havingQueryFind .= ')';
            }
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR " - ") AS attr, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id ASC SEPARATOR " - ") AS attr_id,  REPLACE(GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id ASC SEPARATOR ", "), " ", "") AS attr_id_where, GROUP_CONCAT(DISTINCT ag.title ORDER BY ag.id ASC  SEPARATOR " - ") AS attr_group, GROUP_CONCAT(DISTINCT ag.id ORDER BY ag.id ASC  SEPARATOR " - ") AS attr_group_id, pac.attribute_id');
            if (isset($filterWhereAttrIn)) {
                $builder->select(" REPLACE(GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR ', '), ' ', '') AS attr_slug ");
            }
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', 'left');
            $builder->join('attribute a', 'a.id = pac.attribute_id', 'left');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id', 'left');
            $builder->where($where);
            $builder->groupBy("pa.id");
            $builder->orderBy('pa.default_on  DESC, pa.id ASC');
            $builder->orderBy('ag.rank', 'ASC');
            if ($filterWhereAttrIn) {
                $builder->having($havingQuery);
            }
            if ($havingQueryFind) {
                $builder->having($havingQueryFind);
            }
            $query = $builder->get()->getRow();
            return $query;
        }

        public function productCombinationAll ($where = array()) {
            $builder = $this->db->table('product p');
            $builder->select('p.id, pa.id AS pa_id, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id ASC SEPARATOR ",") AS a_id, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR ",") AS title, GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR ",") AS slug');
            $builder->join('product_attribute pa', 'p.id = pa.id_product', 'left');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', 'left');
            $builder->join('attribute a', 'a.id = pac.attribute_id', 'left');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id', 'left');
            $builder->where($where);
            $builder->groupBy("p.id");
            $builder->orderBy('pa.default_on DESC, pa.id ASC');
            $query = $builder->get()->getRow();
            return $query;
        }

        public function attributePictureAll ($where = array(), $limit = '0') {
            $builder = $this->db->table("product_attribute_picture pap");
            $builder->select("pp.*, pap.product_image_id");
            $builder->join("product_picture pp", "pp.id = pap.product_image_id");
            $builder->groupBy("pp.id");
            $builder->orderBy("pp.is_cover DESC, pp.rank ASC");
            $builder->orderBy("pp.id ASC");
            $builder->where($where);
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function c_all_image ($where = array(), $limit = '0') {
            $builder = $this->db->table("product_picture");
            $builder->select("*");
            $builder->where($where);
            $builder->orderBy('is_cover DESC, rank ASC, id DESC');
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function sizePropCombinationFind ($where = array()) {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id DESC SEPARATOR " - ") AS attr, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id DESC SEPARATOR " - ") AS attr_id, GROUP_CONCAT(DISTINCT ag.title SEPARATOR " - ") AS attr_group, pac.attribute_id');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id');
            $builder->join('attribute a', 'a.id = pac.attribute_id');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->where($where);
            $builder->groupBy("pa.id");
            $builder->orderBy('a.rank ASC');
            $query = $builder->get()->getResult();
            return $query;
        }

        public function colorPropCombinationFind ($where = array()) {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id DESC SEPARATOR " - ") AS attr, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id DESC SEPARATOR " - ") AS attr_id, GROUP_CONCAT(DISTINCT ag.title SEPARATOR " - ") AS attr_group, pac.attribute_id');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', "left");
            $builder->join('attribute a', 'a.id = pac.attribute_id', "left");
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id AND ag.id = 5');
            $builder->where($where);
            $builder->groupBy("a.id");
            $builder->orderBy('pa.default_on  DESC, pa.id ASC');
            $builder->orderBy('ag.rank', 'ASC');
            $query = $builder->get()->getResult();
            return $query;
        }

        public function productCommentOne ($where = array(), $find_in_set = '0') {
            $builder = $this->db->table('product_comment pc');
            $builder->select("pc.*"); 
            $builder->join("product p", "p.id = pc.product_id");
            $builder->join("product_attribute pa", "p.id = pa.id_product AND pc.variant_id = pa.id", "left");
            $builder->where($where);
            $builder->groupBy('p.id');
            $builder->orderBy('p.rank ASC, p.id DESC');
            if ($find_in_set != 0 ) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", p.category_id)');
            }
            $builder = $builder->get()->getRow();
            return  $builder;
        }

        public function productCommentAll ($where = array(), $find_in_set = '0') {
            $builder = $this->db->table('product_comment pc');
            $builder->select("pc.*"); 
            $builder->join("product p", "p.id = pc.product_id", "left");
            $builder->join("product_attribute pa", "p.id = pa.id_product AND pc.variant_id = pa.id", "left");
            $builder->where($where);
            $builder->groupBy('p.id');
            $builder->orderBy('p.rank ASC, p.id DESC');
            if ($find_in_set != 0 ) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", p.category_id)');
            }
            $builder = $builder->get()->getResult();
            return  $builder;
        }

        public function productCommentAdd ($set = array()) {
            return $this->db->table('product_comment')->insert($set);
        }

        public function productCommentRemove ($where = array()) {
            return $this->db->table('product_comment')->where($where)->delete();
        }
        
    }
?>