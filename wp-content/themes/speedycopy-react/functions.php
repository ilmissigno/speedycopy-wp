<?php
// Theme setup
function speedycopy_react_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('custom-logo', [
        'height' => 120,
        'width'  => 120,
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
    $app_css_path = get_template_directory() . '/assets/css/app.css';
    $ver = file_exists($app_css_path) ? (string) filemtime($app_css_path) : '0.3.3';
    wp_enqueue_style(
        'speedycopy-fonts',
        get_template_directory_uri() . '/assets/css/fonts.css',
        [],
        $ver
    );
    wp_enqueue_style('speedycopy-react-app', get_template_directory_uri() . '/assets/css/app.css', ['speedycopy-fonts'], $ver);
    wp_enqueue_script('speedycopy-react-app', get_template_directory_uri() . '/assets/js/app.js', [], $ver, true);
}
add_action('wp_enqueue_scripts','speedycopy_react_assets');

add_action('wp_enqueue_scripts', function(){
  $is_auth = is_page_template('page-accesso.php')
    || ( function_exists('is_account_page') && is_account_page() && ! is_user_logged_in() );
  if ( $is_auth ) {
    $auth_css = get_stylesheet_directory() . '/assets/css/auth.css';
    wp_enqueue_style(
      'speedycopy-auth',
      get_stylesheet_directory_uri() . '/assets/css/auth.css',
      ['speedycopy-react-app'],
      file_exists($auth_css) ? filemtime($auth_css) : '0.1.1'
    );
  }
});

// Voce menu account: Accedi (guest) / Il mio account (loggato)
add_filter('wp_nav_menu_objects', function($items, $args){
  // Applica a primary e footer (stesso menu account)
  $loc = isset($args->theme_location) ? $args->theme_location : '';
  if ( $loc && ! in_array($loc, ['primary', 'footer'], true) ) {
    return $items;
  }
  $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '';
  if ( ! $account_url ) {
    return $items;
  }
  foreach ( $items as $item ) {
    if ( untrailingslashit($item->url) === untrailingslashit($account_url) ) {
      $item->title = is_user_logged_in() ? __('Il mio account', 'speedycopy-react') : __('Accedi', 'speedycopy-react');
    }
  }
  return $items;
}, 10, 2);

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
        $items .= '<li class="menu-item sc-cart-mini"><a href="'.esc_url(wc_get_cart_url()).'"><span class="sc-cart-label">'.esc_html__('Carrello','speedycopy-react').'</span> <span class="sc-cart-count">'.esc_html($count).'</span></a></li>';
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
    return array_values(array_filter($terms, function($t) use ($default_id){
        if (is_wp_error($t) || !is_object($t)) {
            return !is_wp_error($t);
        }
        if ($default_id && (int) $t->term_id === $default_id) {
            return false;
        }
        if (isset($t->slug) && $t->slug === 'uncategorized') {
            return false;
        }
        return true;
    }));
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


/**
 * Newsletter: frontend script + AJAX subscribe + admin list
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'speedycopy-newsletter',
        get_template_directory_uri() . '/assets/js/newsletter.js',
        [],
        '0.1.2',
        true
    );
    wp_localize_script('speedycopy-newsletter', 'scNewsletter', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('sc_newsletter'),
        'strings'  => [
            'ok'    => __('Iscrizione completata. Grazie!', 'speedycopy-react'),
            'error' => __('Impossibile completare l’iscrizione.', 'speedycopy-react'),
            'invalid' => __('Inserisci un’email valida e il consenso privacy.', 'speedycopy-react'),
        ],
    ]);
});

add_action('wp_ajax_sc_newsletter_subscribe', 'speedycopy_react_newsletter_subscribe');
add_action('wp_ajax_nopriv_sc_newsletter_subscribe', 'speedycopy_react_newsletter_subscribe');
function speedycopy_react_newsletter_subscribe() {
    check_ajax_referer('sc_newsletter', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $consent = ! empty($_POST['consent']);

    if (! is_email($email) || ! $consent) {
        wp_send_json_error(['message' => __('Email o consenso non validi.', 'speedycopy-react')], 400);
    }

    $list = get_option('sc_newsletter_subscribers', []);
    if (! is_array($list)) {
        $list = [];
    }

    $key = strtolower($email);
    if (isset($list[$key])) {
        wp_send_json_success(['message' => __('Sei già iscritto alla newsletter.', 'speedycopy-react')]);
    }

    $list[$key] = [
        'email'      => $email,
        'consented'  => 1,
        'created_at' => current_time('mysql'),
        'ip'         => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
        'source'     => 'footer',
    ];
    update_option('sc_newsletter_subscribers', $list, false);

    wp_send_json_success(['message' => __('Iscrizione completata. Grazie!', 'speedycopy-react')]);
}

add_action('admin_menu', function () {
    add_menu_page(
        __('Newsletter', 'speedycopy-react'),
        __('Newsletter', 'speedycopy-react'),
        'manage_options',
        'sc-newsletter',
        'speedycopy_react_newsletter_admin_page',
        'dashicons-email-alt',
        58
    );
});

function speedycopy_react_newsletter_admin_page() {
    if (! current_user_can('manage_options')) {
        return;
    }
    $list = get_option('sc_newsletter_subscribers', []);
    if (! is_array($list)) {
        $list = [];
    }
    echo '<div class="wrap"><h1>Newsletter SpeedyCopy</h1>';
    echo '<p>Iscritti con consenso privacy: <strong>' . esc_html(count($list)) . '</strong></p>';
    echo '<table class="widefat striped"><thead><tr><th>Email</th><th>Data</th><th>Fonte</th></tr></thead><tbody>';
    if (empty($list)) {
        echo '<tr><td colspan="3">Nessun iscritto ancora.</td></tr>';
    } else {
        foreach ($list as $row) {
            echo '<tr><td>' . esc_html($row['email']) . '</td><td>' . esc_html($row['created_at']) . '</td><td>' . esc_html($row['source']) . '</td></tr>';
        }
    }
    echo '</tbody></table></div>';
}


/**
 * Campi fiscali italiani (fatturazione) + meta ordine esposti via REST
 */
add_filter('woocommerce_billing_fields', function ($fields) {
    $fields['billing_fiscal_code'] = [
        'type'        => 'text',
        'label'       => __('Codice fiscale', 'speedycopy-react'),
        'placeholder' => 'RSSMRA80A01H501U',
        'required'    => false,
        'class'       => ['form-row-wide'],
        'priority'    => 35,
        'autocomplete'=> 'off',
    ];
    $fields['billing_vat_number'] = [
        'type'        => 'text',
        'label'       => __('Partita IVA', 'speedycopy-react'),
        'placeholder' => 'IT12345678901',
        'required'    => false,
        'class'       => ['form-row-wide'],
        'priority'    => 36,
        'autocomplete'=> 'off',
    ];
    $fields['billing_sdi_code'] = [
        'type'        => 'text',
        'label'       => __('Codice Destinatario (SDI)', 'speedycopy-react'),
        'placeholder' => 'XXXXXXX',
        'required'    => false,
        'class'       => ['form-row-first'],
        'priority'    => 37,
        'autocomplete'=> 'off',
        'maxlength'   => 7,
    ];
    $fields['billing_pec'] = [
        'type'        => 'email',
        'label'       => __('PEC (alternativa a SDI)', 'speedycopy-react'),
        'placeholder' => 'nome@pec.it',
        'required'    => false,
        'class'       => ['form-row-last'],
        'priority'    => 38,
        'autocomplete'=> 'off',
    ];
    $fields['billing_invoice_type'] = [
        'type'     => 'select',
        'label'    => __('Documento richiesto', 'speedycopy-react'),
        'required' => true,
        'class'    => ['form-row-wide'],
        'priority' => 34,
        'options'  => [
            'receipt' => __('Ricevuta / scontrino', 'speedycopy-react'),
            'invoice' => __('Fattura', 'speedycopy-react'),
        ],
        'default'  => 'receipt',
    ];
    return $fields;
});

add_action('woocommerce_checkout_process', function () {
    $type = isset($_POST['billing_invoice_type']) ? sanitize_text_field(wp_unslash($_POST['billing_invoice_type'])) : 'receipt';
    if ($type !== 'invoice') {
        return;
    }
    $vat = isset($_POST['billing_vat_number']) ? sanitize_text_field(wp_unslash($_POST['billing_vat_number'])) : '';
    $cf  = isset($_POST['billing_fiscal_code']) ? sanitize_text_field(wp_unslash($_POST['billing_fiscal_code'])) : '';
    $sdi = isset($_POST['billing_sdi_code']) ? sanitize_text_field(wp_unslash($_POST['billing_sdi_code'])) : '';
    $pec = isset($_POST['billing_pec']) ? sanitize_email(wp_unslash($_POST['billing_pec'])) : '';

    if ($vat === '' && $cf === '') {
        wc_add_notice(__('Per la fattura inserisci Partita IVA e/o Codice fiscale.', 'speedycopy-react'), 'error');
    }
    if ($sdi === '' && $pec === '') {
        wc_add_notice(__('Per la fattura elettronica indica Codice Destinatario SDI oppure PEC.', 'speedycopy-react'), 'error');
    }
});

add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    $map = [
        'billing_invoice_type' => '_billing_invoice_type',
        'billing_fiscal_code'  => '_billing_fiscal_code',
        'billing_vat_number'   => '_billing_vat_number',
        'billing_sdi_code'     => '_billing_sdi_code',
        'billing_pec'          => '_billing_pec',
    ];
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    foreach ($map as $post_key => $meta_key) {
        if (!isset($_POST[$post_key])) {
            continue;
        }
        $val = sanitize_text_field(wp_unslash($_POST[$post_key]));
        if ($post_key === 'billing_pec') {
            $val = sanitize_email(wp_unslash($_POST[$post_key]));
        }
        $order->update_meta_data($meta_key, $val);
    }
    $order->save();
});

