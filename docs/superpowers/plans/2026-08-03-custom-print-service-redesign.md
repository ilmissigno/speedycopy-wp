# Custom Print Service Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix WooCommerce add-to-cart for document uploads and restyle the `[custom_print_form]` UI to match SpeedyCopy React.

**Architecture:** Keep the existing PHP plugin. Client JS uploads via AJAX `FormData` to `custom_add_to_cart`, server stores the file, adds a hidden virtual product with custom cart meta and dynamic price, then returns the cart URL for redirect. CSS uses SpeedyCopy design tokens.

**Tech Stack:** WordPress, WooCommerce, jQuery, PDF.js, plugin PHP classes, SpeedyCopy CSS variables.

## Global Constraints

- UI: medium redesign — drag & drop, summary, Italian copy, SpeedyCopy tokens (`--sc-primary` `#0b3d91`, `--sc-accent` `#d4af37`, `--sc-bg` `#f7faff`, radius ~18px, `.sc-btn`)
- After success: redirect to WooCommerce cart
- Options only: file + B/N|color + quantity (no duplex/format)
- Product option key: `custom_product_id`
- Price option keys: `custom_bw_price`, `custom_color_price`
- Do not rewrite as React; patch existing plugin files
- No new markdown docs beyond this plan/spec unless asked

## File map

| File | Responsibility |
|------|----------------|
| `wp-content/plugins/custom-print-service/includes/class-custom-print-service.php` | AJAX upload + add_to_cart; cart item display; enqueue cleanup |
| `wp-content/plugins/custom-print-service/includes/class-custom-print-product.php` | Product create/update; add_to_cart with unique key; not sold_individually |
| `wp-content/plugins/custom-print-service/includes/class-custom-print-form.php` | Form markup IT + SpeedyCopy structure; script enqueue only |
| `wp-content/plugins/custom-print-service/custom-print-service.php` | Cart price hooks; allow form AJAX adds; remove broken guards |
| `wp-content/plugins/custom-print-service/assets/js/custom-print-form.js` | Drag/drop, page count, AJAX FormData, redirect |
| `wp-content/plugins/custom-print-service/assets/css/custom-print-service.css` | SpeedyCopy-aligned styles |

---

### Task 1: Fix product + cart backend (add to cart works)

**Files:**
- Modify: `wp-content/plugins/custom-print-service/includes/class-custom-print-product.php`
- Modify: `wp-content/plugins/custom-print-service/includes/class-custom-print-service.php`
- Modify: `wp-content/plugins/custom-print-service/custom-print-service.php`
- Modify: `wp-content/plugins/custom-print-service/includes/class-custom-print-form.php` (remove dead handler)

**Interfaces:**
- Consumes: `$_FILES['custom_file']`, `page_count`, `print_type`, `quantity`, nonce `custom_nonce`
- Produces: AJAX JSON `{ success, data: { redirect_url } }` or error; cart item with `custom_*` meta and `custom_price`

- [ ] **Step 1: Update `Custom_Print_Product::update_product`**

Set `$product->set_sold_individually(false);` keep virtual, hidden, price 0.

- [ ] **Step 2: Update `Custom_Print_Product::add_to_cart($file_data, $print_type, $quantity = 1)`**

```php
public function add_to_cart($file_data, $print_type, $quantity = 1) {
    if (!$this->product_id) {
        $this->product_id = $this->create_product();
    }
    $price = $print_type === 'color' ? $file_data['color_total'] : $file_data['bw_total'];
    $cart_item_data = array(
        'custom_file' => $file_data['file_name'],
        'custom_original_name' => $file_data['original_name'],
        'custom_page_count' => $file_data['page_count'],
        'custom_print_type' => $print_type,
        'custom_price' => $price,
        'unique_key' => md5($file_data['file_name'] . microtime(true)),
    );
    return WC()->cart->add_to_cart($this->product_id, max(1, (int) $quantity), 0, array(), $cart_item_data);
}
```

Also update `handle_checkout` to look up product via `get_option('custom_product_id')` if `$this->product_id` empty — make `handle_checkout` static-safe by reading option inside the method.

- [ ] **Step 3: Rewrite `Custom_Print_Service::handle_add_to_cart`**

Validate nonce; require `$_FILES['custom_file']`; sanitize print_type (`bw`|`color`), page_count >= 1, quantity >= 1; allowlisted extensions; max size 20MB; ensure print upload dir exists; `move_uploaded_file` into `get_print_upload_directory()` with `uniqid` prefix; compute totals from options `custom_bw_price` / `custom_color_price`; call `$this->print_product->add_to_cart(...)`; on success `wp_send_json_success(['redirect_url' => wc_get_cart_url()])`; on failure delete file and `wp_send_json_error`.

Also in `add_order_item_meta` save `_custom_original_name` and `_custom_price`, and store `_print_file_path` on order via existing checkout hook path (file absolute path in cart meta `custom_file` as full path).

Store **absolute path** in `custom_file` so admin download / checkout move works.

- [ ] **Step 4: Fix cart guards in `custom-print-service.php`**

In `custom_prevent_direct_add_to_cart` and `custom_remove_direct_added_product`, allow when:
- `$_POST['action'] === 'custom_add_to_cart'`, OR
- cart item data already has `custom_file` / `custom_price` (for WC internal adds from our handler — actually validation runs before cart item data is attached; so set a request flag in `handle_add_to_cart` before `add_to_cart`, e.g. `define('CUSTOM_PRINT_ADDING', true)` or `$GLOBALS['custom_print_adding'] = true`).

