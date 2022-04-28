<aside class="navbar-aside" id="offcanvas_aside">
    <div class="aside-top">
        <a href="<?= base_url() ?>/panel" class="brand-wrap">
            <img src="<?= base_url() ?>/public/admin/assets/imgs/theme/logo.svg" class="logo" alt="Nest Dashboard" />
        </a>
        <div>
            <button class="btn btn-icon btn-aside-minimize"><i class="text-muted material-icons md-menu_open"></i></button>
        </div>
    </div>
    <nav>
        <ul class="menu-aside">
            <li class="menu-item <?= $sidebarActive == '' ? 'active' : '' ?>">
                <a class="menu-link" href="<?= base_url() ?>/panel">
                    <i class="icon material-icons md-home"></i>
                    <span class="text">Anasayfa</span>
                </a>
            </li>
            <li class="menu-item has-submenu <?= $sidebarActive == 'brand' || $sidebarActive == 'product' || $sidebarActive == 'categories' || $sidebarActive == 'attribute' ? 'active' : ''  ?> ">
                <a class="menu-link" href="page-products-list.html">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Ürün Yönetimi</span>
                </a>
                <div class="submenu">
                    <a class="<?= $sidebarActive == 'product' ? 'active' : '' ?>" href="product/list">Ürün Listesi</a>
                    <a class="<?= $sidebarActive == 'categories' ? 'active' : '' ?>" href="categories/list">Kategori</a>
                    <a class="<?= $sidebarActive == 'attribute' ? 'active' : '' ?>" href="attribute/group-list">Nitelik ve Ürün Özellikleri</a>
                    <a class="<?= $sidebarActive == 'brand' ? 'active' : '' ?>" href="brand/list">Marka</a>
                </div>
            </li>
            <li class="menu-item has-submenu <?= $sidebarActive == 'order' ? 'active' : '' ?>">
                <a class="menu-link" href="page-orders-1.html">
                    <i class="icon material-icons md-shopping_cart"></i>
                    <span class="text">Siparişler</span>
                </a>
                <div class="submenu">
                    <a href="order/all" class="<?= $sidebarAltActive == 'order/all' ? 'active' : '' ?>">Tüm Siparişler</a>
                </div>
            </li>
            <li class="menu-item <?= $sidebarActive == 'campaign' ? 'active' : '' ?>">
                <a class="menu-link" href="campaign/list">
                    <i class="icon material-icons md-campaign"></i>
                    <span class="text">Kampanyalar</span>
                </a>
            </li>
            <li class="menu-item <?= $sidebarActive == 'coupon' ? 'active' : '' ?>">
                <a class="menu-link" href="coupon/list">
                    <i class="icon material-icons md-card_giftcard"></i>
                    <span class="text">İndirim Kuponları</span>
                </a>
            </li>
            <li class="menu-item has-submenu <?= $sidebarActive == 'user' || $sidebarActive == 'managers' || $sidebarActive == 'sellers' ? 'active' : '' ?>">
                <a class="menu-link" href="page-sellers-cards.html">
                    <i class="icon material-icons md-store"></i>
                    <span class="text">Üye Yönetimi</span>
                </a>
                <div class="submenu">
                    <a class="<?= $sidebarAltActive == 'user/list' ? 'active' : '' ?>" href="user/list">Üyeler</a>
                    <a class="<?= $sidebarAltActive == 'managers/list' ? 'active' : '' ?>" href="managers/list">Yöneticiler</a>
                    <a class="<?= $sidebarAltActive == 'sellers/list' ? 'active' : '' ?>" href="sellers/list">Satıcılar</a>
                </div>
            </li>
            <li class="menu-item has-submenu <?= $sidebarActive == 'slider' || $sidebarActive == 'blog' || $sidebarActive == 'contracts' || $sidebarActive == 'faq' || $sidebarActive == 'about' ? 'active' : '' ?>">
                <a class="menu-link" href="page-sellers-cards.html">
                    <i class="icon material-icons md-content_copy"></i>
                    <span class="text">İçerik Yönetimi</span>
                </a>
                <div class="submenu">
                    <a class="<?= $sidebarAltActive == 'slider/list' ? 'active' : '' ?>" href="slider/list">Anasayfa Slider</a>
                    <a class="<?= $sidebarAltActive == 'blog/list' ? 'active' : '' ?>" href="blog/list">Blog Yönetimi</a>
                    <a class="<?= $sidebarAltActive == 'contracts/list' ? 'active' : '' ?>" href="contracts/list">Sözleşme Yönetimi</a>
                    <a class="<?= $sidebarAltActive == 'faq/group-list' ? 'active' : '' ?>" href="faq/group-list">Sıkça Sorulan Sorular</a>
                    <a class="<?= $sidebarAltActive == 'about/list' ? 'active' : '' ?>" href="about/list">Hakkımızda</a>
                </div>
            </li>
        </ul>
        <hr />
        <ul class="menu-aside">
            <li class="menu-item has-submenu <?= $sidebarActive == 'setting' ? 'active' : '' ?>">
                <a class="menu-link" href="#">
                    <i class="icon material-icons md-settings"></i>
                    <span class="text">Genel Ayarlar</span>
                </a>
                <div class="submenu">
                    <a class="<?= $sidebarAltActive == 'setting/general-setting' ? 'active' : '' ?>" href="setting/general-setting">Genel Site Ayarları</a>
                    <a class="<?= $sidebarAltActive == 'setting/contact-setting' ? 'active' : '' ?>" href="setting/contact-setting">İletişim Ayarları</a>
                    <a class="<?= $sidebarAltActive == 'setting/social-setting' ? 'active' : '' ?>" href="setting/social-setting">Sosyal Medya Ayarları</a>
                    <a class="<?= $sidebarAltActive == 'cargoSetting/list' ? 'active' : '' ?>" href="cargoSetting/list">Kargolar Ayarları</a>
                    <a class="<?= $sidebarAltActive == 'fixedFields/list' ? 'active' : '' ?>" href="fixedFields/list">Sabit Alanlar</a>
                </div>
            </li>
        </ul>
        <br />
        <br />
    </nav>
</aside>