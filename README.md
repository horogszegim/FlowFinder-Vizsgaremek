## Auth rendszer fejlesztői dokumentáció (Frontend + Backend)

Ez a kezdetleges fejlesztői dokumentáció részlet a projekt AUTH rendszerét írja le:
felhasználó regisztráció, be- és kijelentkezés, tokenekkel való frontend session kezelés.

---

### Backend (Laravel)

#### Áttekintés

* Auth megoldás: Laravel Sanctum (personal access token)
* Token kizárólag bejelentkezéskor jön létre
* A backend nem kezel kijelentkezés route-ot
* Védett végpont Sanctum middleware-rel van ellátva

---

#### API route-ok

Fájl: `backend/routes/api.php`

* GET `/api/user`

  * Middleware: `auth:sanctum`
  * Visszatérési érték: az aktuálisan autentikált user

* POST `/api/registration`

  * Controller: `RegistrationController@store`
  * Validáció: `StoreUserRequest`

* POST `/api/login`

  * Controller: `AuthController@authenticate`
  * Validáció: `LoginRequest`

---

## Backend controller logika

#### Regisztráció

Fájl: `backend/app/Http/Controllers/RegistrationController.php`

Metódus:

* `store(StoreUserRequest $request): JsonResponse`

Folyamat:

* `$data = $request->validated()`
* `User::create($data)`

Válasz:

```json
{
	"data": {
		"message": "A(z) <email> sikeresen regisztrált"
	}
}
```

Megjegyzés:

* A regisztráció során nem készül token
* A token létrehozása bejelentkezéskor történik

---

#### Bejelentkezés

Fájl: `backend/app/Http/Controllers/AuthController.php`

Metódus:

* `authenticate(LoginRequest $request)`

Folyamat:

* `$data = $request->validated()`
* `Auth::attempt($data)` ellenőrzi az email–jelszó párost
* Siker esetén token generálás történik

Sikeres válasz:

```json
{
	"data": {
		"token": "<plainTextToken>"
	}
}
```

Hibás belépés:

* HTTP státusz: 401
* Válasz:

```json
{
	"data": {
		"message": "Sikertelen belépés"
	}
}
```

---

### Backend validációk

#### LoginRequest

Fájl: `backend/app/Http/Requests/LoginRequest.php`

Mezők:

* email

  * required
  * email:rfc,dns
  * max:255

* password

  * required
  * string
  * min:8
  * max:255

---

#### StoreUserRequest (regisztráció)

Fájl: `backend/app/Http/Requests/StoreUserRequest.php`

Mezők:

* username

  * required
  * string
  * min:3
  * max:24
  * unique:users,username
  * regex: `^[a-z][a-z0-9_]{2,23}$`
  * első karakter kisbetű
  * csak kisbetű, szám és aláhúzás (_)

* email

  * required
  * email:rfc,dns
  * unique:users,email
  * max:255