add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $type = $order->get_meta('_billing_invoice_type');
    echo '<p><strong>Documento:</strong> ' . esc_html($type === 'invoice' ? 'Fattura' : 'Ricevuta') . '</p>';
    foreach ([
        '_billing_fiscal_code' => 'Codice fiscale',
        '_billing_vat_number'  => 'Partita IVA',
        '_billing_sdi_code'    => 'Codice SDI',
        '_billing_pec'         => 'PEC',
    ] as $key => $label) {
        $val = $order->get_meta($key);
        if ($val) {
            echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($val) . '</p>';
        }
    }
});

// Espone meta fiscali anche in REST API ordini (gestionale esterno)
add_filter('woocommerce_rest_prepare_shop_order_object', function ($response, $order) {
    if (!($response instanceof WP_REST_Response)) {
        return $response;
    }
    $data = $response->get_data();
    $data['speedycopy_fiscal'] = [
        'invoice_type' => $order->get_meta('_billing_invoice_type'),
        'fiscal_code'  => $order->get_meta('_billing_fiscal_code'),
        'vat_number'   => $order->get_meta('_billing_vat_number'),
        'sdi_code'     => $order->get_meta('_billing_sdi_code'),
        'pec'          => $order->get_meta('_billing_pec'),
    ];
    $response->set_data($data);
    return $response;
}, 10, 2);