```php
function custom_prevent_direct_add_to_cart($passed, $product_id, $quantity) {
    $custom_product_id = get_option('custom_product_id');
    if ($product_id == $custom_product_id && empty($GLOBALS['custom_print_adding'])) {
        wc_add_notice(__('Questo prodotto può essere aggiunto solo tramite il form di stampa.', 'custom-print-service'), 'error');
        return false;
    }
    return $passed;
}
```

Same for remove handler.

- [ ] **Step 5: Remove dead code**

Delete `handle_custom_print_add_to_cart` and its `add_action` from `class-custom-print-form.php`.  
In `Custom_Print_Service::enqueue_scripts`, stop enqueueing missing `custom-print-service.js` (keep CSS only there, or enqueue CSS from form).  
Ensure form enqueue still runs for shortcode.

- [ ] **Step 6: Manual verify backend**

With shortcode on a page, use browser Network tab: AJAX should return success JSON with `redirect_url`. Or temporary curl with cookie/nonce if feasible.

- [ ] **Step 7: Commit** (if user asked / as plan checkpoint in plugin repo)

---

### Task 2: Redesign form markup (Italian + SpeedyCopy structure)

**Files:**
- Modify: `wp-content/plugins/custom-print-service/includes/class-custom-print-form.php`

**Interfaces:**
- Produces: HTML with classes `cps-form`, `cps-dropzone`, `cps-type-card`, `cps-summary`, `data-bw-price` / `data-color-price` on root `.custom-print-form`
- Form `id="custom-upload-form"`; file input `id="custom-file"` `name="custom_file"`; radios `name="print_type"`; quantity `name="quantity"`

- [ ] **Step 1: Replace `render_form` markup**

Italian defaults when options empty:
- Title: `Stampa il tuo documento`
- File label/description with accepted formats
- B/N / Colore with prices
- Quantity: `Copie`
- CTA: `Aggiungi al carrello`
- Summary labels in Italian

Structure:
- Root `.custom-print-form.cps-form` with price data attrs
- Dropzone wrapping file input
- File chip area `.cps-file-chip` (hidden until file)
- Type cards for bw/color
- Quantity
- Summary `.cps-summary` / `.preview-section`
- Submit `.sc-btn` (and `.cps-submit`)

Keep `wp_nonce_field` optional; AJAX will use localized nonce.

- [ ] **Step 2: Fix price data attributes**

Put `data-bw-price` and `data-color-price` on the same element JS reads, OR update JS to read from `.custom-print-form` — prefer both root attrs and JS reading `.custom-print-form`.

- [ ] **Step 3: Commit**

---

### Task 3: Rewrite frontend JS (drag/drop + AJAX + redirect)

**Files:**
- Modify: `wp-content/plugins/custom-print-service/assets/js/custom-print-form.js`

**Interfaces:**
- Consumes: `custom_form.ajax_url`, `custom_form.nonce`, `custom_form.strings.*`
- Produces: POST FormData with `action=custom_add_to_cart`, `nonce`, `custom_file`, `page_count`, `print_type`, `quantity`

- [ ] **Step 1: Rewrite JS**

Key behaviors:
- Read prices from `.custom-print-form` data attributes
- Drag/drop on `.cps-dropzone`
- Existing page-count logic (PDF.js, mammoth, etc.)
- Enable submit only after successful page count
- `submit` preventDefault → FormData from file input + fields → `$.ajax` with `processData:false`, `contentType:false`
- On success: `window.location = response.data.redirect_url`
- On error: show `.cps-error` message, re-enable button
- Localized strings: uploading, adding, error, invalid_file, remove (Italian)

- [ ] **Step 2: Expand `wp_localize_script` strings** in form class for IT messages.

- [ ] **Step 3: Manual verify** — select PDF, see summary, submit, land on cart with line item.

- [ ] **Step 4: Commit**

---

### Task 4: SpeedyCopy CSS

**Files:**
- Modify: `wp-content/plugins/custom-print-service/assets/css/custom-print-service.css`

- [ ] **Step 1: Replace stylesheet** using SpeedyCopy variables with fallbacks:

```css
.cps-form {
  --cps-primary: var(--sc-primary, #0b3d91);
  --cps-accent: var(--sc-accent, #d4af37);
  --cps-bg: var(--sc-bg, #f7faff);
  --cps-surface: var(--sc-surface, #fff);
  --cps-border: var(--sc-border, #d5dfec);
  --cps-radius: var(--sc-radius, 18px);
  --cps-text: var(--sc-text, #0e1a2b);
  --cps-muted: var(--sc-muted, #5a6775);
}
```

Style dropzone (dashed border, hover/dragover state), type cards (selected border primary), summary box, chip, error, mobile stack. Prefer `.sc-btn` if present; style `.cps-submit` equivalently.

- [ ] **Step 2: Visual check** desktop + narrow viewport.

- [ ] **Step 3: Commit**

---

### Task 5: End-to-end verification

- [ ] Confirm shortcode page loads assets (PDF.js + form JS + CSS)
- [ ] Upload PDF → pages shown → switch B/N/Color updates total
- [ ] Add to cart → redirect → cart shows file name, pages, print type, correct price × copies
- [ ] Second different upload can be added as second line item
- [ ] Fix any remaining issues found

---

## Spec coverage check

| Spec item | Task |
|-----------|------|
| AJAX FormData upload | 1, 3 |
| Option key unification | 1 |
| Server-side price | 1 |
| Cart guards / UNIQUE items | 1 |
| File storage print-files | 1 |
| Redirect to cart | 3 |
| Drag & drop + IT UI | 2, 3, 4 |
| SpeedyCopy tokens | 4 |
| No duplex/format | respected (omitted) |