* password

  * required
  * string
  * min:8
  * max:255
  * confirmed
  * legalább egy kisbetű
  * legalább egy nagybetű
  * legalább egy szám
  * legalább egy speciális karakter (!, @, #, $, %, &, _)
  * csak engedélyezett karakterek

---

### User modell

Fájl: `backend/app/Models/User.php`

Fontos elemek:

* fillable mezők:

  * username
  * email
  * password

* hidden mezők:

  * password
  * remember_token

* casts:

  * password: hashed

    * a jelszó automatikusan hash-elésre kerül mentéskor

* Sanctum:

  * HasApiTokens trait használatban van

---

### Adatbázis migrációk

#### Users tábla

Fájl: `backend/database/migrations/0001_01_01_000000_create_users_table.php`

Mezők:

* id
* username (string, 24, unique)
* email (unique)
* password
* remember_token
* timestamps

#### Role mező

Fájl: `backend/database/migrations/2026_02_19_201614_add_role_to_users_table.php`

* role

  * string
  * max 10 karakter
  * default érték: "user"

#### Sanctum token tábla

* personal_access_tokens
* Laravel Sanctum alap migráció

---

### Frontend (Vue 3 + Pinia + FormKit)

#### Környezeti változók

Fájl: `frontend/.env.example`

* VITE_APP_NAME
* VITE_BACKEND_URL
* VITE_FRONTEND_URL

---

### HTTP kliens

Fájl: `frontend/src/utils/http.mjs`

* Axios instance
* baseURL: VITE_BACKEND_URL
* JSON alapú kérések
* Authorization header nem kerül automatikusan hozzáadásra

---

### Auth Store (Pinia)

Fájl: `frontend/src/stores/AuthStore.js`

State:

* token

  * localStorage-ból inicializálva

Computed:

* isAuthenticated

  * boolean a token megléte alapján

Metódusok:

* setToken(token)

  * token mentése store-ba
  * localStorage frissítése

* logout()

  * token nullázása
  * localStorage törlése

* login(payload)

  * POST /login
  * token mentése siker esetén

* register(payload)

  * POST /registration
  * siker után automatikus login

---

### Regisztráció oldal

Fájl: `frontend/src/pages/regisztracio.vue`

FormKit form használatban van.

Mezők:

* username

  * required
  * min 3, max 24 karakter
  * első karakter kisbetű
  * nincs nagybetű
  * csak kisbetű, szám és _

* email

  * required
  * email
  * max 255 karakter

* password

  * required
  * min 8, max 255 karakter
  * kisbetű, nagybetű, szám kötelező
  * speciális karakter kötelező (!@#$%&_)

* password_confirmation

  * kötelező
  * egyeznie kell a password mezővel

Submit:

* auth.register()
* router redirect a főoldalra

---

### Bejelentkezés oldal

Fájl: `frontend/src/pages/bejelentkezes.vue`

Mezők:

* email (required)
* password (required)

Submit:

* auth.login()
* siker esetén redirect

Hiba:

* (Egyelőre?) generikus hibaüzenet jelenik meg

---

### FormKit globális konfiguráció

Fájl: `frontend/src/main.js`

* FormKit plugin regisztrálva
* Globális class beállítások:

  * form layout
  * label stílus
  * input fókusz és border
  * validációs hibaüzenetek kinézete

---

# Spotok, képek és tagek rendszer dokumentáció (Backend + Frontend)

Ez a dokumentáció a spot alapú tartalomkezelést írja le:
spotok létrehozása, listázása, törlése, képek kezelése, valamint sportok és tagek kapcsolása.

---

## Backend (Laravel)

### Adatbázis migrációk

#### Spots tábla

Fájl: `backend/database/migrations/..._create_spots_table.php`

Mezők:

* id
* created_by

  * foreign key → users.id
  * cascadeOnDelete
* title

  * string (max 60)
* description

  * text (nullable)
* latitude

  * string (max 25)
* longitude

  * string (max 25)
* timestamps

Megjegyzés:

* A spot egy felhasználóhoz tartozik
* A koordináták stringként vannak tárolva

---

#### Images tábla

Fájl: `backend/database/migrations/..._create_images_table.php`

Mezők:

* id
* spot_id

  * foreign key → spots.id
  * cascadeOnDelete
* path

  * string (max 2048)
* timestamps

Megjegyzés:

* Egy spothoz több kép tartozhat
* A fájl elérési út kerül mentésre

---

#### SportsAndTags tábla

Fájl: `backend/database/migrations/..._create_sports_and_tags_table.php`

Mezők:

* id
* name

Megjegyzés:

* Egységes tábla sportok és tagek számára

---

#### Pivot tábla (spot_sports_and_tags)

Fájl: `backend/database/migrations/..._create_spot_sports_and_tags_table.php`

Mezők:

* spot_id
* sports_and_tag_id

Kulcs:

* composite primary key (spot_id, sports_and_tag_id)

Kapcsolat:

* many-to-many a spotok és tagek között

---

### API route-ok (kiegészítés)

Fájl: `backend/routes/api.php`

* GET `/api/users`

* GET `/api/users/{id}`

* GET `/api/spots`

* GET `/api/spots/{id}`

* POST `/api/spots`

* DELETE `/api/spots/{id}`

* GET `/api/images`

* GET `/api/images/{id}`

* POST `/api/images`

* DELETE `/api/images/{id}`

* GET `/api/sports-and-tags`

* GET `/api/sports-and-tags/{id}`

---

### Modellek

#### Spot modell

Fájl: `backend/app/Models/Spot.php`

fillable:

* title
* description
* latitude
* longitude
* created_by

Kapcsolatok:

* user()

  * belongsTo User
* images()

  * hasMany Image
* sportsAndTags()

  * belongsToMany SportsAndTag

---

#### Image modell

Fájl: `backend/app/Models/Image.php`

fillable:

* spot_id
* path

Kapcsolat:

* spot()

  * belongsTo Spot

---

#### SportsAndTag modell

Fájl: `backend/app/Models/SportsAndTag.php`

fillable:

* name

Tulajdonság:

* timestamps kikapcsolva

Kapcsolat:

* spots()

  * belongsToMany Spot

---

### Resource-ok

#### SpotResource

Fájl: `backend/app/Http/Resources/SpotResource.php`

Visszatérési struktúra:

```json
{
  "id": 1,
  "title": "...",
  "description": "...",
  "latitude": "...",
  "longitude": "...",
  "created_by": { ... },
  "sports_and_tags": [ ... ],
  "images": [ ... ]
}
```

Megjegyzés:

* Kapcsolatok csak akkor töltődnek, ha eager loading történik

---

#### ImageResource

Fájl: `backend/app/Http/Resources/ImageResource.php`

Mezők:

* id
* spot_id
* path
* url
* spot (opcionális)

---

#### SportsAndTagResource

Fájl: `backend/app/Http/Resources/SportsAndTagResource.php`

Mezők:

* id
* name

---

### Request validációk

#### StoreSpotRequest

Fájl: `backend/app/Http/Requests/StoreSpotRequest.php`

Mezők:

* title

  * required
  * string
  * max:60

* description

  * required
  * string

* latitude

  * required
  * string
  * max:25

* longitude

  * required
  * string
  * max:25

* created_by

  * required
  * exists:users,id

* sports_and_tags

  * array

* sports_and_tags.*

  * exists:sports_and_tags,id

---

#### StoreImageRequest

Fájl: `backend/app/Http/Requests/StoreImageRequest.php`

Mezők:

* spot_id

  * required
  * exists:spots,id

* image

  * required
  * file
  * image
  * max:3072
  * mimes: jpg, jpeg, png

---

### Controllerek

#### SpotController

Fájl: `backend/app/Http/Controllers/SpotController.php`

index():

* Spot lista lekérése
* eager loading: user, images, sportsAndTags

store():

* validált adatok mentése
* Spot::create()
* tagek sync

show():

* egy spot lekérése kapcsolatokkal

destroy():

* képek törlése storage-ból
* mappa törlése
* rekord törlése

---

#### ImageController

Fájl: `backend/app/Http/Controllers/ImageController.php`

index():

* képek listázása

store():

* fájl mentése
* rekord létrehozása

destroy():

* fájl + rekord törlése

---

#### SportsAndTagController

Fájl: `backend/app/Http/Controllers/SportsAndTagController.php`

* index()
* show()

---

### Seederek

#### SpotSeeder

* 10 teszt adat
* random koordináták

#### SportsAndTagSeeder

* előre definiált lista

#### ImageSeeder

* spotonként több kép
* seed képek használata

---

## Frontend (Vue 3 + Pinia)

### Store-ok

#### SpotStore

Fájl: `frontend/src/stores/SpotStore.js`

* getSpots()
* getSpot(id)
* createSpot()
* deleteSpot()

---

#### SportsAndTag Store

* tagek lekérése

---

#### ImageStore

Fájl: `frontend/src/stores/ImageStore.js`

* getImages()
* uploadImage()
* deleteImage()

---

## Spot megjelenítő oldal

Fájl: `frontend/src/pages/[id].vue`

Funkciók:

* spot betöltés
* tagek megjelenítése
* képgaléria
* lightbox
* Google Maps embed
* bookmark (frontend only)

---

## Képfeltöltés teszt oldal

Fájl: `frontend/src/pages/kepfeltoltes-teszt.vue`

Funkciók:

* képfeltöltés
* kép törlés
* spot törlés
* lista megjelenítés

---

# Spot mentési rendszer dokumentáció (Backend + Frontend)

Ez a dokumentáció a spotok elmentésének (bookmark) rendszerét írja le:
felhasználók által mentett spotok kezelése backend és frontend oldalon.

---

## Backend (Laravel)

### Adatbázis migráció

#### SavedSpots tábla

Fájl: `backend/database/migrations/..._create_saved_spots_table.php`

Mezők:

* id
* user_id

  * foreign key → users.id
  * cascadeOnDelete
* spot_id

  * foreign key → spots.id
  * cascadeOnDelete

Egyediség:

* unique(user_id, spot_id)

Megjegyzés:

* Egy felhasználó egy spotot csak egyszer menthet el
* A rekord törlődik, ha a user vagy a spot törlődik

---

### API route-ok

Fájl: `backend/routes/api.php`

* GET `/api/saved-spots`

  * csak az aktuális user mentéseit adja vissza

* POST `/api/saved-spots`

  * új mentés létrehozása

* GET `/api/saved-spots/{id}`

  * egy mentett spot lekérése

* DELETE `/api/saved-spots/{id}`

  * mentés törlése

Middleware:

* `auth:sanctum`

---

### SavedSpot modell

Fájl: `backend/app/Models/SavedSpot.php`

fillable:

* user_id
* spot_id

Kapcsolatok:

* user()

  * belongsTo User

* spot()

  * belongsTo Spot

---

### Resource

#### SavedSpotResource

Fájl: `backend/app/Http/Resources/SavedSpotResource.php`

Visszatérési struktúra:

{
"id": 1,
"user_id": 1,
"spot_id": 5,
"user": { ... },
"spot": { ... }
}

---

### Request validáció

#### StoreSavedSpotRequest

Fájl: `backend/app/Http/Requests/StoreSavedSpotRequest.php`

Mezők:

* spot_id

  * required
  * integer
  * exists:spots,id

Megjegyzés:

* user_id nem jön a frontendről
* a backend a bejelentkezett userből veszi

---

### Controller

#### SavedSpotController

Fájl: `backend/app/Http/Controllers/SavedSpotController.php`

index(Request $request):

* csak a bejelentkezett user mentéseit adja vissza

store(StoreSavedSpotRequest $request):

* user azonosítása `$request->user()` alapján
* `firstOrCreate` használat az ismétlődések elkerülésére

show(SavedSpot $savedSpot):

* egy rekord lekérése kapcsolatokkal

destroy(Request $request, SavedSpot $savedSpot):

* csak a saját mentés törölhető
* user_id ellenőrzés

---

## Frontend (Vue 3 + Pinia)

### HTTP kliens

Fájl: `frontend/src/utils/http.mjs`

Kiegészítés:

* Authorization header automatikus hozzáadása

Authorization: Bearer <token>

---

### Store

#### SavedSpotStore

Fájl: `frontend/src/stores/SavedSpotStore.js`

State:

* savedSpots

Metódusok:

* getSavedSpots()

  * GET `/saved-spots`
  * user mentéseinek lekérése

* isSaved(spotId)

  * boolean visszatérés
  * ellenőrzi, hogy a spot mentve van-e

* saveSpot(spotId)

  * POST `/saved-spots`
  * új mentés létrehozása

* deleteSavedSpot(id)

  * DELETE `/saved-spots/{id}`
  * mentés törlése

* findSavedSpotId(spotId)

  * visszaadja a mentés ID-ját adott spothoz

---

### Spot megjelenítő oldal

Fájl: `frontend/src/pages/[id].vue`

Funkciók:

* spot betöltés
* mentések lekérése bejelentkezett user esetén
* bookmark állapot inicializálása

Bookmark logika:

* Ha nincs bejelentkezve:

  * piros toast jelenik meg
  * nincs állapotváltozás

* Ha nincs mentve:

  * POST request
  * bookmark aktiválódik

* Ha már mentve van:

  * DELETE request
  * bookmark deaktiválódik

Hiba kezelés:

* try/catch blokk
* toastError flag használata

---

### UI viselkedés

* üres bookmark ikon → nincs mentve
* kitöltött bookmark ikon → mentve van
* toast visszajelzés minden művelet után

---

## Adatfolyam

1. Felhasználó rányom a bookmark gombra
2. Frontend ellenőrzi az auth állapotot
3. API request indul
4. Backend azonosítja a usert token alapján
5. Adatbázis művelet történik
6. Resource visszatér
7. Store frissül
8. UI állapot frissül

---

## API middleware-ek és spot feltöltés

---

## Backend – API middleware-ek

### Áttekintés

Az API végpontok két csoportra lettek bontva:

* publikus végpontok
* védett végpontok (`auth:sanctum` middleware)

Cél:

* nem bejelentkezett user is tudjon böngészni
* létrehozás / törlés csak autentikált usernek legyen elérhető

---

### Route struktúra

Fájl: `backend/routes/api.php`

Publikus:

* GET `/api/spots`
* GET `/api/spots/{id}`
* GET `/api/sports-and-tags`
* GET `/api/sports-and-tags/{id}`
* GET `/api/images`
* GET `/api/images/{id}`
* GET `/api/users`
* GET `/api/users/{id}`

Védett (`auth:sanctum`):

* POST `/api/spots`
* DELETE `/api/spots/{id}`
* POST `/api/images`
* DELETE `/api/images/{id}`
* `/api/saved-spots` összes route

---

### Működés

* A frontend minden kéréshez hozzáadja a Bearer tokent (ha van)
* A Sanctum middleware ellenőrzi a tokent
* Ha nincs token:

  * a kérés 401-el elutasításra kerül
* Ha van token:

  * `$request->user()` és `Auth::id()` elérhető

---

### Spot létrehozás (fontos változás)

A `created_by` mező:

* már nem jön frontendről
* backend állítja be:

```php
$data['created_by'] = Auth::id();
```

Előny:

* nem manipulálható kliens oldalról
* biztonságosabb

---

## Frontend – Spot feltöltés oldal

Fájl: `frontend/src/pages/feltoltes.vue`

---

### Jogosultság kezelés

Oldal betöltéskor:

* ha nincs bejelentkezve:

  * redirect `/bejelentkezes`
  * toast üzenet: „Spot feltöltéshez be kell jelentkezned!”

---

### Használt store-ok

* `AuthStore`

  * auth állapot

* `SportsAndTagStore`

  * tagek lekérése

* `SpotStore`

  * spot létrehozás

* `ImageStore`

  * képfeltöltés

---

### Adatfolyam (feltöltés)

1. FormKit form submit
2. frontend validációk lefutnak
3. `spotStore.createSpot()` hívás
4. spot ID visszatér
5. képek feltöltése `imageStore.uploadImage()`
6. redirect `/spotok/{id}`

---

### Képfeltöltés rendszer

Funkciók:

* drag & drop
* több kép feltöltés
* preview
* sorrend módosítás (bal / jobb nyíl)
* törlés

Limit:

* max 10 kép
* max 3MB / kép
* csak JPG / PNG

Viselkedés:

* ha elérte a limitet:

  * feltöltő mező eltűnik
  * üzenet jelenik meg:

    „Elérted a maximum képszámot. Törölj egy képet, hogy újat tölthess fel!”

---

### Tag kiválasztás UX

* színes tagek (chip-ek)
* kattintással toggle
* nincs Ctrl/Cmd szükség

Limit:

* max 5 tag

Hiba:

* ideiglenes (2.5s) hibaüzenet jelenik meg

---

### Validációk

Frontend:

* cím: max 60 karakter
* latitude / longitude:

  * max 25 karakter
  * csak szám és pont (`^[0-9.]+$`)

Backend:

* ugyanez enforced `StoreSpotRequest`-ben

---

### Hiba kezelés

Típusok:

* imageError

  * képfeltöltési hibák

* tagError

  * tag limit

* errorMessage

  * backend hibák

Viselkedés:

* field közeli hibák
* ideiglenes toast jellegű üzenetek

---

### UI viselkedés

* gombok hover animációval
* preview kártyák overlay action gombokkal
* smooth transition-ök

---

# Kezdőlap, globális auth guard és toast rendszer dokumentáció (Frontend)

Ez a dokumentáció a kezdőlap teljes elkészítését, a route szintű jogosultságkezelést, valamint az egész alkalmazásban használható globális toast visszajelző rendszert írja le.

---

## Kezdőlap (index.vue)

Fájl: `frontend/src/pages/index.vue`

### Áttekintés

A kezdőlap a projekt nyitóoldala, amely bemutatja:

* a FlowFinder célját
* a projekt hátterét
* a fejlesztőcsapatot
* a jelenlegi funkciókat
* a regisztrációhoz kötött lehetőségeket

---

### Layout felépítés

Használt wrapper:

* `BaseLayout`

Fő szekciók:

* Hero blokk logóval és CTA gombokkal
* Projekt bemutatása
* Miért készült a rendszer
* Rólunk blokk
* Funkciólista
* Fiókhoz kötött funkciók ismertetése

---

### Navigációs elemek

RouterLink használatban:

* `/spotkereso`
* `/feltoltes`
* `/profil`
* `/regisztracio`
* `/bejelentkezes`

---

### Meta adatok

Route meta:

* name: `kezdolap`
* title: `FlowFinder`

---

### UI célok

* modern landing page megjelenés
* reszponzív kialakítás
* egyértelmű call-to-action gombok
* projekt bemutatása külsős látogatóknak is

---

## Globális Auth Guard rendszer

### Fájl

`frontend/src/router/guards/AuthGuard.js`

---

### Áttekintés

A guard központilag kezeli:

* belépéshez kötött oldalak védelmét
* vendég-only oldalak kezelését
* automatikus átirányításokat
* toast visszajelzéseket

---

### Használt store-ok

* `AuthStore`
* `ToastStore`

---

### Működés

#### Ha az oldal `requiresAuth: true`

És nincs bejelentkezve a felhasználó:

* toast üzenet jelenik meg
* redirect `/bejelentkezes`

Üzenet:

`Ehhez először be kell jelentkezned!`

---

#### Ha az oldal `guestOnly: true`

És a user már be van jelentkezve:

* toast üzenet jelenik meg
* redirect `/profil`

Üzenet:

`Már be vagy jelentkezve!`

---

#### Egyéb esetben

* navigáció engedélyezett

---

## Router bekötés

Fájl: `frontend/src/router/index.js`

### Új beforeEach guardok

* `authGuard`
* `setTitle`

Sorrend:

1. authGuard
2. setTitle

---

### Előnyök

* minden route egy helyen kezelhető
* nincs szükség oldalszintű auth ellenőrzésekre
* egységes UX működés

---

## Route meta használat

### Védett oldalak

Példák:

* `feltoltes.vue`
* `profil.vue`

Meta:

* `requiresAuth: true`

---

### Vendég-only oldalak

Példák:

* `bejelentkezes.vue`
* `regisztracio.vue`

Meta:

* `guestOnly: true`

---

## Globális Toast rendszer

### Store

Fájl: `frontend/src/stores/ToastStore.js`

---

### State

* `show`
* `message`
* `type`

---

### Típusok

* success
* error

---

### Metódus

#### trigger(message, type)

Feladata:

* üzenet beállítása
* toast megjelenítése
* automatikus eltüntetés 3 másodperc után

---

## Megjelenítő komponens

### Fájl

`frontend/src/components/GlobalToast.vue`

---

### Működés

A komponens figyeli a `ToastStore` állapotát.

Ha `show = true`:

* fix pozícióban megjelenik felül középen
* animációval érkezik
* automatikusan eltűnik

---

### Színezés

* success → elsődleges szín
* error → piros háttér

---

### Animáció

Vue transition használatban:

* belépéskor fade + slide
* kilépéskor fade

---

## Globális bekötés Layout szinten

### Fájl

`frontend/src/layouts/BaseLayout.vue`

---

### Új komponens

* `GlobalToast`

Elhelyezés:

* BaseHeader alatt
* BaseFooter fölött
* teljes alkalmazásból elérhető

---

### Előny

Minden oldal külön import nélkül használhat toast üzenetet store-on keresztül.

---

## Oldalak frissítése toast rendszerre

### Bejelentkezés oldal

Fájl: `frontend/src/pages/bejelentkezes.vue`

Siker esetén:

* toast: `Sikeres bejelentkezés!`
* redirect `/`

---

### Regisztráció oldal

Fájl: `frontend/src/pages/regisztracio.vue`

Siker esetén:

* toast: `Sikeres regisztráció!`
* redirect `/`

---

### Spot megjelenítő oldal

Fájl: `frontend/src/pages/[id].vue`

Mentéskor:

* `Spot sikeresen elmentve!`

Törléskor:

* `Mentés eltávolítva!`

Auth nélkül bookmark esetén:

* `Spot mentéséhez be kell jelentkezned!`

Általános hiba:

* `Hiba történt!`

---

## Spot megjelenítő oldal fejlesztések

Fájl: `frontend/src/pages/[id].vue`

### Új funkciók

* globális toast használat
* bookmark kezelés finomítása

## Feltöltés oldal route védelem

Fájl: `frontend/src/pages/feltoltes.vue`

Meta:

* `requiresAuth: true`

Így a guard automatikusan védi az oldalt.

---

## Profil oldal route védelem

Fájl: `frontend/src/pages/profil.vue`

Meta:

* `requiresAuth: true`

---

## Bejelentkezés és Regisztráció oldal védelem

Meta:

* `guestOnly: true`

Ha a user már belépett:

* nem nyithatja meg ezeket az oldalakat
* automatikusan profil oldalra kerül

---

# Spotkereső rendszer dokumentáció (Frontend + Backend seed frissítések)

Ez a dokumentáció a teljes spotkereső oldal elkészítését, az új spot lista komponens bevezetését, a frontend oldali szűrési és lapozási rendszert, valamint a nagyobb teszt adathalmazhoz igazított backend seeder frissítéseket írja le.

---

## Frontend – Spotkereső oldal

### Fájl

`frontend/src/pages/spotkereso.vue`

---

### Áttekintés

A spotkereső oldal célja, hogy a felhasználó gyorsan és kényelmesen böngésszen az összes feltöltött spot között.

Fő funkciók:

* szöveges keresés
* tag alapú szűrés
* kombinált szűrés
* lapozás
* loading állapot
* reszponzív lista megjelenítés
* gördülékeny UX működés nagy adatmennyiség mellett is

---

### Route meta

Meta adatok:

* name: `spotkereso`
* title: `Spotkereső`

---

### Használt store-ok

* `SpotStore`
* `SportsAndTagStore`

---

### Betöltési folyamat

Oldal mountoláskor:

* `spotStore.getSpots()`
* `sportsAndTagStore.getSportsAndTags()`

Ennek eredménye:

* spot lista betöltése
* tag lista betöltése

---

### Keresőmező

A felső kereső input szabad szavas keresést biztosít.

Keresési mezők:

* spot cím
* spot leírás
* feltöltő felhasználónév
* tagek neve

Működés:

* kis- és nagybetű független
* trimelt input
* azonnali szűrés gépelés közben

---

### Tag alapú szűrés

A tagek vízszintesen görgethető chip rendszerben jelennek meg.

Tulajdonságok:

* több tag egyszerre kiválasztható
* kattintással ki- és bekapcsolható
* aktív tag vizuálisan kiemelt állapotot kap

Szűrés logika:

* ha nincs kiválasztott tag, minden spot látható
* ha van kiválasztott tag, elég egy egyezés a megjelenéshez

---

### Tag görgető UX

A tag lista:

* egérgörgővel oldalirányban is görgethető
* bal / jobb nyíl gombokkal mozgatható
* mobilon natív touch scroll működik

---

### Kombinált szűrés

A szöveges keresés és tag szűrés egyszerre működik.

Példa:

* keresés: `budapest`
* kiválasztott tag: `BMX`

Ekkor csak azok a spotok jelennek meg, amelyek mindkét feltételnek megfelelnek.

---

### Loading állapot

Amíg a spotok töltődnek:

* spinner jelenik meg
* "Spotok betöltése ..." szöveg látható

A loading állapot a store-ból érkezik.

---

### Üres találat állapot

Ha nincs egyező spot:

* középre igazított hibaüzenet jelenik meg

Szöveg:

`Nincs találat a keresésre!`

---

### Lapozás

Oldalanként megjelenített elemszám:

* 100 spot / oldal

Funkciók:

* előző oldal
* következő oldal
* közeli oldalszámok megjelenítése
* aktív oldal kiemelése

UX:

* lapváltáskor automatikus visszagörgetés oldal tetejére

---

### További találatok kijelzése

Aktív szűrés esetén a rendszer mutatja, hogy hány további találat érhető még el a következő oldalakon.

Példa:

`35 további találat ...`

---

### Reszponzív viselkedés

Az oldal mobilon, tableten és desktopon optimalizált.

Főbb elemek:

* rugalmas spot lista
* tördelődő lapozó gombok
* görgethető tag sáv
* megfelelő térközök kisebb kijelzőn is

---

## Frontend – Spot lista elem komponens

### Fájl

`frontend/src/components/BaseSpotBlock.vue`

---

### Áttekintés

Újrafelhasználható komponens egyetlen spot listaelem megjelenítésére.

Felhasználás:

* spotkereső lista

---

### Megjelenített adatok

* első kép
* spot cím
* feltöltő neve
* tagek
* rövidített leírás

---

### Képkezelés

A komponens az első elérhető spot képet jeleníti meg.

Ha nincs kép:

* placeholder kép töltődik be

---

### Navigáció

A teljes blokk kattintható.

Kattintáskor:

* redirect `/spotok/{id}`

---

### Szövegkezelés

Nagy vagy szabálytalan tartalom esetén is stabil marad a layout.

Megoldások:

* hosszú cím levágása
* több soros leírás clamp
* túlcsordulás tiltása
* mobil kompatibilis tördelés

---

### Reszponzív felépítés

Mobilon:

* egymás alatti kép + tartalom

Desktopon:

* oldalsó képes kártya nézet

---

## Frontend – SpotStore frissítés

### Fájl

`frontend/src/stores/SpotStore.js`

---

### Új state

* `loading`

Feladata:

* spot lista kérés állapotának követése

---

### getSpots() módosítás

Lekérés előtt:

* `loading = true`

Lekérés után:

* `loading = false`

Ez biztosítja a keresőoldal loading spinner működését.

---

## Frontend – Spot megjelenítő oldal finomítások

### Fájl

`frontend/src/pages/[id].vue`

---

### Reszponzív szövegkezelés

A cím, feltöltő név és leírás frissítve lett extrém hosszú vagy szándékosan problémás tartalom kezelésére.

Cél:

* ne csússzon szét az oldal
* ne lógjon ki szöveg
* mobilon is olvasható maradjon

Használt megoldások:

* `overflow-wrap:anywhere`
* automatikus sortörés
* hyphenation támogatás
* rugalmas flex layout

---

### Eredmény

Akár troll jellegű tartalom esetén is stabil marad az oldal megjelenése.

---

## Backend – Seeder rendszer frissítés

### Cél

Nagyobb adathalmaz melletti működés tesztelése.

Fő területek:

* frontend lista teljesítmény
* keresés
* lapozás
* eager loading
* képgaléria működés
* adatbázis terhelés

---

## SpotSeeder frissítés

### Fájl

`backend/database/seeders/SpotSeeder.php`

---

### Régi működés

* 10 teszt spot

### Új működés

* 1000 spot generálása

Tulajdonságok:

* változó hosszúságú címek
* változó hosszúságú leírások
* random user tulajdonos
* random magyarországi koordináták

---

### Cím generálás

A cím elején sorszám szerepel.

Példa:

`153. RandomTitle`

A cím továbbra is maximum 60 karakteres.

---

## ImageSeeder frissítés

### Fájl

`backend/database/seeders/ImageSeeder.php`

---

### Régi működés

* csak 10 spothoz generált képeket

### Új működés

* mind az 1000 spothoz készül kép

Spotonként:

* minimum 1 kép
* maximum 10 kép

A képek random seed fájlokból kerülnek kiválasztásra.

---

## SpotSportsAndTagSeeder frissítés

### Fájl

`backend/database/seeders/SpotSportsAndTagSeeder.php`

---

### Új működés

Mindegyik spothoz véletlenszerűen kerül:

* 0 - 5 darab tag

A rendszer akkor is lefut, ha egyetlen tag sem kerül hozzárendelésre.

---

## Tesztelési előnyök

Az új seed adathalmaz alkalmas:

* több száz elemes lista render tesztre
* frontend keresési teljesítmény mérésre
* lapozási UX ellenőrzésre
* képes spotok tömeges tesztelésére
* reszponzív viselkedés ellenőrzésére
* backend lekérdezések valósabb terhelésére

---

# Profil oldal és admin spotkezelés dokumentáció (Backend + Frontend)

Ez a kiegészítés a profil oldal fejlesztését, az admin jogosultság kezelését, valamint az admin oldali spotkezelést írja le.

---

## Backend módosítások

### Auth rendszer kiegészítése

A frontend profil oldalának működéséhez szükség van arra, hogy a backend visszaadja az aktuálisan bejelentkezett felhasználó adatait.

Használt végpont:

* GET `/api/user`

Middleware:

* `auth:sanctum`

Feladata:

* token alapján azonosítja a bejelentkezett felhasználót
* visszaadja a user adatait
* a frontend ebből tudja eldönteni, hogy a user admin-e

Fontos mező:

* `role`

Lehetséges értékek:

* `user`
* `admin`

---

### Admin jogosultság használata

A users táblában lévő `role` mező alapján történik az admin jogosultság ellenőrzése.

A frontend nem saját maga találja ki, hogy ki admin, hanem a backendről lekért user adatból dolgozik.

Előny:

* biztonságosabb
* nem csak frontend oldali elrejtés történik
* a jogosultság a backend adatai alapján dől el

---

### Spot törlés admin felületről

Az admin profil oldalon az admin felhasználó az összes spotot látja, és törölheti őket.

Érintett végpont:

* DELETE `/api/spots/{id}`

Middleware:

* `auth:sanctum`

Törléskor a backend feladata:

* spot rekord törlése
* spothoz tartozó képrekordok kezelése
* storage-ban lévő képfájlok törlése
* spot mappájának törlése
* kapcsolódó pivot rekordok eltávolítása az adatbázisból

Fontos:

* a sportok és tagek rekordjai nem törlődnek
* csak a spot és a hozzá kapcsolódó adatok törlődnek
* ha nincs megfelelő jogosultság, a backend 403-as hibát adhat vissza

---

## Frontend módosítások

## Profil oldal

Fájl:

* `frontend/src/pages/profil.vue`

---

### Áttekintés

A profil oldal feladata:

* bejelentkezett user adatainak megjelenítése
* kijelentkezés biztosítása
* admin esetén spotkezelő felület megjelenítése
* összes spot listázása admin számára
* spotok törlése admin felületről
* lapozás nagyobb adatmennyiséghez

---

### Használt store-ok

* `AuthStore`
* `SpotStore`
* `ToastStore`

---

### Betöltési folyamat

Oldal betöltésekor:

* lefut az `authStore.fetchUser()`
* a rendszer lekéri az aktuális usert
* ha a user admin, lefut a `spotStore.getSpots()`
* ha a munkamenet lejárt vagy hibás a token, a user kijelentkeztetésre kerül
* a rendszer átirányítja a felhasználót a bejelentkezés oldalra

Hiba esetén megjelenő toast:

* `A munkamenet lejárt, jelentkezz be újra!`

---

### Admin badge

A korábbi hosszabb admin szöveg helyett egy rövid badge jelenik meg.

Szöveg:

* `Admin`

Elhelyezés:

* a felhasználónév mellett
* reszponzívan törik, ha kisebb kijelzőn nem fér el egy sorban

Cél:

* letisztultabb profil fejléc
* kevesebb felesleges szöveg
* egyértelmű admin jelölés

---

### Kijelentkezés

A kijelentkezés gomb a profil fejléc jobb oldalán jelenik meg.

Működés:

* meghívja az `authStore.logout()` metódust
* törli a session állapotot a frontendből
* toast visszajelzést ad
* átirányít a bejelentkezés oldalra

Sikeres üzenet:

* `Sikeresen kijelentkeztél!`

---

## Admin spotkezelő felület

A spotkezelő felület csak admin jogosultsággal jelenik meg.

Nem admin user esetén a profil oldal csak egy egyszerű üzenetet jelenít meg:

* `Ez egy sima felhasználói profil (nem admin).`

---

### Spot lista

Az admin felületen az összes spot listázva van.

Megjelenítés:

* `BaseSpotBlock` komponenssel
* minden spot mellett külön törlés gombbal
* a törlés gomb nem a spot kártyára van rárakva
* a törlés gomb jobb oldalon, függőlegesen középre igazítva jelenik meg

---

### Loading állapot

A profil oldalon lévő spot betöltés ugyanazt a loading stílust használja, mint a spotkereső oldal.

Megjelenő elemek:

* animált spinner
* `Spotok betöltése ...` szöveg

Cél:

* egységes UX a spotkereső és a profil oldal között
* ne legyen különböző loading dizájn ugyanarra a funkcióra

---

### Üres lista állapot

Ha nincs feltöltött spot, a következő szöveg jelenik meg:

* `Nincs még feltöltött spot.`

---

## Lapozás

A profil oldalon a lapozás a spotkereső oldal működéséhez lett igazítva.

Oldalankénti elemszám:

* 100 spot / oldal

### Lapozó működése

A lapozó ugyanazt a logikát követi, mint a `spotkereso.vue`.

Funkciók:

* előző oldal gomb
* következő oldal gomb
* SVG nyíl ikonok használata
* aktuális oldal kiemelése
* közeli oldalszámok megjelenítése
* lapváltáskor automatikus visszagörgetés az oldal tetejére

Használt SVG ikonok:

* `left-arrow-white.svg`
* `right-arrow-white.svg`

---

### Látható oldalszámok

A lapozó az aktuális oldal körüli oldalszámokat jeleníti meg.

Logika:

* aktuális oldal előtt maximum 2 oldal
* aktuális oldal után maximum 2 oldal
* az oldalszámok nem mennek 1 alá
* az oldalszámok nem mennek a maximális oldalszám fölé

---

### Aktuális oldal korrigálása törlés után

Spot törlése után előfordulhat, hogy az aktuális oldal már nem létezik.

Példa:

* a user az utolsó oldalon van
* törli az utolsó spotot
* emiatt csökken az oldalak száma

Erre a profil oldal figyel:

* ha az aktuális oldal nagyobb, mint az összes oldal száma, akkor visszaáll az utolsó létező oldalra

---

## Spot törlés frontend oldalon

Törlés előtt a rendszer megerősítést kér.

Megerősítő szöveg:

* `Biztos törölni szeretnéd ezt a spotot: "<spot címe>"?`

Ha a user mégsem töröl:

* nem történik API kérés

Ha megerősíti:

* beáll a `deleteLoadingId`
* a konkrét törlés gomb disabled állapotba kerül
* lefut a `spotStore.deleteSpot(spot.id)`
* siker esetén a spot kikerül a listából
* toast visszajelzés jelenik meg

Sikeres törlés üzenete:

* `Spot sikeresen törölve!`

Jogosultsági hiba esetén:

* `Nincs jogosultságod a spot törléséhez!`

Általános hiba esetén:

* `Hiba történt a spot törlése közben!`

---

# Spot szerkesztés, képsorrend, profil oldal, 404 oldal és seeder frissítések

Ez a rész az előző commit óta bekerült újabb módosításokat írja le.

A módosítások több részt érintenek:

* backend spot kezelés
* képfeltöltés és képsorrend
* seedelt adatok
* profil oldal
* spot szerkesztés frontend oldalon
* 404-es oldal
* kisebb store, route és jogosultsági javítások

---

## Backend módosítások

### Images tábla frissítése

Fájl:

`backend/database/migrations/2026_04_06_155031_create_images_table.php`

Az `images` tábla bővült.

Új mező:

* `sort_order`

Feladata:

* a spothoz tartozó képek sorrendjének tárolása
* feltöltéskor az új kép a meglévő képek után kerül
* szerkesztéskor a frontend által küldött sorrend menthető

A `path` mező mérete is módosult.

Korábbi cél:

* lokális storage útvonal tárolása

Új cél:

* lokális storage útvonal tárolása
* külső kép URL tárolása

Ez azért kellett, mert a seed képek már nem lokálisan vannak tárolva, hanem külső tárhelyről töltődnek be.

---

### Image modell frissítése

Fájl:

`backend/app/Models/Image.php`

A modell `fillable` mezői bővültek.

Újonnan kezelhető mező:

* `sort_order`

Így az Eloquent modell már tömeges létrehozáskor és frissítéskor is tudja kezelni a képek sorrendjét.

---

### ImageResource frissítése

Fájl:

`backend/app/Http/Resources/ImageResource.php`

Az `ImageResource` már kezeli a külső és a belső képeket is.

Működés:

* ha a `path` `http://` vagy `https://` kezdetű, akkor külső URL-ként kerül visszaadásra
* ha a `path` lokális storage útvonal, akkor a Laravel `Storage::disk('public')->url()` alakítja publikus URL-lé
* a válaszban megjelenik a `sort_order` is

Visszaadott fontos mezők:

* `id`
* `spot_id`
* `path`
* `url`
* `sort_order`
* `spot`, ha be van töltve

Ez egységesíti a feltöltött és seedelt képek frontend oldali használatát.

---

### StoreImageRequest javítás

Fájl:

`backend/app/Http/Requests/StoreImageRequest.php`

A request osztályban bekerült az `authorize()` metódus.

```php
public function authorize(): bool
{
    return true;
}
```

Ez azért fontos, mert így a FormRequest nem blokkolja automatikusan a képfeltöltést.

A validáció továbbra is a képfájlokra vonatkozik.

Engedett formátumok:

* jpg
* jpeg
* png

Méretlimit:

* maximum 3MB

---

### StoreSpotRequest javítás

Fájl:

`backend/app/Http/Requests/StoreSpotRequest.php`

A request osztályban bekerült az `authorize()` metódus.

Ez megszünteti azt a problémát, hogy a Laravel FormRequest jogosultsági okból elutasíthatja a spot létrehozást vagy szerkesztést.

A validáció szigorítva lett.

Fontos szabályok:

* `title`: kötelező, maximum 60 karakter
* `description`: kötelező, maximum 2048 karakter
* `latitude`: kötelező, maximum 25 karakter, csak szám és pont
* `longitude`: kötelező, maximum 25 karakter, csak szám és pont
* `sports_and_tags`: opcionális tömb
* `sports_and_tags.*`: létező tag azonosító

---

### Új UpdateSpotImageOrderRequest

Fájl:

`backend/app/Http/Requests/UpdateSpotImageOrderRequest.php`

Új request osztály készült a képsorrend mentéséhez.

Elvárt adat:

* `image_order`

Típusa:

* tömb

Tartalma:

* image ID-k

Validáció:

* az `image_order` kötelező
* minden elemnek létező kép azonosítónak kell lennie az `images` táblában

Ez különválasztja a spot adatok szerkesztését és a képek sorrendjének módosítását.

---

### Spot modell frissítése

Fájl:

`backend/app/Models/Spot.php`

A `images()` kapcsolat módosult.

A képek már nem csak simán kapcsolódnak a spothoz, hanem rendezve jönnek vissza.

Rendezés:

1. `sort_order`
2. `id`

Ennek hatása:

* a spot részletező oldal ugyanabban a sorrendben kapja meg a képeket
* a profil oldal is rendezett képlistát használ
* a szerkesztés után mentett képsorrend stabilan megmarad

---

### SpotController frissítése

Fájl:

`backend/app/Http/Controllers/SpotController.php`

A controller több új feladatot kapott.

Új vagy módosított működések:

* spot lista rendezett lekérése
* spot létrehozás backend oldali `created_by` beállítással
* spot szerkesztés
* képsorrend mentése
* spot törlés jogosultsági ellenőrzéssel
* admin és tulajdonos alapú jogosultságkezelés

---

#### Spot létrehozás

A `created_by` továbbra sem frontendről érkezik.

A backend állítja be:

```php
'created_by' => Auth::id()
```

Ez biztonságosabb, mert a kliens nem tudja meghamisítani a feltöltő felhasználó azonosítóját.

---

#### Spot szerkesztés

Új működés:

* a spot címe módosítható
* a leírás módosítható
* a koordináták módosíthatók
* a tagek módosíthatók

Szerkesztésre jogosult:

* a spot tulajdonosa
* admin felhasználó

Ha más felhasználó próbálja szerkeszteni:

* 403-as válasz érkezik

---

#### Képsorrend mentése

Új metódus:

`updateImageOrder(UpdateSpotImageOrderRequest $request, Spot $spot)`

Működés:

* a frontend elküldi az image ID-k sorrendjét
* a backend végigmegy a tömbön
* minden kép megkapja az új `sort_order` értéket
* a válaszban frissített spot resource tér vissza

---

#### Spot törlés jogosultsággal

A spot törlés most már ellenőrzi, hogy a felhasználó jogosult-e a törlésre.

Törölhet:

* admin
* a spot tulajdonosa

Nem törölhet:

* másik sima felhasználó

Törléskor a backend megpróbálja törölni a spothoz tartozó storage fájlokat és könyvtárat is.

---

### API route módosítások

Fájl:

`backend/routes/api.php`

A spot route-ok bővültek.

Fontos endpointok:

* `GET /api/spots`
* `GET /api/spots/{spot}`
* `POST /api/spots`
* `PUT/PATCH /api/spots/{spot}`
* `DELETE /api/spots/{spot}`
* `PUT /api/spots/{spot}/images/order`

A képsorrend endpoint védett route.

Ez azt jelenti, hogy csak bejelentkezett user tudja hívni.

A jogosultságot a controller ellenőrzi.

---

### SavedSpotController frissítés

Fájl:

`backend/app/Http/Controllers/SavedSpotController.php`

A mentett spotok kezelése pontosabb lett.

Módosítások:

* a mentett spot lista csak az aktuális felhasználó mentéseit adja vissza
* a válasz betölti a spothoz tartozó usert, képeket és tageket is
* mentéskor `firstOrCreate()` fut
* így ugyanazt a spotot nem menti el többször ugyanannak a usernek
* törléskor ellenőrzi, hogy a mentés az aktuális userhez tartozik-e

Ez a profil oldali mentett spot lista miatt fontos.

---

## Seeder módosítások

### DatabaseSeeder sorrend

Fájl:

`backend/database/seeders/DatabaseSeeder.php`

A seederek meghívási sorrendje át lett rendezve.

Sorrend:

1. `UserSeeder`
2. `SportsAndTagSeeder`
3. `SpotSeeder`
4. `ImageSeeder`
5. `SpotSportsAndTagSeeder`
6. `SavedSpotSeeder`

Ez azért fontos, mert az egymásra épülő adatok csak így jönnek létre helyesen.

Példa:

* előbb kell user
* utána spot
* utána kép
* utána mentett spot

---

### UserSeeder átdolgozása

Fájl:

`backend/database/seeders/UserSeeder.php`

A korábbi kevés tesztfelhasználó helyett több tesztadat jön létre.

Létrehozott felhasználók:

* 10 sima teszt user
* 3 admin teszt user

A felhasználónevek és e-mailek generált, egységes mintát követnek.

Ez jobb tesztelhetőséget ad a profil, spot, mentés és jogosultsági funkciókhoz.

---

### SportsAndTagSeeder frissítés

Fájl:

`backend/database/seeders/SportsAndTagSeeder.php`

A tag lista bővült és pontosabb lett.

Példák:

* gördeszka
* roller
* BMX
* MTB
* kosárlabda
* foci
* frizbi
* grind
* rail
* pump track
* skatepark
* utcai spot
* kezdőbarát
* haladó

A seeder `firstOrCreate()` használatával dolgozik.

Ez csökkenti a duplikáció esélyét.

---

### SpotFactory megírása

Fájl:

`backend/database/factories/SpotFactory.php`

A spot factory már saját szókészletből generál adatokat.

Generált adatok:

* cím
* leírás
* magyarországi latitude
* magyarországi longitude

Fontos korlátok:

* cím maximum 60 karakter
* leírás maximum 2048 karakter
* koordináta magyarországi tartományban generálódik

Ez a frontend és backend validációhoz jobban illeszkedő tesztadatokat ad.

---

### SpotSeeder átdolgozása

Fájl:

`backend/database/seeders/SpotSeeder.php`

A spotok generálása nagyobb tesztadat mennyiséget hoz létre.

Működés:

* minden user legalább több spotot kap
* a maradék spotok véletlenszerű userhez kerülnek
* összesen 550 spot jön létre

Ez a lapozás, profil lista, spotkereső és mentési rendszer teszteléséhez kellett.

---

### ImageSeeder átdolgozása

Fájl:

`backend/database/seeders/ImageSeeder.php`

A seed képek már külső tárhelyről jönnek.

Képek forrása:

* külső `https://i.ibb.co/...` URL-ek

Működés:

* minden spot 1 és 10 közötti képet kap
* egy spoton belül a kiválasztott képek keverve kerülnek be
* a képek `sort_order` értéket kapnak
* a képek nem a repositoryban foglalják a helyet

Ez csökkenti a projekt méretét és könnyebbé teszi a seedelt spotok vizuális tesztelését.

---

### SavedSpotSeeder hozzáadása

Fájl:

`backend/database/seeders/SavedSpotSeeder.php`

Új seeder készült a mentett spotokhoz.

Működés:

* végigmegy az összes useren
* minden user több creator spotjaiból kap mentéseket
* `firstOrCreate()` miatt nem hoz létre felesleges duplikációt

Ez azért kellett, hogy a profil oldalon a mentett spot lista már seedelés után is tesztelhető legyen.

---

## Frontend módosítások

### Új közös SpotForm komponens

Fájl:

`frontend/src/components/SpotForm.vue`

A spot feltöltés és spot szerkesztés közös űrlapkomponenst kapott.

Cél:

* ne legyen duplikált form logika
* ugyanaz a validáció fusson feltöltésnél és szerkesztésnél
* a képfeltöltés és képsorrend kezelése egységes legyen

---

#### SpotForm fő funkciók

A komponens kezeli:

* cím megadását
* leírás megadását
* latitude mezőt
* longitude mezőt
* tag kiválasztást
* képfeltöltést
* képelőnézetet
* kép törlést
* képsorrend módosítást
* validációs hibákat
* mentés és megszakítás gombokat

---

#### Képkezelés a SpotForm komponensben

Támogatott működések:

* meglévő képek megjelenítése
* új képek hozzáadása
* új képek preview URL-lel való megjelenítése
* képek mozgatása balra
* képek mozgatása jobbra
* meglévő képek törlésre jelölése
* új képek azonnali eltávolítása
* object URL-ek felszabadítása komponens bezáráskor

Limit:

* maximum 10 kép
* maximum 3MB képenként
* csak JPG és PNG

---

#### Tag kezelés a SpotForm komponensben

A tag kiválasztás kattintással működik.

Limit:

* maximum 5 tag

Ha a user túllépi:

* hibaüzenet jelenik meg
* nem kerül hozzáadásra új tag

---

#### Submit adatok

A komponens submitkor átadja:

* spot alapadatok
* kiválasztott tagek
* új képek
* törlésre jelölt meglévő kép ID-k
* végleges képsorrend

Ez alapján ugyanaz a komponens használható új spot létrehozására és meglévő spot szerkesztésére is.

---

### Feltöltés oldal egyszerűsítése

Fájl:

`frontend/src/pages/feltoltes.vue`

A feltöltés oldal már a közös `SpotForm` komponenst használja.

Feladata:

* bejelentkezés ellenőrzése
* tagek betöltése
* spot létrehozása
* új képek feltöltése
* sikeres feltöltés után átirányítás a spot oldalára

Sikeres feltöltés után:

* toast üzenet jelenik meg
* redirect történik a létrehozott spot részletező oldalára

---

### Új spot szerkesztő oldal

Fájl:

`frontend/src/pages/spotok/[id]/szerkesztes.vue`

Új oldal készült a meglévő spotok szerkesztéséhez.

Route cél:

* `/spotok/{id}/szerkesztes`

Az oldal betölti:

* a spot adatait
* a spot képeit
* a spot tagjeit
* a bejelentkezett user adatait
* a tagek listáját

---

#### Szerkesztési jogosultság frontend oldalon

Szerkeszthet:

* admin
* a spot tulajdonosa

Ha nincs bejelentkezve:

* átirányítás a bejelentkezés oldalra

Ha nincs jogosultsága:

* hibaüzenet jelenik meg
* visszairányítás a spot részletező oldalára

A backend ettől függetlenül külön is ellenőrzi a jogosultságot.

---

#### Spot mentés szerkesztéskor

Mentéskor több lépés fut le.

Lépések:

1. spot alapadatok frissítése
2. törlésre jelölt régi képek törlése
3. új képek feltöltése
4. végleges képsorrend mentése
5. friss spot adatok betöltése
6. átirányítás a spot részletező oldalára
7. sikeres toast megjelenítése

Ez biztosítja, hogy az adatok, képek és sorrend együtt frissüljenek.

---

### SpotStore frissítés

Fájl:

`frontend/src/stores/SpotStore.js`

Új vagy módosított metódusok:

* `getSpots()`
* `getSpot(id)`
* `createSpot(spotData)`
* `updateSpot(id, spotData)`
* `updateImageOrder(id, imageOrder)`
* `deleteSpot(id)`

Fontos működés:

* a `deleteSpot()` a helyi state-ből is eltávolítja a törölt spotot
* az `updateSpot()` frissíti a spot adatait
* az `updateImageOrder()` a backend képsorrend endpointját hívja

---

### ImageStore frissítés

Fájl:

`frontend/src/stores/ImageStore.js`

Az `uploadImage()` metódus bővült.

Új paraméter:

* `sortOrder`

A feltöltés `FormData` objektummal történik.

Küldött adatok:

* `spot_id`
* `image`
* `sort_order`, ha van érték

Ez lehetővé teszi, hogy az újonnan feltöltött képek is rögtön sorrendben kerüljenek mentésre.

---

### SavedSpotStore frissítés

Fájl:

`frontend/src/stores/SavedSpotStore.js`

Új metódus:

* `removeBySpotId(spotId)`

Feladata:

* törölt spot esetén a mentett spot state-ből is eltávolítani az adott elemet

Ez főleg a profil oldali törlés után fontos.

---

### Spot részletező oldal frissítése

Fájl:

`frontend/src/pages/spotok/[id].vue`

Új vagy módosított működések:

* a részletező oldal kezeli a szerkesztő alroute megjelenítését
* ha szerkesztő oldal aktív, akkor a részletező tartalom helyett a szerkesztő jelenik meg
* a spot tulajdonosánál saját spot jelölés jelenik meg
* a cím tördelése javítva lett
* a képek és tagek megjelenítése az új adatszerkezethez igazodik
* a kép carousel új funkciókkal bővült, a lapozáson kívül nyomonkövethető, hogy hol tartunk a képek között

Szerkesztésre navigáló útvonal:

* `/spotok/{id}/szerkesztes`

---

### Profil oldal frissítése

Fájl:

`frontend/src/pages/profil.vue`

A profil oldal jelentősen bővült.

Fő részek:

* felhasználói adatok megjelenítése
* feltöltött spotok listája
* mentett spotok listája
* lapozás
* törlés saját spotoknál
* loading állapotok
* üres lista állapotok
* toast visszajelzések

---

#### Feltöltött spotok a profil oldalon

A profil oldal kilistázza az aktuális user által feltöltött spotokat.

A lista a spotkeresőben használt kártyás megjelenítésre épül.

Kezelt állapotok:

* betöltés
* üres lista
* több oldalnyi találat
* törlés folyamatban

---

#### Mentett spotok a profil oldalon

A profil oldal a mentett spotokat is külön listázza.

Ehhez a backend már a mentett spothoz tartozó spot adatokat, képeket, tageket és feltöltőt is visszaadja.

Ez azért kellett, hogy a mentett spotok teljes értékű kártyaként jelenhessenek meg.

---

#### Spot törlés profilból

A saját feltöltött spot törölhető a profil oldalról.

Működés:

* a user megerősítő kérdést kap
* törlés közben az adott gomb disabled állapotba kerül
* sikeres törlés után a spot eltűnik a listából
* ha a spot mentve is volt, akkor a mentett listából is kikerülhet
* sikeres és hibás esetben toast jelenik meg

Jogosultsági hiba esetén külön üzenet jelenik meg.

---

#### Profil oldali lapozás

A profil oldalon lapozás működik a feltöltött spotokra és a mentett spotokra is.

Működés:

* előző oldal
* következő oldal
* aktuális oldal kiemelése
* közeli oldalszámok megjelenítése
* törlés után az oldal korrigálása
* lapváltáskor az oldal tetejére görgetés

Ez a nagyobb seedelt adatmennyiség miatt lett fontos.

---

### BaseSpotBlock frissítés

Fájl:

`frontend/src/components/BaseSpotBlock.vue`

A spot kártya megjelenítése igazodott az új működéshez.

Fontosabb célok:

* képek egységes kezelése
* feltöltő nevének stabil megjelenítése
* spot részletező oldalra navigálás
* fallback kép használata, ha nincs kép

---

### Új 404-es oldal

Fájl:

`frontend/src/pages/[...all].vue`

Új catch-all oldal készült a nem létező útvonalakhoz.

Feladata:

* jelezni, hogy az oldal nem található
* visszavezetni a usert a főoldalra
* egységes FlowFinder megjelenést adni hibás URL esetén

Megjelenő elemek:

* 404 cím
* rövid magyarázó szöveg
* vissza a főoldalra gomb
* FlowFinder kép

---

### Új frontend assetek

Új vagy érintett képi elemek:

* `frontend/src/assets/img/edit-black.svg`
* `frontend/src/assets/img/trash-white.svg`

Felhasználás:

* szerkesztés gomb
* törlés gomb
* profil és spot műveletek vizuális jelölése

---

## Egyéb fontos javítások

### Külső képek kezelése

A projekt már nem lokális storage képekkel seedek.

A backend és a frontend is úgy lett módosítva, hogy működjön:

* lokális feltöltött képpel
* külső URL-ből érkező seed képpel

Ez a seedelés miatt fontos, mert így a tesztadatokhoz nem kell nagy mennyiségű képfájlt a repositoryban tárolni.

---

### FormRequest blokkolás megszüntetése

Több request osztályban bekerült az `authorize()` metódus.

Ez azért fontos, mert enélkül a Laravel FormRequest alapértelmezetten jogosultsági hibával megakaszthat bizonyos kéréseket.

Érintett részek:

* spot létrehozás
* spot szerkesztés
* képfeltöltés
* képsorrend módosítás

---

### Jogosultsági logika pontosítása

A spot kezelésben egységesebb lett a jogosultsági logika.

Admin:

* szerkeszthet spotot
* törölhet spotot

Tulajdonos:

* szerkesztheti a saját spotját
* törölheti a saját spotját

Másik user:

* nem szerkeszthet
* nem törölhet

A frontend csak akkor mutatja a műveleti gombokat, ha a user jogosult lehet rá.

A backend ettől függetlenül külön ellenőrzi a jogosultságot.

---