// CSS checkout campi fiscali
add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }
    $css = '.woocommerce-billing-fields #billing_sdi_code_field,.woocommerce-billing-fields #billing_pec_field{clear:none;}';
    wp_add_inline_style('speedycopy-react-app', $css);
});


/**
 * Performance: trim frontend assets and WP chrome.
 */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
});

add_filter('emoji_svg_url', '__return_false');

add_action('wp_enqueue_scripts', function () {
    // SliceWP front CSS only on affiliate pages (tracking stays for referral capture).
    $affiliate_pages = array('diventa-affiliato', 'area-affiliati', 'reset-password-affiliato');
    $is_affiliate = is_page($affiliate_pages);
    if (!$is_affiliate) {
        wp_dequeue_style('slicewp-style');
        wp_deregister_style('slicewp-style');
    }

    // SliceWP tracking only when affiliate param is present (script exits early otherwise).
    $aff_key = function_exists('slicewp_get_setting') ? slicewp_get_setting('affiliate_keyword', 'aff') : 'aff';
    if (empty($_GET[$aff_key]) && empty($_GET['aff'])) {
        wp_dequeue_script('slicewp-script-tracking');
        wp_deregister_script('slicewp-script-tracking');
    }

    // WooCommerce styles not needed on pure marketing/home if no products rendered via blocks.
    if (function_exists('is_woocommerce')) {
        $needs_wc = is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_page('stampa-i-tuoi-documenti');
        if (!$needs_wc && !is_front_page()) {
            // Keep front page WC only if featured products use WC classes; front page does — skip dequeue there.
        }
        if (!$needs_wc && !is_front_page()) {
            wp_dequeue_style('woocommerce-general');
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
            wp_dequeue_style('wc-blocks-style');
        }
    }
}, 100);

