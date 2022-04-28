<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class ProductCategoryModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'product';
        }

        public function c_one ($where = array(), $find_in_set = '0') {
            $builder = $this->db->table('product p');
            $builder->select("p.*, b.id b_id, b.title b_title, b.slug b_slug, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR ' - ' ) AS attr "); 
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

        public function c_all_old ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
           
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= 'HAVING (';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrIn) {
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQuery .= '(';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQuery .= 'FIND_IN_SET(\''.$value.'\' , REPLACE(GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR "," ), " ", "")) OR ';
                        }
                    }
                    $havingQuery = rtrim($havingQuery, 'OR ');
                    $havingQuery .= ') AND ';
                }
                $havingQuery = rtrim($havingQuery, ' AND ');
            }

            if ($filterWhereIn) {
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $brandQuery = rtrim($brandQuery, ',');
            }

            $builder = $this->db->table('product p');
            $builder->select("`p`.`id`, p.title, `p`.`category_id`, `p`.`campaign_id`, `p`.`brand_id`, `p`.`tax_rate`, `p`.`sale_price`, `p`.`discount_price`, `p`.`slug`, `p`.`basket_price`, `p`.`outlet_price`, b.id b_id, b.title b_title, b.slug b_slug, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR ' - ' ) AS attr"); 
            if ($filterWhereAttrCombineIn || $filterWhereAttrIn) {
                if (!$havingQuery) {
                    $havingQuery = 'GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR ", " )';
                }
                $builder->select("
                    REPLACE(CONCAT((SELECT REPLACE(GROUP_CONCAT(DISTINCT ac.slug ORDER BY ac.attribute_group_id ASC SEPARATOR ','), ' ','') AS attr_slug FROM `product_attribute` `pa` 
                    INNER JOIN product_attribute_combination AS pac ON pa.id = pac.attribute_product_id 
                    INNER JOIN `attribute` `ac` ON pac.attribute_id = `ac`.`id` 
                    WHERE pa.id_product = p.id GROUP BY pa.id 
                    ".$havingQueryCombine." LIMIT 1 ) , ',', CASE WHEN ". $havingQuery ." THEN GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR ',' ) ELSE 'NULL' END), ' ', '')
                    AS attr_slug
                ");
            }
            $builder->join("brand b", "p.brand_id = b.id", "left");
            $builder->join("product_feature pf", "p.id = pf.product_id", "left");
            $builder->join("product_attribute pa", "p.id = pa.id_product", "left");
            $builder->join("attribute_group ag", "pf.attribute_group_id = ag.id AND ag.is_combination = '0'", "left");
            $builder->join("attribute a", "pf.attribute_id = a.id AND ag.id = a.attribute_group_id", "left");
           
            $builder->where($where);
            $builder->groupBy('p.id');
            $builder->orderBy('p.rank ASC, p.id DESC, ag.rank ASC');
            if ($find_in_set) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", p.category_id)');
            }
            if ($brandQuery) {
                $builder->where('b.slug IN('.$brandQuery.')');
            }
            if ($filterWhereAttrCombineIn) {
                $builder->having('attr_slug IS NOT NULL ');
            }
            if ($filterWhereAttrIn) {
                $builder->having('attr_slug NOT LIKE "%NULL" ');
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder = $builder->get()->getResult();
            return  $builder;
        }

        public function c_all_new ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {

            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $categoryQuery .= ') ';
            }
            $builder = $this->db->table('category_count_table');
            $builder->select("*, (CASE WHEN c_discount THEN c_discount WHEN discount_rate THEN discount_rate ELSE 0 END) AS discount, (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price"); 
            $builder->where($where);
            if ($find_in_set) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", category_id)');
            }
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            
            if ($filterRank) {
                $builder->orderBy($filterRank);
            }else{
                $builder->orderBy('id DESC');
            }
            $builder->groupBy('id');
            $builder = $builder->get()->getResult();
            return  $builder;
        }

        public function count_new ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {

            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $categoryQuery .= ') ';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($find_in_set) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", category_id)');
            }

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            
            $builder->orderBy('id DESC');

            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $builder->groupBy('id');
            $query = $builder->countAllResults();
            return $query;
        }

        public function c_all_new_campaign ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {

            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }
            $builder = $this->db->table('category_count_table');
            $builder->select("*, (CASE WHEN c_discount THEN c_discount WHEN discount_rate THEN discount_rate ELSE 0 END) AS discount, (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price"); 
            $builder->where($where);
            if ($find_in_set) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", campaign_id)');
            }
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            
            if ($filterRank) {
                $builder->orderBy($filterRank);
            }else{
                $builder->orderBy('id DESC');
            }
            
            $builder = $builder->get()->getResult();
            return  $builder;
        }

        public function count_new_campaign ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {

            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($find_in_set) {
                $builder->where('FIND_IN_SET("'.$find_in_set.'", campaign_id)');
            }

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            
            $builder->orderBy('id DESC');

            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->countAllResults();
            return $query;
        }

        public function productCombinationOne ($where = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $pa_in = '') {

            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR  " - ") AS attr, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id ASC SEPARATOR " - ") AS attr_id, GROUP_CONCAT(DISTINCT ag.title ORDER BY ag.id ASC SEPARATOR " - ") AS attr_group , GROUP_CONCAT(DISTINCT ag.id ORDER BY ag.id ASC SEPARATOR " - ") AS attr_id_group');
            if ($filterWhereAttrCombineIn) {
                $builder->select(" REPLACE(GROUP_CONCAT(DISTINCT a.slug ORDER BY a.attribute_group_id ASC SEPARATOR ', '), ' ', '') AS attr_slug ");
            }
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', 'left');
            $builder->join('attribute a', 'a.id = pac.attribute_id', 'left');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id', 'left');
            $builder->where($where);
            if ($pa_in) {
                $pa_in = explode(', ', $pa_in);
                $builder->whereIn('pa.id', $pa_in);
            }
            $builder->groupBy("pa.id");
            $builder->orderBy('pa.default_on  DESC, pa.id ASC');
            $builder->orderBy('ag.rank', 'ASC');
            if ($filterWhereAttrCombineIn) {
                $builder->where($havingQueryCombine);
            }
            $query = $builder->get()->getRow();
            return $query;
        }

        public function attributePictureAll ($where = array(), $limit = '0') {
            $builder = $this->db->table("product_attribute_picture pap");
            $builder->select("pp.*, pap.product_image_id");
            $builder->join("product_picture pp", "pp.id = pap.product_image_id");
            $builder->groupBy("pp.id");
            $builder->orderBy("pp.is_cover DESC, pp.rank ASC, pp.id ASC");
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
            $builder->orderBy('pa.default_on  DESC, pa.id ASC');
            $builder->orderBy('ag.rank', 'ASC');
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

        public function sizePropFind ($where = array()) {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, a.title AS attr, a.id AS attr_id, a.rank AS attr_rank, ag.title AS attr_group, pac.attribute_id, p.slug');
            $builder->join('product p', 'p.id = pa.id_product');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id');
            $builder->join('attribute a', 'a.id = pac.attribute_id AND a.attribute_group_id = "4" ');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id');
            $builder->where($where);
            $builder->groupBy("pa.id");
            $builder->orderBy('pa.default_on  DESC, pa.id ASC');
            $builder->orderBy('ag.rank', 'ASC');
            $query = $builder->get()->getRow();
            return $query;
        }

        public function filterCombinationArea ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }   

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug');
           
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", category_id)');
            
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }

            $builder->orderBy('id DESC');
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterOrderArea ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }
            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }
            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }   

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug');
           
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", category_id)');
            
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }

            $builder->orderBy('id DESC');
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterBrandArea ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            } 

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, b_title, b_slug'); 
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", category_id)');

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            $builder->orderBy('id DESC');
            $builder->groupby('b_id');
            
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterGenderArea ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            } 

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_g_title, cat_g_slug');  
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", category_id)');

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            $builder->orderBy('id DESC');
            $builder->groupby('cat_g_slug');
            
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterCombinationAreaCampaign ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }
            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }
            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }   

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug');
           
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", campaign_id)');
            
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }

            $builder->orderBy('id DESC');
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterOrderAreaCampaign ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }
            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }
            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }
            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }   

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_g_title, cat_g_slug');   
           
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", campaign_id)');
            
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }

            $builder->orderBy('id DESC');
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterCategoryAreaCampaign ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            } 

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }
            
            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_title, cat_slug');  
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", campaign_id)');

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }

            $builder->orderBy('id DESC');
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterGenderAreaCampaign ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            } 

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    } 
                    if ($key == 'b.slug') {
                        foreach ($row as $item) {
                            $brandQuery .= '\''.$item.'\''. ',';;
                        }
                    }
                    
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }

            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_g_title, cat_g_slug');  
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", campaign_id)');

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            
            $builder->orderBy('id DESC');
            $builder->groupby('cat_g_slug');
            
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterBrandAreaCampaign ($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key == 'beden' || $key == 'renk') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                            }
                        }
                        $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                        $havingQueryCombine .= ') AND ';
                    }
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            }

            if ($filterWhereAttrCombineInBool) {
                $havingQueryCombine = '';
            }

            if ($filterWhereAttrIn) {
                if (!$havingQueryCombine) {
                    $havingQueryCombine .= '(';
                }else{
                    $havingQueryCombine .= 'AND (';
                }
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQueryCombine .= ' ( ';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQueryCombine = rtrim($havingQueryCombine, 'OR ');
                    $havingQueryCombine .= ') AND ';
                }
                $havingQueryCombine = rtrim($havingQueryCombine, ' AND ');
                $havingQueryCombine .= ') ';
            } 

            $filterWhereInBool = FALSE;
            if ($filterWhereIn) {
                $categoryQuery .= '(';
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'categories') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                    if ($key == 'gender') {
                        $filterWhereInBool = TRUE;
                        $categoryQuery .= ' ( ';
                        foreach ($row as $value) {
                                $categoryQuery .= 'FIND_IN_SET(\''.$value.'\' , cat_g_slug) OR ';
                        }
                        $categoryQuery = rtrim($categoryQuery, 'OR ');
                        $categoryQuery .= ') AND ';
                    }
                }
                $categoryQuery = rtrim($categoryQuery, ' AND ');
                $brandQuery = rtrim($brandQuery, ',');
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }
            $builder = $this->db->table('category_count_table');
            $builder->select('attr AS attr, attr_combination_slug AS attr_combination_slug, category_count_table.title AS pa_title, attr_slug AS attr_slug, b_title, b_slug');  
            if ($where) {
                $builder->where($where);
            }

            $builder->where('FIND_IN_SET("'.$id.'", campaign_id)');

            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }

            $builder->orderBy('id DESC');
            $builder->groupby('b_id');
            
            if ($limit) {
                if (is_array($limit)) {
                    $builder->limit($limit['item'], $limit['whereStart']);
                }else{
                    $builder->limit($limit);
                }
            }
            $query = $builder->get()->getResult();
            return $query;
        }
    }
?>