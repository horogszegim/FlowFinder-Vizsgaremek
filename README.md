## Auth rendszer fejlesztői dokumentáció (Frontend + Backend)

Ez a kezdetleges fejlesztői dokumentáció részlet a projekt AUTH rendszerét írja le:
felhasználó regisztráció, be- és kijelentkezés, tokenekkel való frontend session kezelés.

---

### Backend (Laravel)

#### Áttekintés
- Auth megoldás: Laravel Sanctum (personal access token)
- Token kizárólag bejelentkezéskor jön létre
- A backend nem kezel kijelentkezés route-ot
- Védett végpont Sanctum middleware-rel van ellátva

---

#### API route-ok
Fájl: `backend/routes/api.php`

- GET `/api/user`
  - Middleware: `auth:sanctum`
  - Visszatérési érték: az aktuálisan autentikált user

- POST `/api/registration`
  - Controller: `RegistrationController@store`
  - Validáció: `StoreUserRequest`

- POST `/api/login`
  - Controller: `AuthController@authenticate`
  - Validáció: `LoginRequest`
 
 ## Backend controller logika

#### Regisztráció
Fájl: `backend/app/Http/Controllers/RegistrationController.php`

Metódus:
- `store(StoreUserRequest $request): JsonResponse`

Folyamat:
- `$data = $request->validated()`
- `User::create($data)`

Válasz:
```json
{
	"data": {
		"message": "A(z) <email> sikeresen regisztrált"
	}
}
```

Megjegyzés:
- A regisztráció során nem készül token
- A token létrehozása bejelentkezéskor történik

---

#### Bejelentkezés
Fájl: `backend/app/Http/Controllers/AuthController.php`

Metódus:
- `authenticate(LoginRequest $request)`

Folyamat:
- `$data = $request->validated()`
- `Auth::attempt($data)` ellenőrzi az email–jelszó párost
- Siker esetén token generálás történik

Sikeres válasz:
```json
{
	"data": {
		"token": "<plainTextToken>"
	}
}
```

Hibás belépés:
- HTTP státusz: 401
- Válasz:
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
- email
  - required
  - email:rfc,dns
  - max:255

- password
  - required
  - string
  - min:8
  - max:255

---

#### StoreUserRequest (regisztráció)
Fájl: `backend/app/Http/Requests/StoreUserRequest.php`

Mezők:

- username
  - required
  - string
  - min:3
  - max:24
  - unique:users,username
  - regex: `^[a-z][a-z0-9_]{2,23}$`
  - első karakter kisbetű
  - csak kisbetű, szám és aláhúzás (_)

- email
  - required
  - email:rfc,dns
  - unique:users,email
  - max:255

- password
  - required
  - string
  - min:8
  - max:255
  - confirmed
  - legalább egy kisbetű
  - legalább egy nagybetű
  - legalább egy szám
  - legalább egy speciális karakter (!, @, #, $, %, &, _)
  - csak engedélyezett karakterek

### User modell

Fájl: `backend/app/Models/User.php`

Fontos elemek:
- fillable mezők:
  - username
  - email
  - password

- hidden mezők:
  - password
  - remember_token

- casts:
  - password: hashed
    - a jelszó automatikusan hash-elésre kerül mentéskor

- Sanctum:
  - HasApiTokens trait használatban van

---

### Adatbázis migrációk

#### Users tábla
Fájl: `backend/database/migrations/0001_01_01_000000_create_users_table.php`

Mezők:
- id
- username (string, 24, unique)
- email (unique)
- password
- remember_token
- timestamps

#### Role mező
Fájl: `backend/database/migrations/2026_02_19_201614_add_role_to_users_table.php`

- role
  - string
  - max 10 karakter
  - default érték: "user"

#### Sanctum token tábla
- personal_access_tokens
- Laravel Sanctum alap migráció

---

### Frontend (Vue 3 + Pinia + FormKit)

#### Környezeti változók
Fájl: `frontend/.env.example`

- VITE_APP_NAME
- VITE_BACKEND_URL
- VITE_FRONTEND_URL

---

### HTTP kliens
Fájl: `frontend/src/utils/http.mjs`

- Axios instance
- baseURL: VITE_BACKEND_URL
- JSON alapú kérések
- Authorization header nem kerül automatikusan hozzáadásra

---

### Auth Store (Pinia)
Fájl: `frontend/src/stores/AuthStore.js`

State:
- token
  - localStorage-ból inicializálva

Computed:
- isAuthenticated
  - boolean a token megléte alapján

Metódusok:
- setToken(token)
  - token mentése store-ba
  - localStorage frissítése

- logout()
  - token nullázása
  - localStorage törlése

- login(payload)
  - POST /login
  - token mentése siker esetén

- register(payload)
  - POST /registration
  - siker után automatikus login

---

### Regisztráció oldal
Fájl: `frontend/src/pages/regisztracio.vue`

FormKit form használatban van.

Mezők:
- username
  - required
  - min 3, max 24 karakter
  - első karakter kisbetű
  - nincs nagybetű
  - csak kisbetű, szám és _

- email
  - required
  - email
  - max 255 karakter

- password
  - required
  - min 8, max 255 karakter
  - kisbetű, nagybetű, szám kötelező
  - speciális karakter kötelező (!@#$%&_)

- password_confirmation
  - kötelező
  - egyeznie kell a password mezővel

Submit:
- auth.register()
- router redirect a főoldalra

---

### Bejelentkezés oldal
Fájl: `frontend/src/pages/bejelentkezes.vue`

Mezők:
- email (required)
- password (required)

Submit:
- auth.login()
- siker esetén redirect

Hiba:
- (Egyelőre?) generikus hibaüzenet jelenik meg

---

### FormKit globális konfiguráció
Fájl: `frontend/src/main.js`

- FormKit plugin regisztrálva
- Globális class beállítások:
  - form layout
  - label stílus
  - input fókusz és border
  - validációs hibaüzenetek kinézete