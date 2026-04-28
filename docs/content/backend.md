# FlowFinder - Backend fejlesztői dokumentáció

Ez a dokumentum a FlowFinder backend felépítését írja le fejlesztői szemmel. A cél az, hogy egy új fejlesztő gyorsan átlássa, milyen adatbázis táblák, modellek, kontrollerek, route-ok, validációk, erőforrások és seederek vannak a projektben.

## Tartalomjegyzék

- [1. Backend mappaszerkezet](#1-backend-mappaszerkezet)
- [2. Adatbázis modell](#2-adatbázis-modell)
- [3. Táblák és kapcsolatok](#3-táblák-és-kapcsolatok)
- [4. Modellek](#4-modellek)
- [5. API Resource réteg](#5-api-resource-réteg)
- [6. Form Request validációk](#6-form-request-validációk)
- [7. Kontrollerek működése](#7-kontrollerek-működése)
- [8. API route-ok](#8-api-route-ok)
- [9. Seederek és tesztadatok](#9-seedek-és-tesztadatok)
- [10. Fájlfeltöltés és képek kezelése](#10-fájlfeltöltés-és-képek-kezelése)
- [11. Hitelesítés és jogosultságkezelés](#11-hitelesítés-és-jogosultságkezelés)

## 1. Backend mappaszerkezet

A backend legfontosabb fejlesztői fájljai:

| Útvonal | Tartalom |
| --- | --- |
| `backend/routes/api.php` | API route-ok definíciója |
| `backend/app/Models/` |  Modellek |
| `backend/app/Http/Controllers/` | API kontrollerek |
| `backend/app/Http/Requests/` | Validációs Form Request osztályok |
| `backend/app/Http/Resources/` | JSON response formázó Resource osztályok |
| `backend/database/migrations/` | Adatbázis táblák létrehozása és módosítása |
| `backend/database/seeders/` | Alap és tesztadatok feltöltése |
| `backend/database/factories/` | Randomizált tesztadatok generálása |
| `backend/tests/Feature/` | Laravel feature tesztek |

## 2. Adatbázis modell

Az adatbázismodell képként is szerepel a dokumentációban. 

![Adatbázis modell](../public/adatbazis_modell.png)

A képfájl helye:

```text
docs/public/adatbazis_modell.png
```

Az adatbázis export/dump fájlja is szerepel a dokumentációban. 

A fájl helye:

```text
docs/public/FlowFinder-dump.sql
```

Az adatbázis központi elemei:

- felhasználók,
- spotok,
- sportok és tagek,
- spotokhoz tartozó képek,
- mentett spotok,

## 3. Táblák és kapcsolatok

### `users`

A felhasználók adatait tárolja.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `username` | Felhasználónév, egyedi |
| `email` | Email cím, egyedi |
| `password` | Titkosított jelszó |
| `remember_token` | Laravel remember token |
| `role` | Jogosultsági szerepkör, például `user` vagy `admin` |

Kapcsolatok:

- egy felhasználó több spotot hozhat létre,
- egy felhasználó több spotot menthet el.

### `spots`

A felhasználók által létrehozott sporthelyeket tárolja.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `created_by` | Létrehozó felhasználó azonosítója |
| `title` | Spot neve |
| `description` | Spot leírása |
| `latitude` | Földrajzi szélesség |
| `longitude` | Földrajzi hosszúság |

Kapcsolatok:

- egy spot egy felhasználóhoz tartozik,
- egy spothoz több kép tartozhat,
- egy spothoz több sport vagy tag kapcsolható,
- egy spotot több felhasználó is elmenthet.

### `images`

A spotokhoz feltöltött képeket tárolja.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `spot_id` | Kapcsolódó spot |
| `path` | Kép elérési útja a public storage-ban vagy külső URL |
| `sort_order` | Képek sorrendje egy spoton belül |

A `sort_order` miatt a spot képei rendezhetőek. Ez fontos a frontend szerkesztő felületén, ahol a képek sorrendje módosítható.

### `sports_and_tags`

A sportágakat és címkéket tárolja egy közös táblában.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `name` | Sport vagy tag neve |
| `type` | `sport` vagy `tag` |

A projektben a sportok és a tagek egy táblában vannak kezelve, mert a frontend szűrésnél és spot feltöltésnél hasonló módon használja őket.

### `spot_sports_and_tag`

Pivot tábla a spotok és sportok/tagek között.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `spot_id` | Kapcsolódó spot |
| `sports_and_tag_id` | Kapcsolódó sport vagy tag |
| `created_at`, `updated_at` | Időbélyegek |

Ez a tábla valósítja meg a több a többhöz kapcsolatot a spotok és a sport/tag elemek között.

### `saved_spots`

A mentett spotokat tárolja.

Fő mezők:

| Mező | Jelentés |
| --- | --- |
| `id` | Egyedi azonosító |
| `user_id` | Mentést végző felhasználó |
| `spot_id` | Mentett spot |

A `user_id` és `spot_id` páros egyedi, ezért ugyanaz a felhasználó ugyanazt a spotot csak egyszer mentheti el.

## 4. Modellek

### `User`

Fájl:

```text
backend/app/Models/User.php
```

Feladata:

- felhasználói adatok kezelése,
- Sanctum tokenek kezelése,
- spotokkal és mentett spotokkal való kapcsolatok biztosítása.

Fontos kapcsolatok:

```php
public function spots()
public function savedSpots()
```

### `Spot`

Fájl:

```text
backend/app/Models/Spot.php
```

Feladata:

- spot adatainak kezelése,
- létrehozó felhasználó kapcsolása,
- képek kezelése,
- sportok és tagek kapcsolása,
- mentések kapcsolása.

Fontos kapcsolatok:

```php
public function createdBy()
public function images()
public function sportsAndTags()
public function savedByUsers()
public function savedSpots()
```

### `Image`

Fájl:

```text
backend/app/Models/Image.php
```

Feladata:

- spothoz tartozó kép tárolása,
- kép elérési útjának kezelése,
- sorrend kezelése.

Fontos kapcsolat:

```php
public function spot()
```

### `SportsAndTag`

Fájl:

```text
backend/app/Models/SportsAndTag.php
```

Feladata:

- sportok és tagek közös kezelése,
- spotokhoz való több a többhöz kapcsolódás biztosítása.

Fontos kapcsolat:

```php
public function spots()
```

### `SavedSpot`

Fájl:

```text
backend/app/Models/SavedSpot.php
```

Feladata:

- egy felhasználó és egy mentett spot kapcsolatának kezelése.

Fontos kapcsolatok:

```php
public function user()
public function spot()
```

## 5. API Resource réteg

A backend nem nyersen adja vissza a Modelleket, hanem Resource osztályokon keresztül formázza a JSON válaszokat.

| Resource | Fájl | Szerep |
| --- | --- | --- |
| `UserResource` | `app/Http/Resources/UserResource.php` | Felhasználói adatok egységes JSON formában |
| `SpotResource` | `app/Http/Resources/SpotResource.php` | Spot adatok kapcsolt képekkel, tagekkel, létrehozóval |
| `ImageResource` | `app/Http/Resources/ImageResource.php` | Képadatok, `path` és publikus `url` mező |
| `SportsAndTagResource` | `app/Http/Resources/SportsAndTagResource.php` | Sport és tag adatok |
| `SavedSpotResource` | `app/Http/Resources/SavedSpotResource.php` | Mentett spot adatok, kapcsolt spottal és felhasználóval |

### `SpotResource`

A `SpotResource` a spot fő adatai mellett visszaadja:

- a létrehozó felhasználót,
- a kapcsolt képeket,
- a kapcsolt sportokat és tageket.

Ez azért fontos, mert a frontend spotkereső, profil és részletező oldalai ezekből az adatokból építik fel a kártyákat és a részletes nézeteket.

### `ImageResource`

Az `ImageResource` külön kezeli a `path` és `url` mezőt. A `url` mező a `Storage::disk('public')->url($path)` alapján készül, ha a kép lokálisan került feltöltésre.

Ha a kép külső URL, akkor a frontend képes közvetlenül is használni az elérési útvonalat.

## 6. Form Request validációk

A projekt külön Form Request osztályokat használ a bejövő adatok validálására.

| Request | Feladat |
| --- | --- |
| `LoginRequest` | Bejelentkezési adatok ellenőrzése |
| `RegistrationRequest` | Regisztrációs adatok ellenőrzése |
| `StoreSpotRequest` | Új spot létrehozásának validálása |
| `StoreImageRequest` | Képfeltöltés validálása |
| `StoreSavedSpotRequest` | Spot mentésének validálása |

### Bejelentkezés

A bejelentkezésnél kötelező:

- `email`,
- `password`.

### Regisztráció

A regisztrációnál kötelező:

- `username`, legalább 3, legfeljebb 255 karakter, egyedi,
- `email`, valid email, egyedi,
- `password`, legalább 8 karakter, megerősítéssel.

### Spot létrehozás

Új spot létrehozásánál kötelező:

- `name`,
- `image`, amely jpg, jpeg vagy png fájl lehet.
- `latitude`,
- `longitude`.

Opcionális:

- `description`,
- `sports_and_tags`, amely ID-k tömbje.

## 7. Kontrollerek működése

### `AuthController`

Fájl:

```text
backend/app/Http/Controllers/AuthController.php
```

Feladata:

- bejelentkezés,
- aktuális felhasználó lekérése,
- kijelentkezés,
- Sanctum token létrehozása és törlése.

Fő metódusok:

| Metódus | Szerep |
| --- | --- |
| `login()` | Email és jelszó alapján beléptet, majd Bearer tokent ad vissza |
| `user()` | Visszaadja az aktuálisan bejelentkezett felhasználót |
| `logout()` | Törli az aktuális access tokent |

### `RegistrationController`

Feladata:

- új felhasználó létrehozása,
- jelszó hash-elése,
- alapértelmezett `user` szerepkör beállítása.

### `UserController`

Feladata:

- felhasználók listázása,
- egy felhasználó lekérése.

A jelenlegi backendben az `index` és `show` végpont publikus route-ként szerepel. Ez a projekt jelenlegi állapota szerint dokumentált működés.

### `SpotController`

Feladata:

- spotok listázása,
- spot részleteinek lekérése,
- spot létrehozása,
- spot módosítása,
- spot törlése,
- spot képsorrend frissítése.

Fontos működések:

- a listázás betölti a létrehozót, képeket és sport/tag kapcsolatokat,
- létrehozáskor a `created_by` mező az aktuális bejelentkezett felhasználó azonosítója lesz,
- módosításkor csak a spot tulajdonosa vagy admin módosíthat,
- törléskor csak a spot tulajdonosa vagy admin törölhet,
- képsorrend módosításakor csak a spothoz tartozó képek sorrendje frissül.

### `ImageController`

Feladata:

- képek listázása,
- egy kép lekérése,
- kép feltöltése,
- kép törlése.

Fontos működések:

- a képek a `public` diskre kerülnek,
- az elérési út az adatbázisban a `path` mezőben tárolódik,
- új kép esetén a `sort_order` automatikusan a spot eddigi utolsó képe után kerül,
- kép törlésénél a backend a fájlt is törli a storage-ból.

### `SportsAndTagController`

Feladata:

- sportok és tagek listázása,
- egy sport vagy tag lekérése.

A sportok és tagek jelenleg seederből kerülnek az adatbázisba, frontend oldalon ezek alapján lehet szűrni és spothoz címkéket választani.

### `SavedSpotController`

Feladata:

- bejelentkezett felhasználó mentett spotjainak listázása,
- spot mentése,
- mentett spot részleteinek lekérése,
- mentés törlése.

Fontos működések:

- a mentések felhasználóhoz kötöttek,
- ugyanazt a spotot egy felhasználó csak egyszer mentheti,
- a törlésnél a backend ellenőrzi, hogy a mentés az aktuális felhasználóhoz tartozik-e.

## 8. API route-ok

Az API route-ok fő fájlja:

```text
backend/routes/api.php
```

### Publikus végpontok

| Metódus | Végpont | Kontroller | Feladat |
| --- | --- | --- | --- |
| `POST` | `/registration` | `RegistrationController@registration` | Új felhasználó regisztrálása |
| `POST` | `/login` | `AuthController@login` | Bejelentkezés és token generálás |
| `GET` | `/users` | `UserController@index` | Felhasználók listázása |
| `GET` | `/users/{user}` | `UserController@show` | Egy felhasználó lekérése |
| `GET` | `/spots` | `SpotController@index` | Spotok listázása |
| `GET` | `/spots/{spot}` | `SpotController@show` | Egy spot részletes lekérése |
| `GET` | `/images` | `ImageController@index` | Képek listázása |
| `GET` | `/images/{image}` | `ImageController@show` | Egy kép lekérése |
| `GET` | `/sports-and-tags` | `SportsAndTagController@index` | Sportok és tagek listázása |
| `GET` | `/sports-and-tags/{sports_and_tag}` | `SportsAndTagController@show` | Egy sport vagy tag lekérése |

### Bejelentkezést igénylő végpontok

Ezek a route-ok `auth:sanctum` middleware mögött vannak.

| Metódus | Végpont | Kontroller | Feladat |
| --- | --- | --- | --- |
| `GET` | `/user` | `AuthController@user` | Aktuális bejelentkezett felhasználó lekérése |
| `POST` | `/logout` | `AuthController@logout` | Kijelentkezés, token törlés |
| `POST` | `/spots` | `SpotController@store` | Új spot létrehozása |
| `PUT/PATCH` | `/spots/{spot}` | `SpotController@update` | Spot módosítása |
| `DELETE` | `/spots/{spot}` | `SpotController@destroy` | Spot törlése |
| `PATCH` | `/spots/{spot}/images/order` | `SpotController@updateImageOrder` | Spot képeinek sorrendezése |
| `POST` | `/images` | `ImageController@store` | Új kép feltöltése spothoz |
| `DELETE` | `/images/{image}` | `ImageController@destroy` | Kép törlése |
| `GET` | `/saved-spots` | `SavedSpotController@index` | Saját mentett spotok listázása |
| `POST` | `/saved-spots` | `SavedSpotController@store` | Spot mentése |
| `GET` | `/saved-spots/{saved_spot}` | `SavedSpotController@show` | Egy mentett spot lekérése |
| `DELETE` | `/saved-spots/{saved_spot}` | `SavedSpotController@destroy` | Mentett spot törlése |

## 9. Seedek és tesztadatok

A seederek célja, hogy a projekt indítás után ne üres adatbázissal induljon, hanem legyenek felhasználók, spotok, tagek, képek és mentett spotok.

Fő seeder fájlok:

| Seeder | Feladat |
| --- | --- |
| `DatabaseSeeder` | Seederek központi futtatása |
| `UserSeeder` | Teszt felhasználók és adminok létrehozása |
| `SpotSeeder` | Spotok generálása |
| `SportsAndTagSeeder` | Sportok és tagek feltöltése |
| `SpotSportsAndTagSeeder` | Spotok és tagek összekapcsolása |
| `ImageSeeder` | Képek hozzárendelése spotokhoz |
| `SavedSpotSeeder` | Mentett spotok generálása |

### Teszt felhasználók

A `UserSeeder` több fix felhasználót hoz létre. A pontos bejelentkezési adatok a fő fejlesztői dokumentációban szerepelnek `docs/content/fejlesztoi-dokumentacio.md`, de backend szinten ezek az adatok a `backend/database/seeders/UserSeeder.php` fájlban találhatók.

A szerepkörök:

- `user`,
- `admin`.

A jelszavak hash-elve kerülnek az adatbázisba.

### Spot seeder

A `SpotSeeder` randomizált spotokat hoz létre. A koordináták Magyarország környéki tartományból generálódnak, ezért az adatok futtatásonként eltérhetnek.

Ez fontos dokumentációs szempontból: a seederben factory-k és random generálás is van, ezért az adatbázis exportban és friss seed után az adatok nem minden futtatásnál lesznek teljesen azonosak.

### Sportok és tagek

A `SportsAndTagSeeder` fix sportokat és tageket tölt be. Ezeket használja a frontend:

- spot keresésnél,
- spot feltöltésnél,
- spot szerkesztésnél,
- spot részletező nézetben.

### Kép seeder

Az `ImageSeeder` spotokhoz rendel képeket. A projektben külső kép URL-ek is szerepelnek, ezért nem kell minden seedelt képet fizikailag a repositoryban tárolni.

## 10. Fájlfeltöltés és képek kezelése

A felhasználó spot feltöltés után képeket tud hozzárendelni a spothoz.

Backend oldali folyamat:

1. A frontend `multipart/form-data` kérést küld az `/images` végpontra.
2. A backend validálja a képet a `StoreImageRequest` alapján.
3. A kép a Laravel `public` diskre kerül.
4. Az adatbázisba bekerül a kép `path` értéke.
5. A `sort_order` automatikusan beállításra kerül.
6. Az API válaszban az `ImageResource` visszaadja a kép publikus URL-jét is.

Fejlesztésnél fontos, hogy a storage link létezzen:

```bash
php artisan storage:link
```

Docker környezetben ez tipikusan a backend konténerben futtatandó.

## 11. Hitelesítés és jogosultságkezelés

A backend Sanctum token alapú hitelesítést használ.

Bejelentkezéskor:

1. A frontend elküldi az email és jelszó párost.
2. A backend ellenőrzi az adatokat.
3. Sikeres belépésnél token készül.
4. A frontend ezt Bearer tokenként küldi a védett API kérésekhez.

A backend szintjén fontos jogosultsági szabályok:

- csak bejelentkezett felhasználó tölthet fel spotot,
- csak bejelentkezett felhasználó menthet spotot,
- csak bejelentkezett felhasználó tölthet fel képet,
- spot módosításnál és törlésnél a tulajdonos vagy admin jogosult,
- mentett spot törlésénél csak a saját mentés törölhető.

A szerepkör jelenleg a `users.role` mezőben található. A frontend AuthStore-ban van `isAdmin` computed érték, de a jelenlegi felhasználói felületen a fő funkciók elsősorban normál felhasználói működésre épülnek.

---

### AI és képi tartalmak

- A projektben használt egyes képek mesterséges intelligenciával készültek.
- A képgenerálásban és a dokumentáció egyes részeinek megfogalmazásában a **ChatGPT** is segítséget nyújtott.
- Az AI segítségével készült tartalmakat a projekt készítői ellenőrizték, javították és a projekt igényeihez igazították.