<?php
// Theme setup
function speedycopy_react_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('custom-logo', [
        'height' => 80,
        'width'  => 80,
        'flex-width' => true,
        'flex-height'=> true,
    ]);
    register_nav_menus([
        'primary' => __('Menu Principale','speedycopy-react'),
        'footer'  => __('Menu Footer','speedycopy-react')
    ]);
}
add_action('after_setup_theme','speedycopy_react_setup');

// Enqueue assets
function speedycopy_react_assets() {
    $ver = '0.1.0';
    wp_enqueue_style('speedycopy-react-app', get_template_directory_uri() . '/assets/css/app.css', [], $ver);
    wp_enqueue_script('speedycopy-react-app', get_template_directory_uri() . '/assets/js/app.js', [], $ver, true);
}
add_action('wp_enqueue_scripts','speedycopy_react_assets');

add_action('wp_enqueue_scripts', function(){
  if( is_page_template('page-accesso.php') ){
    wp_enqueue_style('speedycopy-auth', get_stylesheet_directory_uri() . '/assets/css/auth.css', ['speedycopy-app'], filemtime(get_stylesheet_directory() . '/assets/css/auth.css'));
  }
});

// Container helper
function speedycopy_react_container_open($class='') { echo '<div class="sc-container '.esc_attr($class).'">'; }
function speedycopy_react_container_close() { echo '</div>'; }

// WooCommerce: rimuovo breadcrumb e sidebar default se presenti
add_action('init', function(){
    remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);
});

// Wrapper per contenuto WooCommerce
function speedycopy_react_wc_wrapper_start(){ echo '<div class="sc-wc-wrapper">'; }
function speedycopy_react_wc_wrapper_end(){ echo '</div>'; }
add_action('woocommerce_before_main_content','speedycopy_react_wc_wrapper_start',5);
add_action('woocommerce_after_main_content','speedycopy_react_wc_wrapper_end',50);

// Mini badge carrello nel menu
function speedycopy_react_cart_count($items, $args){
    if($args->theme_location === 'primary'){
        $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
        $items .= '<li class="menu-item sc-cart-mini"><a href="'.esc_url(wc_get_cart_url()).'">🛒 <span class="sc-cart-count">'.esc_html($count).'</span></a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items','speedycopy_react_cart_count',10,2);

// Aggiorna conteggio carrello via frammenti
function speedycopy_react_cart_fragment($fragments){
    ob_start();
    echo '<span class="sc-cart-count">'. ( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) .'</span>';
    $fragments['span.sc-cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments','speedycopy_react_cart_fragment');

// Nascondi categoria "Senza categoria" (uncategorized) dai listing e dal single
function speedycopy_react_hide_uncategorized_terms($terms, $taxonomies, $args){
    if(empty($terms) || empty($taxonomies)) return $terms;
    if(!in_array('product_cat',$taxonomies,true)) return $terms;
    $default_id = (int) get_option('default_product_cat'); // WooCommerce salva categoria default
    return array_filter($terms, function($t) use ($default_id){
        if(is_wp_error($t)) return false;
        if($default_id && (int)$t->term_id === $default_id) return false;
        if($t->slug === 'uncategorized') return false; // fallback
        return true;
    });
}
add_filter('get_terms','speedycopy_react_hide_uncategorized_terms',10,3);

// Rimuove la categoria anche dall'output delle categorie prodotto su single / loop
function speedycopy_react_filter_product_categories_list($html, $terms, $taxonomy, $query_vars, $term){
    // Non serve se già vuoto
    if(strpos($html,'uncategorized')===false) return $html;
    // Ricostruisci senza la categoria indesiderata
    $filtered = array_filter($terms, function($t){
        $default_id = (int) get_option('default_product_cat');
        if($default_id && (int)$t->term_id === $default_id) return false;
        return $t->slug !== 'uncategorized';
    });
    if(empty($filtered)) return '';
    $links = array();
    foreach($filtered as $t){
        $links[] = '<a href="'.esc_url(get_term_link($t)).'">'.esc_html($t->name).'</a>';
    }
    return implode(', ', $links);
}
add_filter('woocommerce_product_categories_widget_args', function($args){
    // Esclude la categoria default / uncategorized dal widget categorie
    $default_id = (int) get_option('default_product_cat');
    $exclude = array();
    if($default_id) $exclude[] = $default_id;
    $uncat = get_term_by('slug','uncategorized','product_cat');
    if($uncat && $uncat->term_id !== $default_id) $exclude[] = (int)$uncat->term_id;
    if(!empty($exclude)){
        $args['exclude'] = isset($args['exclude']) && $args['exclude'] ? array_merge((array)$args['exclude'],$exclude) : $exclude;
    }
    return $args;
},10,1);

// Filtra lista categorie su single product (hook woocommerce single meta)
add_filter('woocommerce_product_get_category_ids', function($ids, $product){
    $default_id = (int) get_option('default_product_cat');
    $uncat = get_term_by('slug','uncategorized','product_cat');
    return array_values(array_filter($ids, function($id) use ($default_id,$uncat){
        if($default_id && (int)$id === $default_id) return false;
        if($uncat && (int)$id === (int)$uncat->term_id) return false;
        return true;
    }));
},10,2);

// Fallback menu se non assegnato
function speedycopy_react_menu_fallback(){
    if ( current_user_can('edit_theme_options') ) {
        echo '<ul class="sc-fallback-menu"><li><a href="'.admin_url('nav-menus.php').'">Configura il menu &raquo;</a></li></ul>';
    }
}

// Admin notice se nessun menu primario
add_action('admin_notices', function(){
    if ( current_user_can('edit_theme_options') ) {
        $locations = get_nav_menu_locations();
        if ( empty($locations['primary']) ) {
            echo '<div class="notice notice-warning"><p><strong>SpeedyCopy React:</strong> nessun menu assegnato alla posizione "Menu Principale". Vai in <a href="'.admin_url('nav-menus.php').'">Aspetto → Menu</a>.</p></div>';
        }
    }
});
