<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class SearchModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
        }

        public function searchBrand ($where = array(), $where2 = '', $orderBy = 'b.rank ASC, b.id DESC', $limit = '', $filterWhereAttrIn = '', $orderBySelect = '1') {

            if ($filterWhereAttrIn) {
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQuery .= '(';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQuery .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQuery = rtrim($havingQuery, 'OR ');
                    $havingQuery .= ') AND ';
                }
                $havingQuery = rtrim($havingQuery, ' AND ');
            }
            $builder = $this->db->table('brand b');
            $builder->select(' '.$orderBySelect.' AS score, b.*,GROUP_CONCAT(DISTINCT a.slug) AS attr_slug');
            $builder->join('product p', 'b.id = p.brand_id', 'left');
            $builder->join('product_feature  pf', 'p.id = pf.product_id', 'left');
            $builder->join('attribute a', 'pf.attribute_id = a.id', 'left');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($havingQuery) {
                $builder->having($havingQuery);
            }
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->groupBy('b.id');
            $builder->orderBy($orderBy);
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function filterSizeArea ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }
            $filterWhereAttrCombineInBool = TRUE;
            if ($filterWhereAttrCombineIn) {
                $havingQueryCombine .= '(';
                foreach ($filterWhereAttrCombineIn as $key => $row) {
                    if ($key != 'beden') {
                        $filterWhereAttrCombineInBool = FALSE;
                        $havingQueryCombine .= ' ( ';
                        foreach ($row as $item) {
                            foreach ($item as $value) {
                                if ($key == 'renk') {
                                    $havingQueryCombine .= 'FIND_IN_SET(\''.$value.'\' , attr_color) OR ';
                                }
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
            $builder->select(''. $orderBySelect .' AS score, attr AS attr, attr_size_in AS attr_size_in, attr_color AS attr_color, category_count_table.title AS pa_title, attr_slug AS attr_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
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
            
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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

        public function filterOrderArea ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
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
            $builder->select(''. $orderBySelect .' AS score, attr AS attr, attr_size_in AS attr_size_in, attr_color AS attr_color, category_count_table.title AS pa_title, attr_slug AS attr_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
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
            
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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
        
        public function filterCategoryArea ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
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
            $builder->select(''. $orderBySelect .' AS score, attr AS attr, attr_size_in AS attr_size_in, attr_color AS attr_color, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_title, cat_slug '); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
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

            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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

        public function filterGenderArea ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
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
            $builder->select(''. $orderBySelect .' AS score, attr AS attr, attr_size_in AS attr_size_in, attr_color AS attr_color, category_count_table.title AS pa_title, attr_slug AS attr_slug, cat_g_title, cat_g_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
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

            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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

        public function filterBrandArea ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
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
                $categoryQuery .= ') ';
            }

            if (!$filterWhereInBool) {
                $categoryQuery = '';
            }
            $builder = $this->db->table('category_count_table');
            $builder->select(''. $orderBySelect .' AS score, attr AS attr, attr_size_in AS attr_size_in, attr_color AS attr_color, category_count_table.title AS pa_title, attr_slug AS attr_slug, b_title, b_slug'); 
           
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
            }
            
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }

            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }
            
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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

        public function searchColor ($where = array(), $where2 = '', $orderBy = 'a.rank ASC, a.id DESC', $limit = '', $filterWhereAttrIn = '') {

            if ($filterWhereAttrIn) {
                foreach ($filterWhereAttrIn as $key => $row) {
                    $havingQuery .= '(';
                    foreach ($row as $item) {
                        foreach ($item as $value) {
                            $havingQuery .= 'FIND_IN_SET(\''.$value.'\' , attr_slug) OR ';
                        }
                    }
                    $havingQuery = rtrim($havingQuery, 'OR ');
                    $havingQuery .= ') AND ';
                }
                $havingQuery = rtrim($havingQuery, ' AND ');
            }

            $builder = $this->db->table('attribute a');
            $builder->select('a.*, GROUP_CONCAT(DISTINCT a.slug) AS attr_slug');
            $builder->join("product_feature pf", "a.id = pf.attribute_id", "left");
            $builder->join('product_attribute AS pa', 'pf.product_attribute_id = pa.id', 'left');
            $builder->join('product p', 'pf.product_id = p.id');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($havingQuery) {
                $builder->having($havingQuery);
            }
            $builder->groupBy('a.id');
            $builder->orderBy($orderBy);
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function searchCategories ($where = array(), $where2 = '', $orderBy = 'c.rank ASC, c.id DESC', $limit = '') {
            $builder = $this->db->table('categories c');
            $builder->select('c.*');
            $builder->join('product p', 'FIND_IN_SET(c.id, p.category_id)');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            $builder->groupBy('c.id');
            $builder->orderBy($orderBy);
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function searchCamping ($where = array(), $where2 = '', $orderBy = 'cp.rank ASC, cp.id DESC', $limit = '') {
            $builder = $this->db->table('campaign cp');
            $builder->select('cp.*');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            $builder->where("((cp.start_at != 'NULL' AND cp.start_at <= '".nowDate()."') OR cp.start_at IS NULL)");
            $builder->where("((cp.end_at != 'NULL' AND cp.end_at >= '".nowDate()."') OR cp.end_at IS NULL)");
            $builder->groupBy('cp.id');
            $builder->orderBy($orderBy);
            if ($limit) {
                $builder->limit($limit);
            }
            $query = $builder->get()->getResult();
            return $query;
        }

        public function searchProductNew ($where = array(), $where2 = '', $orderBy = 'id DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1', $filterRank = '') {
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }

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
            $builder->select("*, (CASE WHEN c_discount THEN c_discount WHEN discount_rate THEN discount_rate ELSE 0 END) AS discount, (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price, ". $orderBySelect ." AS score"); 
            
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            if ($filterRank) {
                $builder->orderBy($orderBy.','.$filterRank);
            }else{
                $builder->orderBy($orderBy);
            }
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

        public function searchProductCountNew ($where = array(), $where2 = '', $orderBy = 'score DESC', $limit = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $orderBySelect = '1') {
           
            if (!$orderBySelect) {
                $orderBySelect = '0';
            }
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
            $builder->select(" ". $orderBySelect ." AS score, attr_slug AS attr_slug"); 
            
            if ($where) {
                $builder->where($where);
            }

            if ($where2) {
                $builder->where($where2);
            }

            if ($brandQuery) {
                $builder->where('b_slug IN('.$brandQuery.')');
            }
            if ($havingQueryCombine) {
                $builder->where($havingQueryCombine);
            }
            if ($categoryQuery) {
                $builder->where($categoryQuery);
            }
            if ($orderBySelect) {
                $builder->having('score > 0');
            }
            $builder->orderBy($orderBy);
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