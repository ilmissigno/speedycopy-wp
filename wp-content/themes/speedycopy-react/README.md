# SpeedyCopy React Theme

Tema WordPress custom creato per e-commerce di cancelleria con stile moderno.

## Funzionalità
- Layout responsive React-style
- Integrazione WooCommerce (shop, prodotto, carrello, checkout)
- Hero, categorie e prodotti in evidenza in homepage
- Componenti card uniformi
- Menu mobile con toggle
- Aggiornamento dinamico badge carrello

## Installazione
1. Copia la cartella `speedycopy-react` in `wp-content/themes/`.
2. Vai in Aspetto > Temi e attiva "SpeedyCopy React".
3. Imposta:
   - Pagina statica Home = pagina "Home" (assegna modello front-page automaticamente)
   - Pagina articoli = una pagina vuota (es. Blog)
4. In WooCommerce > Impostazioni > Avanzate verifica mapping: Carrello, Checkout, Il mio account, Shop.
5. Crea menu primario e footer e assegnali alle location.

## Personalizzazione
- Colori e variabili in `assets/css/app.css` nella sezione `:root`.
- Aggiungi nuovi override WooCommerce creando file dentro `woocommerce/`.

## Build / Dipendenze
Tema senza build tool. Se vuoi usare SCSS o bundler, aggiungi config (es. vite) e compila in `assets/css/app.css`.

## Note
Questo è un punto di partenza leggero. Espandi con pattern, blocchi Gutenberg personalizzati o JS avanzato secondo necessità.