add_action('wp_default_scripts', function ($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, array('jquery-migrate'));
    }
});


/**
 * Serve WebP variants when the browser supports them.
 */
function speedycopy_client_accepts_webp() {
    if (empty($_SERVER['HTTP_ACCEPT'])) {
        return false;
    }
    return false !== stripos($_SERVER['HTTP_ACCEPT'], 'image/webp');
}

function speedycopy_maybe_webp_url($url) {
    if (!$url || is_admin() || !speedycopy_client_accepts_webp()) {
        return $url;
    }
    $uploads = wp_get_upload_dir();
    if (empty($uploads['baseurl']) || strpos($url, $uploads['baseurl']) !== 0) {
        return $url;
    }
    $clean = preg_replace('/\?.*$/', '', $url);
    $path = str_replace($uploads['baseurl'], $uploads['basedir'], $clean);
    // Prefer foo.jpg.webp (nginx style); fallback foo.webp
    $candidates = array($path . '.webp', preg_replace('/\.(jpe?g|png)$/i', '.webp', $path));
    foreach ($candidates as $cpath) {
        if ($cpath && file_exists($cpath)) {
            return str_replace($uploads['basedir'], $uploads['baseurl'], $cpath) . (strpos($url, '?') !== false ? substr($url, strpos($url, '?')) : '');
        }
    }
    return $url;
}

add_filter('wp_get_attachment_url', 'speedycopy_maybe_webp_url');
add_filter('wp_calculate_image_srcset', function ($sources) {
    if (!is_array($sources) || !speedycopy_client_accepts_webp()) {
        return $sources;
    }
    foreach ($sources as $w => $source) {
        if (!empty($source['url'])) {
            $sources[$w]['url'] = speedycopy_maybe_webp_url($source['url']);
        }
    }
    return $sources;
});

/** Create WebP sibling on upload/resize. */
add_filter('wp_generate_attachment_metadata', function ($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    if (!$file || !file_exists($file)) {
        return $metadata;
    }
    speedycopy_create_webp_sibling($file);
    if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
        $dir = trailingslashit(dirname($file));
        foreach ($metadata['sizes'] as $size) {
            if (!empty($size['file'])) {
                speedycopy_create_webp_sibling($dir . $size['file']);
            }
        }
    }
    return $metadata;
}, 20, 2);

function speedycopy_create_webp_sibling($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg', 'jpeg', 'png'), true) || !function_exists('imagewebp')) {
        return;
    }
    $out = $path . '.webp';
    $alt = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    if (file_exists($out) && filemtime($out) >= filemtime($path)) {
        return;
    }
    if ($ext === 'png') {
        $im = @imagecreatefrompng($path);
    } else {
        $im = @imagecreatefromjpeg($path);
    }
    if (!$im) {
        return;
    }
    imagepalettetotruecolor($im);
    imagealphablending($im, true);
    imagesavealpha($im, true);
    if (@imagewebp($im, $out, 82) && $alt && $alt !== $out) {
        @copy($out, $alt);
    }
    imagedestroy($im);
}
