<?php 
    namespace App\Libraries;

    class Seo {
        public $general_site_title;
        public $general_site_keyword;
        public $general_site_desc;
        public $general_img;
        public $general_url;
        private $content;

        public function __construct($site_title, $site_desc, $site_img, $url, $site_keyword = '') {
            $this->general_site_title = $site_title;
            $this->general_site_keyword = $site_keyword;
            $this->general_site_desc = $site_desc;
            $this->general_img = $site_img;
            $this->general_url = $url;
        }

        public function seo($title, $desc = '', $url = '', $image = '', $noSiteTitle = '0', $keyword = '', $product = array()) {
            $site_title = $this->general_site_title;
        
            $site_title_two = $site_title;
            if ($desc) {
                $site_desc = $desc;
            }else{
                $site_desc = $this->general_site_desc;
            }
            if ($image) {
                $site_img = $this->general_url.$image;
            }else{
                $site_img = $this->general_url.$this->general_img;
            }
            if ($keyword) {
                $site_keyword = $keyword;
            }else{
                $site_keyword = $this->general_site_keyword;
            }
            $url = $this->general_url.$url;
           
            if ($title) {
                if ($noSiteTitle == '1') {
                    $title = $title;
                }else{
                    $title = $title . ' | ';
                }
            }else{
                $title = '';
            }
            if ($noSiteTitle == '1') {
                $site_title = '';
            }
            $this->content .= '<title>'.$title.''.$site_title .'</title>';
            $this->content .= '<meta name="description" content="'.$site_desc.'">'.PHP_EOL;
            if ($site_keyword) {
                $this->content .= '<meta name="keywords" content="'.$site_keyword.'">'.PHP_EOL;
            }

            $this->content .= '<meta itemprop="name" content="'.$title.''.$site_title .'">'.PHP_EOL;
            $this->content .= '<meta itemprop="description" content="'.$site_desc.'">'.PHP_EOL;
            $this->content .= '<meta itemprop="image" content="'.$site_img.'">'.PHP_EOL;

            $this->content .= '<meta name="twitter:card" content="summary_large_image">'.PHP_EOL;
            $this->content .= '<meta name="twitter:title" content="'.$title.''.$site_title .'">'.PHP_EOL;
            $this->content .= '<meta name="twitter:description" content="'.$site_desc.'">'.PHP_EOL;
            $this->content .= '<meta property="twitter:url" content="'.$url.'">'.PHP_EOL;
            $this->content .= '<meta name="twitter:site" content="@'.$site_title_two.'">'.PHP_EOL;
            $this->content .= '<meta name="twitter:creator" content="@biltstore">'.PHP_EOL;
            $this->content .= '<meta name="twitter:image:src" content="'.$site_img.'">'.PHP_EOL;

            $this->content .= '<meta name="og:title" content="'.$title.''.$site_title .'">'.PHP_EOL;
            $this->content .= '<meta name="og:description" content="'.$site_desc.'">'.PHP_EOL;
            $this->content .= '<meta name="og:image" content="'.$site_img.'">'.PHP_EOL;
            $this->content .= '<meta name="og:image:secure_url" content="'.$site_img.'">'.PHP_EOL;
            $this->content .= '<meta property="og:image:alt" content="'.$title.'" />'.PHP_EOL;
            $this->content .= '<meta property="og:image:type" content="image/jpeg" />'.PHP_EOL;
            $this->content .= '<meta property="og:url" content="'.$url.'">'.PHP_EOL;
            $this->content .= '<meta property="og:site_name" content="'.$site_title_two.'">'.PHP_EOL;
            $this->content .= '<meta name="og:locale" content="tr-TR">'.PHP_EOL;
            if ($product) {
                $this->content .= '<meta property="og:type" content="product">'.PHP_EOL;
                $this->content .= '<meta name="product:availability" content="instock">'.PHP_EOL;
                $this->content .= '<meta name="product:price:currency" content="'.$product['currency'].'">'.PHP_EOL;
                $this->content .= '<meta name="product:price:amount" content="'.$product['price'].'">'.PHP_EOL;
                $this->content .= '<meta name="product:brand" content="'.$product['brand'].'">'.PHP_EOL;
            }else{
                $this->content .= '<meta property="og:type" content="website">'.PHP_EOL;
            }

            return $this->content;
        }

        public function seo_1($seoArray) {
            $title = $seoArray['title'];
            $seo_title = $seoArray['seo_title'];
            $site_title = $seoArray['site_title'];
            $site_desc = $seoArray['site_desc'];
            $seo_desc = $seoArray['seo_desc'];
            $site_img = $seoArray['site_img'];
            $url = $seoArray['url'];

            if(!$title){
                $title = $seo_title .' - ';
            }
            if ($site_title == 0){
                $site_title = $this->general_site_title;
            }
            if($site_desc){
                $site_desc = $site_desc;
            }elseif($seo_desc){
                $site_desc = $seo_desc;
            }else{
                $site_desc = $this->general_site_desc;
            }
            if($site_img && file_exists($site_img)){
                $site_img = $this->general_url.$site_img;
            }else{
                $site_img = $this->general_url.$this->general_img;
            }

            $url = $this->general_url.$url;

            $content .= '<title>'.$title.$site_title .'</title>';
            if ($site_keyword) {
                $content .= '<meta name="keywords" content="'.$site_keyword.'">';
            }

            $content .= '<meta itemprop="name" content="'.$title.' | '.$site_title.'">';
            $content .= '<meta itemprop="description" content="'.$site_desc.'">';
            $content .= '<meta itemprop="image" content="'.$site_img.'">';

            $content .= '<meta name="twitter:card" content="summary_large_image">';
            $content .= '<meta name="twitter:title" content="'.$title.' | '.$site_title.'">';
            $content .= '<meta name="twitter:description" content="'.$site_desc.'">';
            $content .= '<meta property="twitter:url" content="'.$url.'">';
            $content .= '<meta name="twitter:site" content="@'.$site_title.'">';
            $content .= '<meta name="twitter:creator" content="@biltstore">';
            $content .= '<meta name="twitter:image:src" content="'.$site_img.'">';

            $content .= '<meta name="og:title" content="'.$title.' | '.$site_title.'">';
            $content .= '<meta name="og:description" content="'.$site_desc.'">';
            $content .= '<meta name="og:image" content="'.$site_img.'">';
            $content .= '<meta name="og:image:secure_url" content="'.$site_img.'">';
            $content .= '<meta property="og:image:alt" content="'.$title.'" />';
            $content .= '<meta property="og:image:type" content="image/jpeg" />';
            $content .= '<meta property="og:url" content="'.$url.'">';
            $content .= '<meta property="og:site_name" content="'.$site_title.'">';
            $content .= '<meta name="og:locale" content="tr-TR">';
            $content .= '<meta property="og:type" content="product">';

            return $content;
        }

    }

?>