# Custom Print Service — Redesign & Cart Fix Design

**Date:** 2026-08-03  
**Status:** Approved  
**Scope:** Plugin `wp-content/plugins/custom-print-service` + visual alignment with theme `speedycopy-react`

## Goal

Rendere il form di stampa allineato al tema SpeedyCopy (UI media redesign) e far funzionare l’aggiunta al carrello WooCommerce con upload reale del file e redirect al carrello.

## Decisions (locked)

| Decision | Choice |
|----------|--------|
| UI depth | Medium redesign (drag & drop, summary, IT copy, SpeedyCopy tokens) |
| After add to cart | Redirect to WooCommerce cart |
| Print options | File + B/N|Colore + quantità copie only (no duplex/format) |
| Implementation approach | Targeted patch of existing plugin (not React rewrite) |

## Current bugs (root causes)

1. Form POSTs to cart URL instead of AJAX `admin-ajax.php` with the actual file.
2. JS reads prices from `#custom-upload-form` but `data-bw-price` / `data-color-price` live on the parent `.custom-print-form`.
3. Option key mismatch: product stored as `custom_product_id`, one handler looks for `custom_print_product_id`.
4. Price option mismatch in dead handler (`custom_print_bw_price` vs `custom_bw_price`).
5. `custom_prevent_direct_add_to_cart` / `custom_remove_direct_added_product` can block legitimate adds.
6. `sold_individually` on the print product prevents multiple distinct print line items.
7. File is never uploaded/stored on successful “add”; only a filename string is passed.
8. Missing `assets/js/custom-print-service.js` referenced by enqueue.

## Functional flow

1. User drops/selects a document.
2. Client counts pages (PDF via PDF.js; other types estimated).
3. User picks B/N or Color and copy quantity.
4. Live summary shows file, pages, unit price, total.
5. Submit → AJAX `FormData` to `custom_add_to_cart`:
   - validate nonce, file type/size, page count, print type, quantity
   - store file under `uploads/print-files/`
   - ensure hidden virtual WooCommerce product exists (`custom_product_id`)
   - `WC()->cart->add_to_cart` with unique cart item data + `custom_price`
   - return `{ redirect_url: cart_url }`
6. Client redirects to cart.

## Cart / order data

Cart item meta:

- `custom_file` — stored filename/path key  
- `custom_original_name` — original upload name  
- `custom_page_count`  
- `custom_print_type` (`bw` | `color`)  
- `custom_price` — unit line price (pages × price_per_page; quantity is WC qty for copies)

Display in cart via `woocommerce_get_item_data`.  
Price via existing `woocommerce_before_calculate_totals` + session restore.  
On checkout, persist file path / meta on order for admin download; delete file when order completed (existing behavior kept).

Allow multiple print items: disable `sold_individually` or force unique cart keys per upload.

## UI design

Align with SpeedyCopy CSS variables:

- `--sc-primary` `#0b3d91`, `--sc-accent` `#d4af37`, `--sc-bg` `#f7faff`, `--sc-radius` ~18px, `.sc-btn`

Layout:

- Centered card (~640px)
- Italian defaults: title “Stampa il tuo documento”, CTA “Aggiungi al carrello”
- Drag & drop zone + file chip (name, pages, remove)
- Two selectable option cards for B/N vs Color with per-page price
- Quantity field
- Summary box with emphasized total
- Disabled CTA until file is valid; loading/error states

Responsive: single column on mobile.

## Error handling

- Unsupported / oversized file → clear message, resettable
- Page-count failure → block submit
- AJAX failure → stay on page with error, retryable
- Missing product → recreate or return clear error

## Out of scope

- Duplex / paper size options  
- React rewrite or theme-embedded mini-app  
- Changing admin settings IA beyond defaults/IT copy if needed  

## Success criteria

- PDF upload → correct pages → B/N and Color totals → item appears in cart with meta and correct price  
- Redirect to cart after success  
- UI matches SpeedyCopy look on desktop and mobile  
- File available for admin download on order  

## Files to touch

- `custom-print-service.php` — cart guards, product flags, cleanup  
- `includes/class-custom-print-service.php` — AJAX upload + add_to_cart  
- `includes/class-custom-print-form.php` — markup IT + SpeedyCopy structure  
- `includes/class-custom-print-product.php` — product creation flags, add_to_cart  
- `assets/js/custom-print-form.js` — drag/drop, AJAX FormData, redirect  
- `assets/css/custom-print-service.css` — SpeedyCopy-aligned styles  
- Remove dead/broken alternate add-to-cart handler paths; fix/remove missing JS enqueue
