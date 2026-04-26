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