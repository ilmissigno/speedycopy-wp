<?php
/**
 * My Account Navigation Override
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$endpoints = wc_get_account_menu_items();
?>
<nav class="sc-account-nav" aria-label="Navigazione account">
  <ul>
    <?php foreach( $endpoints as $endpoint => $label ) :
      $url = wc_get_account_endpoint_url( $endpoint );
      $classes = wc_get_account_menu_item_classes( $endpoint );
      // Aggiungo classe custom per styling card-like
      $classes .= ' sc-account-nav-item';
    ?>
      <li class="<?php echo esc_attr( $classes ); ?>">
        <a href="<?php echo esc_url( $url ); ?>">
          <span class="sc-account-nav-icon">
            <?php
            switch($endpoint){
              case 'dashboard': echo '🏠'; break;
              case 'orders': echo '📦'; break;
              case 'downloads': echo '⬇️'; break;
              case 'edit-address': echo '🏠'; break;
              case 'edit-account': echo '👤'; break;
              case 'customer-logout': echo '🚪'; break;
              default: echo '➡️';
            }
            ?>
          </span>
          <span class="sc-account-nav-label"><?php echo esc_html( $label ); ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
