# Hajusrakendused

## Projekti dokumentatsioon

### Kasutatud tehnoloogiad

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 + Inertia.js
- **CSS:** Tailwind CSS 4
- **Andmebaas:** SQLite (arenduses)
- **Autentimine:** Laravel Vue starter kit (Inertia — sisselogimine, registreerumine, Google OAuth valikuliselt)
- **Kaart:** Leaflet + OpenStreetMap
- **Ilma API:** OpenWeatherMap (`WEATHER_API` → `config/services.php`)
- **Makse:** Stripe (valikuline) + PayPal demo (simuleeritud serveripoolne voog)

### Rakenduse ülesehitus

Projekt on üles ehitatud Laravel + Inertia.js + Vue 3 arhitektuuriga. Moodulid (ilm, kaart, blog, e-pood, NFL rookied / API demo jms) on ühes rakenduses.

**Kaustad:**

- `app/Models/` — Eloquent mudelid (nt User, Post, Comment, Author, Marker, Product, Order, OrderItem, Review, MyFavoriteSubject — NFL rookied / API kirjed)
- `app/Http/Controllers/` — veebikontrollerid
- `app/Http/Controllers/Api/` — JSON API (nt rookied, sõbra API proxy)
- `database/migrations/` — migratsioonid
- `database/seeders/` — näidisandmed (blog, pood, rookied)
- `resources/js/pages/` — Vue leheküljed (nt `Ilm.vue`, `Kaart.vue`, `blog/`, `shop/`, `nfl-rookies/`, `cart/`, `checkout/`)
- `routes/web.php` — veebimaršruudid
- `routes/api.php` — API maršruudid

### Juhised koodi käivitamiseks

```bash
# 1. Klooni repositoorium
git clone <repo-url>
cd ta-24-frame-test

# 2. Paigalda PHP sõltuvused
composer install

# 3. Paigalda JS sõltuvused
npm install

# 4. Loo .env fail
cp .env.example .env

# 5. Genereeri rakenduse võti
php artisan key:generate

# 6. Loo SQLite andmebaas
touch database/database.sqlite

# 7. Käivita migratsioonid
php artisan migrate

# 8. Täida andmebaas näidisandmetega
php artisan db:seed

# 9. Loo storage link (failide jaoks)
php artisan storage:link

# 10. Lisa OpenWeatherMap API võti .env faili
# Registreeru: https://openweathermap.org/api
# Lisa (vt config/services.php → weather.key):
WEATHER_API=sinu_api_võti

# 11. Käivita arendusserverid
npm run dev
```

See käsk käivitab korraga PHP development serveri ja Vite (vaikimisi umbes `http://127.0.0.1:8001`; täpne port on `package.json` → skript `dev`).

Alternatiiv (käsitsi kaks terminali):

```bash
php artisan serve
npm run dev
```

### Tootmine / build

```bash
touch database/database.sqlite && php artisan migrate --force
npm run build
```

`npm run build` käivitab Wayfinderi ja Vite production buildi.
