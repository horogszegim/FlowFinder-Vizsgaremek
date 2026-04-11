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