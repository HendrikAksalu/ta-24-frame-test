# Hajusrakendused

## Projekti dokumentatsioon

### Kasutatud tehnoloogiad
- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Vue 3 + Inertia.js
- **CSS:** Tailwind CSS 3
- **Andmebaas:** SQLite (arenduses)
- **Autentimine:** Laravel Breeze
- **Kaart:** Leaflet + OpenStreetMap
- **Ilma API:** OpenWeatherMap API
- **Makse:** PayPal (simuleeritud)

### Rakenduse ülesehitus
Projekt on üles ehitatud Laravel + Inertia.js + Vue 3 arhitektuuriga. Kõik viis ülesannet on integreeritud ühte rakendusse.

**Kaustad:**
- `app/Models/` — Eloquent mudelid (User, Post, Comment, Marker, Product, Order, OrderItem, Movie)
- `app/Http/Controllers/` — Kontrollerid igale funktsionaalsusele
- `app/Http/Controllers/Api/` — JSON API kontrollerid
- `database/migrations/` — Andmebaasi migratsioonid
- `resources/js/Pages/` — Vue leheküljed (Weather, Map, Blog, Shop, Movies)
- `routes/web.php` — Veebimaršruudid

### Juhised koodi käivitamiseks

```bash
# 1. Klooni repositoorium
git clone <repo-url>
cd Hajusrakendused

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

# 8. Täida andmebaas algandmetega (tooted)
php artisan db:seed

# 9. Loo storage link (piltide jaoks)
php artisan storage:link

# 10. Lisa OpenWeatherMap API võti .env faili
# Registreeru: https://openweathermap.org/api
# Lisa: OPENWEATHERMAP_API_KEY=sinu_api_võti

# 11. Käivita arendusserverid
php artisan serve
npm run dev
```
