<?php 

    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\ConnectionInterface;

    class OrderModels extends Model
    {
        protected $db;
        public $veri = array();

        function __construct(ConnectionInterface &$db)
        {
            $this->db =& $db;
            $this->table = 'product_order';
            $this->veri = array();
        }

        public function c_one ($where = array()) {
            return $this->db->table($this->table)->where($where)->get()->getRow();
        }

        public function c_all ($where = array(), $pagination = array()) {
            $builder = $this->db->table($this->table)->where($where)->orderBy('id DESC');
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            return $builder->get()->getResult();
        }

        public function orderList ($where = array(), $pagination = array()) {
            $builder = $this->db->table($this->table . ' o');
            $builder->select('o.*, u.full_name, u.email, u.phone, do.title');
            $builder->join('users u', 'o.user_id = u.id AND u.is_active = "1" ');
            $builder->join('delivery_options do', 'o.shipping_id = do.id');
            $builder->join('order_address_clone oac', 'o.shipping_address = oac.id');
            $builder->where($where);
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            return $builder->get()->getResult();
        }

        public function orderListPanel ($where = array(), $pagination = array()) {
            $builder = $this->db->table($this->table . ' o');
            $builder->select('o.*, SUM(odn.price) AS odn_price, odn.status AS odn_status, u.full_name, u.email, u.phone, do.title');
            $builder->join('product_order_detail_nebim odn', 'o.id = odn.order_id', 'left');
            $builder->join('users u', 'o.user_id = u.id AND u.is_active = "1" ', 'left');
            $builder->join('delivery_options do', 'o.shipping_id = do.id', 'left');
            $builder->join('order_address_clone oac', 'o.shipping_address = oac.id', 'left');
            $builder->where($where);
            $builder->orderBY('o.id DESC');
            $builder->groupBY('o.id');
            if ($pagination) {
                $builder->limit($pagination['whereStart'], $pagination['item']);
            }
            return $builder->get()->getResult();
        }
        
        public function orderCount ($where = array()) {
            $builder = $this->db->table($this->table . ' o');
            $builder->select('COUNT(o.id) AS orderCount, SUM(CASE WHEN o.discount_status = 1 THEN total_price_discount ELSE total_price END) AS total_price, discount_status ');
            $builder->join('users u', 'o.user_id = u.id AND u.is_active = "1" ');
            $builder->join('delivery_options do', 'o.shipping_id = do.id');
            $builder->join('order_address_clone oac', 'o.shipping_address = oac.id');
            $builder->where($where);
            return $builder->get()->getRow();
        }

        public function add ($set = array()) {
            return $this->db->table($this->table)->insert($set);
        }
        
        public function edit ($id, $set = array()) {
            return $this->db->table($this->table)->where("id", $id)->update($set);
        }

        public function count ($where = array()) {
            return $this->db->table($this->table)->where($where)->countAllResults();
        }

        public function editOrderNo ($id, $set = array()) {
            return $this->db->table($this->table)->where("order_no", $id)->update($set);
        }

        public function deleteRow ($id) {
            return $this->db->table($this->table)->where("id", $id)->delete();
        }

        public function orderDetailOne ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_detail')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getRow();
        }

        public function orderDetailNebimOne ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_detail_nebim')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getRow();
        }

        public function orderDetailAll ($where = array(), $limit = '0', $where2 = '') {
            $builder = $this->db->table('product_order_detail')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getResult();
        }

        public function orderDetailNebimSingle ($where = array(), $limit = '0', $where2 = '') {
            $builder = $this->db->table('product_order_detail_nebim');
            $builder->select('*');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($limit) {
                $builder->limit($limit);
            }
            return $builder->get()->getResult();
        }

        public function orderDetailNebimAll ($where = array(), $limit = '0', $where2 = '') {
            $builder = $this->db->table('product_order_detail_nebim');
            $builder->select('*, COUNT(product_order_detail_nebim.id) AS piece,
            COUNT(product_order_detail_nebim.cancellation_count) AS cancellation_count, 
            COUNT(product_order_detail_nebim.return_request_count) AS return_request_count, 
            COUNT(product_order_detail_nebim.return_count) AS return_count,
            COUNT(product_order_detail_nebim.return_request_cancel) AS return_request_cancel');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($limit) {
                $builder->limit($limit);
            }
            $builder->groupBy('product_order_detail_nebim.product_id, product_order_detail_nebim.variant_id, product_order_detail_nebim.`status` ');
            $builder->orderBy('product_order_detail_nebim.status ASC');
            return $builder->get()->getResult();
        }

        public function orderDetailNebimReport ($where = array(), $limit = '0', $where2 = '') {
            $builder = $this->db->table('product_order_detail_nebim');
            $builder->select('COUNT(product_order_detail_nebim.id) AS piece, 
            SUM(cancellation_count) AS cancellation_count, 
            SUM(return_request_count) AS return_request_count, 
            SUM(return_count) AS return_count');
            if ($where) {
                $builder->where($where);
            }
            if ($where2) {
                $builder->where($where2);
            }
            if ($limit) {
                $builder->limit($limit);
            }
            $builder->groupBy('product_order_detail_nebim.product_id, product_order_detail_nebim.variant_id');
            return $builder->get()->getRow();
        }
        
        public function orderDetailAdd ($set = array()) {
            return $this->db->table('product_order_detail')->insert($set);
        }

        public function orderDetailCombinationAdd ($set = array()) {
            return $this->db->table('product_order_detail_combination')->insert($set);
        }

        public function orderDetailNebimAdd ($set = array()) {
            return $this->db->table('product_order_detail_nebim')->insert($set);
        }

        public function orderDetailUpdate ($id, $set = array()) {
            return $this->db->table('product_order_detail')->where("id", $id)->update($set);
        }

        public function orderDetailNebimUpdate ($id, $set = array()) {
            return $this->db->table('product_order_detail_nebim')->where("id", $id)->update($set);
        }

        public function orderDetailUpdateWhere ($where, $set = array()) {
            return $this->db->table('product_order_detail')->where($where)->update($set);
        }

        public function orderDetailNebimUpdateWhere ($where, $set = array(), $limit = '') {
            $builder = $this->db->table('product_order_detail_nebim');
            if ($where) {
                $builder->where($where);
            }
            if ($limit) {
                $builder->limit($limit);
            }
            $builder->update($set);
            return $builder;
        }

        public function orderDetailDelete ($where = array()) {
            return $this->db->table('product_order_detail')->where($where)->delete();
        }
        public function orderDetailCombinationDelete ($where = array()) {
            return $this->db->table('product_order_detail_combination')->where($where)->delete();
        }

        public function orderDetailNebimDelete ($where = array()) {
            return $this->db->table('product_order_detail_nebim')->where($where)->delete();
        }

        public function delivery_c_one ($where = array()) {
            return $this->db->table('delivery_options')->where($where)->get()->getRow();
        }

        public function delivery_c_all ($where = array()) {
            return $this->db->table('delivery_options')->where($where)->orderBy('is_default DESC, id ASC')->get()->getResult();
        }

        public function delivery_edit ($where, $set = array()) {
            return $this->db->table('delivery_options')->where($where)->update($set);
        }
        
        public function orderReturnOne ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_return')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getRow();
        }

        public function orderReturnAll ($where = array(), $where2 = '', $pagination = array()) {
            $builder = $this->db->table('product_order_return')->where($where);
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getResult();
        }

        public function orderReturnCount ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_return')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->countAllResults();
        }
        public function orderReturnAdd ($set = array()) {
            return $this->db->table('product_order_return')->insert($set);
        }

        public function orderReturnUpdate ($id, $set = array()) {
            return $this->db->table('product_order_return')->where("id", $id)->update($set);
        }

        public function orderReturnDelete ($where = array()) {
            return $this->db->table('product_order_return')->where($where)->delete();
        }


        public function orderReturnDetailOne ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_return_detail')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getRow();
        }

        public function orderReturnDetailAll ($where = array(), $where2 = '', $pagination = array()) {
            $builder = $this->db->table('product_order_return_detail')->where($where);
            if ($pagination) {
                $builder->limit($pagination['item'], $pagination['whereStart']);
            }
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->get()->getResult();
        }

        public function orderReturnDetailCount ($where = array(), $where2 = '') {
            $builder = $this->db->table('product_order_return_detail')->where($where);
            if ($where2) {
                $builder->where($where2);
            }
            return $builder->countAllResults();
        }
        public function orderReturnDetailAdd ($set = array()) {
            return $this->db->table('product_order_return_detail')->insert($set);
        }

        public function orderReturnDetailUpdate ($id, $set = array()) {
            return $this->db->table('product_order_return_detail')->where("id", $id)->update($set);
        }
        public function orderReturnDetailWhereUpdate ($where = array(), $set = array()) {
            return $this->db->table('product_order_return_detail')->where($where)->update($set);
        }

        public function orderReturnDetailDelete ($where = array()) {
            return $this->db->table('product_order_return_detail')->where($where)->delete();
        }

    }
