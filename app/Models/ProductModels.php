<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class ProductModels extends Model
    {
        protected $db;

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'product';
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array(), $where2 = '', $pagination = '', $filterWhereIn = '', $groupBY = 'p.id') {

            if ($filterWhereIn) {
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'title') {
                        foreach ($row as $item) {
                            $whereQuery .= ' p.title LIKE "%'.$item.'%" OR pa.title LIKE "%'.$item.'%" OR pa.barcode_no LIKE "%'.$item.'%" ';
                        }
                    }
                }
                $whereQuery = rtrim($whereQuery, ',');
            }
            $builder =  $this->db->table($this->table.' p');
            $builder->select('p.*, pa.barcode_no, pa.stock');
            $builder->join('product_attribute pa', 'p.id = pa.id_product', 'left');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($whereQuery) {
                $builder->where($whereQuery);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            $builder->groupBy($groupBY);
            return  $builder->get()->getResult();
        }

        public function c_all_search_view ($where = array(), $where2 = '', $pagination = '', $filterWhereIn = '', $groupBY = 'p.id') {

            $builder =  $this->db->table('category_count');
            $builder->select('*');
            return  $builder->get()->getResult();
        }

        public function c_all_index ($where = array()) {

            $builder =  $this->db->table($this->table.' p');
            $builder->select('p.*, pa.barcode_no, pa.stock');
            $builder->join('product_attribute pa', 'p.id = pa.id_product', 'left');
            $builder->select('*');
            $builder->where($where);
            $builder->groupBy('pa.id');
            $builder->limit('8');
            $builder->orderBy('p.id DESC');
            return  $builder->get()->getResult();
        }

        public function c_one_search ($where = array()) {

            $builder =  $this->db->table('category_count_table');
            $builder->select('*');
            $builder->where($where);
            return  $builder->get()->getRow();
        }

        public function c_season ($where = array()) {
            return $this->db->table($this->table)->select('season')->where($where)->groupBY('season')->get()->getResult();
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
        
        public function count ($where = array(), $where2 = '', $filterWhereIn = '') {
            if ($filterWhereIn) {
                foreach ($filterWhereIn as $key => $row) {
                    if ($key == 'title') {
                        foreach ($row as $item) {
                            $whereQuery .= ' title LIKE "%'.$item.'%" ';
                        }
                    }
                }
                $whereQuery = rtrim($whereQuery, ',');
            }
            $builder =  $this->db->table($this->table);
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($whereQuery) {
                $builder->where($whereQuery);
            }
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            return $builder->countAllResults();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }

        public function add_search ($set = array()) {
            return $this->db->table('category_count_table')->insert($set);
        }

        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function edit_array ($where = array(), $set = array()) {
            return $this->db->table($this->table)->where($where)->update($set);
        }
        
        public function edit_search ($id = array(), $set = array()) {
            return $this->db->table('category_count_table')->where($id)->update($set);
        }

        public function editCampaign ($categoryArray = '', $brandArray = '', $seasonArray = '', $set = array()) {
            if ($categoryArray) {
                $queryCategory .= ' (';
                foreach ($categoryArray as $key => $row) {
                    $queryCategory .= ' FIND_IN_SET(\''.$row.'\' , category_id ) OR ';
                }
                    $queryCategory = rtrim($queryCategory, 'OR ');
                    $queryCategory .= ') AND ';
                $queryCategory = rtrim($queryCategory, ' AND ');
            }

            if ($brandArray) {
                $queryBrand .= ' (';
                foreach ($brandArray as $key => $row) {
                    $queryBrand .= ' FIND_IN_SET(\''.$row.'\' , brand_id ) OR ';
                }
                    $queryBrand = rtrim($queryBrand, 'OR ');
                    $queryBrand .= ') AND ';
                $queryBrand = rtrim($queryBrand, ' AND ');
            }
            if ($seasonArray) {
                $querySeason .= ' (';
                foreach ($seasonArray as $key => $row) {
                    $querySeason .= ' FIND_IN_SET(\''.$row.'\' , season ) OR ';
                }
                    $querySeason = rtrim($querySeason, 'OR ');
                    $querySeason .= ') AND ';
                $querySeason = rtrim($querySeason, ' AND ');
            }
            $builder = $this->db->table($this->table);
            if ($queryCategory) {
                $builder->where($queryCategory);
            }
            if ($queryBrand) {
                $builder->where($queryBrand);
            }
            if ($querySeason) {
                $builder->where($querySeason);
            }
            $builder->update($set);
            return $builder;
        }

        public function deleteRow ($id) {
            return $this->db->table($this->table)->where("id", $id)->delete();
        }

        public function delete_search ($id = array()) {
            return $this->db->table('category_count_table')->where($id)->delete();
        }
        ////////////////////////////// Product Price Models Area   //////////////////////////////////
        public function product_price_one ($where = array()) {
            return $this->db->table('product_price')->where($where)->get()->getRow();
        }

        public function product_price_all ($where = array()) {
            return $this->db->table('product_price')->where($where)->get()->getResult();
        }

        public function product_price_add ($set = array()) {
            return $this->db->table('product_price')->insert($set);
        }

        public function product_price_edit ($id = array(), $set = array()) {
            return $this->db->table('product_price')->where($id)->update($set);
        }
        ////////////////////////////// Product Picture Models Area //////////////////////////////////
        public function pictureAdd ($set = array()) {
            return $this->db->table("product_picture")->insert($set);
        }

        public function pictureEditSession ($upload_id, $set = array()) {
            return $this->db->table("product_picture")->where("upload_id", $upload_id)->update($set);
        }

        public function pictureEdit ($id, $set = array()) {
            return $this->db->table("product_picture")->where("id", $id)->update($set);
        }

        public function c_one_image ($where = array(), $orderBy = "\"is_cover\", \"DES\"") {
            return $this->db->table("product_picture")->where($where)->orderBy($orderBy)->limit("1")->get()->getRow();
        }

        public function c_all_image ($where = array()) {
            return $this->db->table("product_picture")->where($where)->orderBy('rank ASC, is_cover DESC, id DESC')->get()->getResult();
        }

        public function deleteImage ($id) {
            return $this->db->table("product_picture")->where("upload_id", $id)->orWhere("id", $id)->delete();
        }

        ////////////////////////////// Product Attribute Models Area //////////////////////////////////
        
        public function productAttrAll ($where = array(), $groupBY = "pa.id") {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.id, SUM(pa.stock) as totalStock, pa.barcode_no, pa.stock, pa.reference, pa.default_on, pa.id');
            $builder->where($where);
            $builder->groupBy($groupBY);
            $query = $builder->get()->getResult();
            return $query;
        }

        public function productCombinationOne ($where = array()) {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.id, pa.id_product, pa.reference, pa.default_on, pa.id, GROUP_CONCAT(DISTINCT a.title SEPARATOR " - ") AS attr, GROUP_CONCAT(DISTINCT ag.title SEPARATOR " - ") AS attr_group, pac.attribute_id');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', 'left');
            $builder->join('attribute a', 'a.id = pac.attribute_id', 'left');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id', 'left');
            $builder->where($where);
            $builder->groupBy("pa.id");
            $builder->orderBy('ag.rank', 'ASC');
            $query = $builder->get()->getRow();
            return $query;
        }

        public function productCombinationAll ($where = array(), $groupBY = "pa.id") {
            $builder = $this->db->table('product_attribute pa');
            $builder->select('pa.*, GROUP_CONCAT(DISTINCT a.title ORDER BY a.attribute_group_id ASC SEPARATOR  " - ") AS attr, GROUP_CONCAT(DISTINCT a.id ORDER BY a.attribute_group_id ASC SEPARATOR " - ") AS attr_id, GROUP_CONCAT(DISTINCT ag.title ORDER BY ag.id ASC SEPARATOR " - ") AS attr_group , GROUP_CONCAT(DISTINCT ag.id ORDER BY ag.id ASC SEPARATOR " - ") AS attr_id_group');
            $builder->join('product_attribute_combination pac', 'pac.attribute_product_id = pa.id', 'left');
            $builder->join('attribute a', 'a.id = pac.attribute_id', 'left');
            $builder->join('attribute_group ag', 'ag.id = a.attribute_group_id', 'left');
            $builder->where($where);
            $builder->groupBy($groupBY);
            $builder->orderBy('ag.rank', 'ASC');
            $query = $builder->get()->getResult();
            return $query;
        }

        public function productAttrInsert ($set = array()) {
            return $this->db->table('product_attribute')->insert($set);
        }

        public function productAttrUpdate ($where = array(), $set = array()) {
            return $this->db->table('product_attribute')->where($where)->update($set);
        }
        public function productAttrUpdate2 ($where = '', $set = array()) {
            return $this->db->table('product_attribute')->where('rand != '.$where.'')->update($set);
        }
        public function productAttrUpdate3 ($where = '', $id = '', $set = array()) {
            return $this->db->table('product_attribute')->where('rand != '.$where.'')->where('id_product = '.$id.'')->update($set);
        }

        public function productCombinationUpdate ($where = array(), $set = array()) {
            return $this->db->table('product_attribute')->where($where)->update($set);
        }
        
        public function orderCombinationColorSelect ($where = array()) {
            $builder = $this->db->table("product_attribute pa");
            $builder->select("pa.*");
            $builder->join("product_attribute_combination pac", "pa.id = pac.attribute_product_id", "left");
            $builder->join("attribute a", "pac.attribute_id = a.id", "left");
            $builder->join("attribute_group ag", "ag.id = a.attribute_group_id", "left");
            $builder->groupBy("pa.id");
            $builder->where($where);
            $query = $builder->get()->getResult();
            return $query;
        }

        public function productAttributeDelete ($id) {
            return $this->db->table('product_attribute')->where("id", $id)->delete();
        }
        public function productAttributeDeleteWhere ($id = array() ) {
            return $this->db->table('product_attribute')->where($id)->delete();
        }

        public function attributePictureAll ($where = array()) {
            $builder = $this->db->table("product_attribute_picture pap");
            $builder->select("pp.id, pp.image, pp.is_cover, pap.product_image_id");
            $builder->join("product_picture pp", "pp.id = pap.product_image_id");
            $builder->groupBy("pp.id");
            $builder->orderBy("pp.is_cover DESC, pp.rank ASC, pp.id DESC");
            $builder->where($where);
            $query = $builder->get()->getResult();
            return $query;
        }
        
        public function attributePictureSelect ($set = array()) {
            return $this->db->table('product_attribute_picture')->insert($set);
        }
        
        public function attributePictureDelete ($where = array()) {
            return $this->db->table('product_attribute_picture')->where($where)->delete();
        }
        
        ////////////////////////////// Product Attribute Combination Models Area //////////////////////////////////
        public function productattributecombination_c_one ($where = array()) {
            return $this->db->table("product_attribute_combination")->where($where)->get()->getRow();
        }
        public function productattributecombination_c_all ($where = array()) {
            return $this->db->table("product_attribute_combination")->where($where)->get()->getResult();
        }
        public function productattributecombination ($set = array()) {
            return $this->db->table('product_attribute_combination')->insert($set);
        }

        public function productattributecombinationUpdate ($where = array(), $set = array()) {
            return $this->db->table('product_attribute_combination')->where($where)->update($set);
        }

        public function productCombinationDelete ($id = array()) {
            $builder = $this->db->table('product_attribute_combination');
            $builder->whereIn("id", $id);
            $query = $builder->delete();
            return $query;
        }

        ////////////////////////////// Product Attribute Combination Models Area //////////////////////////////////
        public function brandAll ($where = array()) {
            return $this->db->table("brand")->where($where)->get()->getResult();
        }

        ////////////////////////////// Product Shopping Centre Models Area //////////////////////////////////
        public function shoppingCenterAll ($where = array()) {
            return $this->db->table("shopping_centre")->where($where)->get()->getResult();
        }

        public function prodcutShoppingCenterAdd ($set = array()) {
            return $this->db->table('product_shopping_centre')->insert($set);
        }

        ///////////////////////////////// Sitemap Ürünleri ////////////////////////////////////////

        public function c_all_sitemap_product ($where = array(), $cimri_sezon = '') {

            $builder =  $this->db->table('category_count_table cct');
            $builder->select('cct.*, GROUP_CONCAT(c.title ORDER BY c.id SEPARATOR " > ") AS category_name, b.title AS b_title, GROUP_CONCAT(c.is_cimri ORDER BY c.id SEPARATOR ",") AS is_cimri, GROUP_CONCAT(c.is_akakce ORDER BY c.id SEPARATOR ",") AS is_akakce, , (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price');
            $builder->join('categories AS c', 'FIND_IN_SET(c.id, cct.category_id) ');
            $builder->join('brand AS b', 'b.id = cct.brand_id AND b.is_active = "1" ');
            $builder->where($where);
            $builder->groupBy('pa_id');
            return  $builder->get()->getResult();
        }

        public function c_all_sitemap_cimri ($where = array(), $cimri_sezon = '') {

            $builder =  $this->db->table('category_count_table cct');
            $builder->select('cct.*, GROUP_CONCAT(c.title ORDER BY c.id SEPARATOR " > ") AS category_name, b.title AS b_title, REPLACE(GROUP_CONCAT(c.is_cimri ORDER BY c.id SEPARATOR ","), " ", "") AS is_cimri, (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price');
            $builder->join('categories AS c', 'FIND_IN_SET(c.id, cct.category_id) ');
            $builder->join('brand AS b', 'b.id = cct.brand_id AND b.is_active = "1" AND b.is_cimri = "1"');
            $builder->where($where);
            if ($cimri_sezon) {
                $builder->where('season IN('. $cimri_sezon .')');
            }
            $builder->groupBy('pa_id');
            $builder->having('!FIND_IN_SET("0", is_cimri)');
            return  $builder->get()->getResult();
        }

        public function c_all_sitemap_akakce ($where = array(), $cimri_sezon = '') {

            $builder =  $this->db->table('category_count_table cct');
            $builder->select('cct.*, GROUP_CONCAT(c.title ORDER BY c.id SEPARATOR " > ") AS category_name, b.title AS b_title, REPLACE(GROUP_CONCAT(c.is_akakce ORDER BY c.id SEPARATOR ","), " ", "") AS is_akakce, , (CASE WHEN campaign_price THEN campaign_price WHEN basket_price THEN basket_price WHEN discount_price THEN discount_price WHEN sale_price THEN sale_price END ) AS last_price');
            $builder->join('categories AS c', 'FIND_IN_SET(c.id, cct.category_id) ');
            $builder->join('brand AS b', 'b.id = cct.brand_id AND b.is_active = "1" AND b.is_akakce = "1"');
            $builder->where($where);
            if ($cimri_sezon) {
                $builder->where('season IN('. $cimri_sezon .')');
            }
            $builder->groupBy('pa_id');
            $builder->having('!FIND_IN_SET("0", is_akakce)');
            return  $builder->get()->getResult();
        }

        public function c_all_mubiko ($where = array(), $cimri_sezon = '') {

          
            $builder =  $this->db->table($this->table.' p');
            $builder->select('pa.title, pa.barcode_no, pa.stock, REPLACE(GROUP_CONCAT(c.is_mubiko ORDER BY c.id SEPARATOR ","), " ", "") AS is_mubiko');
            $builder->join('product_attribute pa', 'p.id = pa.id_product');
            $builder->join('categories AS c', 'FIND_IN_SET(c.id, p.category_id) ');
            $builder->join('brand AS b', 'b.id = p.brand_id AND b.is_active = "1"');
            $builder->where($where);
            if ($cimri_sezon) {
                $builder->where('season IN('. $cimri_sezon .')');
            }
            $builder->groupBy('pa.id');
            $builder->having('!FIND_IN_SET("0", is_mubiko)');
            return  $builder->get()->getResult();
        }
    }
?>