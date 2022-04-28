<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class BrandModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'brand';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array(), $orderBY = '') {
            return $this->db->table($this->table)->where($where)->orderBY($orderBY)->get()->getResult();
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

        public function productNew ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {

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
                $builder->where('brand_id IN('.$find_in_set.')');
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

        public function productCountNew ($where = array(), $find_in_set = '0', $pagination = array(), $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '', $filterRank = '') {
            
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
                $builder->where('brand_id IN('.$find_in_set.')');
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

        public function c_all_index ($where = array(), $pagination = array()) {
            $builder = $this->db->table($this->table);
            $builder->select('*'); 
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            if ($where) {
                $builder->where($where);
            }
            $builder->orderBY('rank ASC, id DESC');
            return $builder->get()->getResult();
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

             $builder->where('brand_id IN('.$id.')');
            
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

        public function filterCategoryArea($where = array(), $id = '', $filterWhereAttrIn = '', $filterWhereAttrCombineIn = '', $filterWhereIn = '') {
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

            $builder->where('brand_id IN('.$id.')');

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

             $builder->where('brand_id IN('.$id.')');
            
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

             $builder->where('brand_id IN('.$id.')');

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

             $builder->where('brand_id IN('.$id.')');

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
    }
?>