# FlowFinder - Frontend fejlesztői dokumentáció

Ez a dokumentum a FlowFinder frontend felépítését mutatja be. A cél az, hogy egy fejlesztő átlássa az oldalak, komponensek, store-ok, route guardok, API kommunikáció és felhasználói felület logikai működését.

A telepítés és indítás részletes leírása külön fájlban található: [telepites-es-inditas.md](./telepites-es-inditas.md)

## Tartalomjegyzék

- [1. Frontend mappaszerkezet](#1-frontend-mappaszerkezet)
- [2. Routing és route guardok](#2-routing-és-route-guardok)
- [3. API kommunikáció](#3-api-kommunikáció)
- [4. Pinia store-ok](#4-pinia-store-ok)
- [5. Oldalak részletesen](#5-oldalak-részletesen)
- [6. Fő komponensek](#6-fő-komponensek)
- [7. Képek kezelése frontend oldalon](#7-képek-kezelése-frontend-oldalon)
- [8. Tagek és színezés](#8-tagek-és-színezés)
- [9. Hibakezelés és visszajelzések](#9-hibakezelés-és-visszajelzések)
- [10. Stílus és reszponzivitás](#10-stílus-és-reszponzivitás)

## 1. Frontend mappaszerkezet

Fontosabb frontend útvonalak:

| Útvonal | Tartalom |
| --- | --- |
| `frontend/src/main.js` | Vue alkalmazás indítása |
| `frontend/src/App.vue` | Alkalmazás fő belépési komponense |
| `frontend/src/pages/` | Oldalak, fájl alapú route-ok |
| `frontend/src/components/` | Újrahasznosítható komponensek |
| `frontend/src/components/layout/` | Fejléc és lábléc komponensek |
| `frontend/src/layouts/` | Oldal layoutok |
| `frontend/src/stores/` | Pinia store-ok |
| `frontend/src/router/` | Router konfiguráció és guardok |
| `frontend/src/utils/` | Segédfájlok, API kliens, tag színek |
| `frontend/src/locales/` | Többnyelvű szövegek |
| `frontend/src/assets/` | CSS és statikus assetek |

## 2. Routing és route guardok

A projektben az útvonalak a fájlstruktúrából jönnek létre az `unplugin-vue-router` segítségével.

Router fájl:

```text
frontend/src/router/index.js
```

A router két guardot használ:

| Guard | Fájl | Feladat |
| --- | --- | --- |
| `authGuard` | `router/guards/AuthGuard.js` | Védett és vendég-only oldalak kezelése |
| `setTitle` | `router/guards/SetTitleGuard.mjs` | Böngésző title beállítása route meta alapján |

### Route-ok

| Oldal | Fájl | Route név | Jogosultság |
| --- | --- | --- | --- |
| Kezdőlap | `pages/index.vue` | `kezdolap` | publikus |
| Bejelentkezés | `pages/bejelentkezes.vue` | `bejelentkezes` | csak vendég |
| Regisztráció | `pages/regisztracio.vue` | `regisztracio` | csak vendég |
| Spotkereső | `pages/spotkereso.vue` | `spotkereso` | publikus |
| Spot feltöltés | `pages/feltoltes.vue` | `feltoltes` | bejelentkezés kell |
| Profil | `pages/profil.vue` | `profil` | bejelentkezés kell |
| Spot részletek | `pages/spotok/[id].vue` | `spot-megjelenites` | publikus |
| Spot szerkesztés | `pages/spotok/[id]/szerkesztes.vue` | `spot-szerkesztes` | bejelentkezés kell |
| 404 oldal | `pages/[...all].vue` | `not-found` | publikus |

### `authGuard`

Az `authGuard` működése:

- ha az oldal `requiresAuth: true` meta értéket kapott, csak bejelentkezett felhasználó érheti el,
- ha az oldal `guestOnly: true`, bejelentkezett felhasználó nem érheti el,
- hibás hozzáférésnél toast üzenetet jelenít meg,
- bejelentkezés nélkül védett oldal esetén a felhasználót a bejelentkezés oldalra viszi.

### `setTitle`

A `setTitle` a route `meta.title` értéke alapján állítja a böngésző címét:

```text
{oldal címe} | {VITE_APP_NAME}
```

## 3. API kommunikáció

Az API kliens fájlja:

```text
frontend/src/utils/http.mjs
```

A projekt Axios példányt használ:

- `baseURL`: `import.meta.env.VITE_BACKEND_URL`,
- alap fejléc: `Accept: application/json`,
- request interceptor: ha van token a `localStorage`-ban, akkor beállítja az `Authorization: Bearer ...` fejlécet.

Ez biztosítja, hogy a bejelentkezést igénylő API kérésekhez automatikusan hozzákerüljön a token.

## 4. Pinia store-ok

### `AuthStore.js`

Fájl:

```text
frontend/src/stores/AuthStore.js
```

Feladata:

- bejelentkezési token tárolása,
- felhasználó adatainak tárolása,
- login, logout és register műveletek,
- aktuális felhasználó lekérése,
- bejelentkezett állapot ellenőrzése,
- admin szerepkör ellenőrzése.

Fontos állapotok és computed értékek:

| Név | Szerep |
| --- | --- |
| `token` | Sanctum token a localStorage alapján |
| `user` | Aktuális felhasználó objektum |
| `isAuthenticated` | Van-e token |
| `isAdmin` | A felhasználó szerepköre admin-e |

### `SpotStore.js`

Fájl:

```text
frontend/src/stores/SpotStore.js
```

Feladata:

- spotok listázása,
- egy spot lekérése,
- új spot létrehozása,
- spot módosítása,
- spot törlése,
- képsorrend frissítése.

Fő API végpontok:

- `GET /spots`,
- `GET /spots/{id}`,
- `POST /spots`,
- `PUT /spots/{id}`,
- `DELETE /spots/{id}`,
- `PATCH /spots/{id}/images/order`.

### `ImageStore.js`

Fájl:

```text
frontend/src/stores/ImageStore.js
```

Feladata:

- képek lekérése,
- kép feltöltése `multipart/form-data` formátumban,
- kép törlése.

Fő API végpontok:

- `GET /images`,
- `POST /images`,
- `DELETE /images/{id}`.

### `SportsAndTagStore.js`

Fájl:

```text
frontend/src/stores/SportsAndTagStore.js
```

Feladata:

- sportok és tagek listázása,
- egy sport vagy tag lekérése.

Fő API végpontok:

- `GET /sports-and-tags`,
- `GET /sports-and-tags/{id}`.

### `SavedSpotStore.js`

Fájl:

```text
frontend/src/stores/SavedSpotStore.js
```

Feladata:

- saját mentett spotok lekérése,
- spot mentése,
- mentés törlése,
- annak ellenőrzése, hogy egy spot mentve van-e.

Fő API végpontok:

- `GET /saved-spots`,
- `POST /saved-spots`,
- `DELETE /saved-spots/{id}`.

### `ToastStore.js`

Fájl:

```text
frontend/src/stores/ToastStore.js
```

Feladata:

- globális siker és hiba üzenetek megjelenítése,
- üzenet típusának kezelése,
- automatikus eltüntetés 3 másodperc után.

## 5. Oldalak részletesen

### Kezdőlap

Fájl:

```text
frontend/src/pages/index.vue
```

Route:

```text
http://frontend.vm1.test/
```

Feladata:

- FlowFinder bemutatása,
- fő CTA gombok megjelenítése,
- spotkereső és regisztráció/bejelentkezés felé terelés,

A kezdőlap több újrahasznosítható vizuális komponenst használ, például:

- `Grainient`,
- `ScrollFloat`,
- `ScrollReveal`,
- `SplitText`,
- `VariableProximity`.

Ezek főleg a megjelenésért és animációért felelnek.

### Bejelentkezés oldal

Fájl:

```text
frontend/src/pages/bejelentkezes.vue
```

Route:

```text
http://frontend.vm1.test/bejelentkezes
```

Feladata:

- email és jelszó bekérése,
- FormKit alapú űrlapkezelés,
- `AuthStore.login()` meghívása,
- sikeres belépés után átirányítás a profil oldalra,
- hibás belépésnél hibaüzenet megjelenítése.

Az oldal `guestOnly`, ezért bejelentkezett felhasználó nem marad ezen az oldalon.

### Regisztráció oldal

Fájl:

```text
frontend/src/pages/regisztracio.vue
```

Route:

```text
http://frontend.vm1.test/regisztracio
```

Feladata:

- felhasználónév, email, jelszó és jelszó megerősítés bekérése,
- kliens oldali validáció,
- `AuthStore.register()` meghívása,
- sikeres regisztráció után automatikus bejelentkezés,
- átirányítás a profil oldalra.

Az oldal `guestOnly`, ezért bejelentkezett felhasználó nem használja.

### Spotkereső oldal

Fájl:

```text
frontend/src/pages/spotkereso.vue
```

Route:

```text
http://frontend.vm1.test/spotkereso
```

Feladata:

- spotok listázása,
- keresés név, leírás, készítő és tag alapján,
- tag alapú szűrés,
- aktív szűrők kezelése,
- spot kártyák megjelenítése `BaseSpotBlock` komponenssel.

A szűrés frontend oldalon történik a betöltött spot lista alapján. A tag keresésnél a spothoz kapcsolt tagek nevei is figyelembe vannak véve.

A tag szűrő vízszintesen görgethető, és külön nyilakkal is navigálható.

### Spot feltöltés oldal

Fájl:

```text
frontend/src/pages/feltoltes.vue
```

Route:

```text
http://frontend.vm1.test/feltoltes
```

Feladata:

- új spot létrehozása,
- név, leírás, koordináták megadása,
- tagek kiválasztása,
- képek feltöltése,
- sikeres feltöltés után átirányítás a létrehozott spot részletező oldalára.

Az oldal bejelentkezést igényel.

Az oldal a `SpotForm` komponenst használja. A mentés folyamata:

1. A felhasználó kitölti a spot adatokat.
2. A frontend meghívja a `SpotStore.createSpot()` műveletet.
3. A képek feltöltése külön API hívásokkal történik az `ImageStore.uploadImage()` segítségével.
4. A felhasználó a létrehozott spot oldalára kerül.

### Profil oldal

Fájl:

```text
frontend/src/pages/profil.vue
```

Route:

```text
http://frontend.vm1.test/profil
```

Feladata:

- mentett spotok listázása,
- mentett spot eltávolítása,
- saját spotok listázása,
- saját spot szerkesztése,
- saját spot törlése,
- kijelentkezés.

Az oldal bejelentkezést igényel.

A profil oldal több store-t használ:

- `AuthStore`,
- `SpotStore`,
- `SavedSpotStore`,
- `ToastStore`.

A saját spotok a `created_by` mező alapján vannak szűrve. A mentett spotok a backend `/saved-spots` végpontjáról érkeznek.

### Spot részletező oldal

Fájl:

```text
frontend/src/pages/spotok/[id].vue
```

Route:

```text
http://frontend.vm1.test/spotok/{id}
```

Feladata:

- egy kiválasztott spot részletes megjelenítése,
- képek megjelenítése,
- tagek megjelenítése,
- helyszín megjelenítése a megadott koordináták alapján,
- készítő megjelenítése,
- spot mentése vagy mentés törlése,
- tulajdonos vagy admin esetén szerkesztés és törlés indítása.

A spot oldal publikus, de mentéshez bejelentkezés kell. Ha a felhasználó nincs bejelentkezve és menteni próbál, a rendszer a bejelentkezés oldalra irányítja.

### Spot szerkesztés oldal

Fájl:

```text
frontend/src/pages/spotok/[id]/szerkesztes.vue
```

Route:

```text
http://frontend.vm1.test/spotok/{id}/szerkesztes
```

Feladata:

- meglévő spot adatainak módosítása,
- cím módosítása,
- új képek feltöltése,
- meglévő képek törlése,
- képek sorrendjének módosítása,
- leírás módosítása,
- koordináták módosítása,
- tagek frissítése.

Az oldal bejelentkezést igényel. A backend ellenőrzi, hogy a módosítást a spot tulajdonosa vagy admin végzi-e.

### 404 oldal

Fájl:

```text
frontend/src/pages/[...all].vue
```

Feladata:

- nem létező útvonalak kezelése,
- felhasználó visszairányítása létező oldalra.

## 6. Fő komponensek

### `BaseLayout.vue`

Fájl:

```text
frontend/src/layouts/BaseLayout.vue
```

Feladata:

- oldalstruktúra biztosítása,
- fejléc és lábléc egységes megjelenítése,
- tartalom slotolása.

### `BaseHeader.vue`

Fájl:

```text
frontend/src/components/layout/BaseHeader.vue
```

Feladata:

- navigáció megjelenítése,
- mobil menü kezelése.

A menüpontok elérhetősége a felhasználó állapotától függően változnak.

### `BaseFooter.vue`

Fájl:

```text
frontend/src/components/layout/BaseFooter.vue
```

Feladata:

- lábléc megjelenítése,
- egyszerű információs és navigációs tartalom biztosítása.

### `BaseSpotBlock.vue`

Fájl:

```text
frontend/src/components/BaseSpotBlock.vue
```

Feladata:

- spot kártya megjelenítése,
- spot képének megjelenítése,
- spot nevének, leírásának és készítőjének megjelenítése,
- tagek megjelenítése,
- kattintásra navigáció a spot részletező oldalára.

A komponens többféle backend válaszformátumot is képes kezelni a készítő nevéhez és a képek eléréséhez. Ha nincs kép, fallback placeholder jelenik meg.

### `SpotForm.vue`

Fájl:

```text
frontend/src/components/SpotForm.vue
```

Feladata:

- spot feltöltés és spot szerkesztés közös űrlapja,
- alapadatok kezelése,
- tagek választása,
- képek kiválasztása,
- szerkesztésnél meglévő képek kezelése,
- képsorrend módosításának támogatása.

Ez azért fontos komponens, mert a feltöltés és szerkesztés nem két teljesen külön logikára épül, hanem ugyanazt az űrlapot használja eltérő módban.

### `GlobalToast.vue`

Fájl:

```text
frontend/src/components/GlobalToast.vue
```

Feladata:

- globális siker és hiba üzenetek megjelenítése,
- `ToastStore` állapotának vizuális megjelenítése.

## 7. Képek kezelése frontend oldalon

A képek kezelése több helyen megjelenik:

- spot kártyákon,
- spot részletező oldalon,
- spot feltöltéskor,
- spot szerkesztéskor,
- profil oldalon.

A frontend többféle képmezőt is kezel:

- `image.path`,
- `image.url`,
- abszolút külső URL,
- backend storage alapú URL.

Ha nincs kép, akkor fallback kép jelenik meg.

Feltöltésnél a képek nem a spot létrehozási kérésben mennek fel, hanem a spot létrehozása után külön `/images` API hívásokkal.

Szerkesztésnél a meglévő képek:

- törölhetők,
- sorrendezhetők,
- új képekkel kiegészíthetők.

## 8. Tagek és színezés

A tagek színezéséhez külön utility fájl tartozik:

```text
frontend/src/utils/tagColors.js
```

Ez a fájl a tag neve alapján ad vissza stílust. Emiatt a tag megjelenés egységes tud maradni:

- spotkereső oldalon,
- spot kártyákon,
- spot részleteknél,
- feltöltés és szerkesztés során.

A tagek backend oldalon a `sports_and_tags` táblából érkeznek.

## 9. Hibakezelés és visszajelzések

A projektben a visszajelzések két fő módon jelennek meg:

1. lokális hibaüzenetek az adott oldalon vagy űrlapon,
2. globális toast üzenetek.

Példák:

- sikeres bejelentkezés,
- hibás bejelentkezés,
- sikeres spot feltöltés,
- sikertelen mentés,
- spot törlése,
- jogosultsági probléma,
- bejelentkezést igénylő művelet.

A globális visszajelzés a `ToastStore` és a `GlobalToast` komponens együttműködésével jelenik meg.

## 10. Stílus és reszponzivitás

A frontend Tailwind CSS-t használ. A felület mobilon és asztali nézetben is használható.

A fő design irány:

- világos, modern felület,
- kártyás elrendezés,
- lekerekített elemek,
- animált főoldal,
- vizuálisan kiemelt CTA elemek,
- közösségi jellegű megjelenés.

A komponensek többsége saját CSS classokkal és Tailwind utilitykkel épül fel.

---

### AI és képi tartalmak

- A projektben használt egyes képek mesterséges intelligenciával készültek.
- A képgenerálásban és a dokumentáció egyes részeinek megfogalmazásában a **ChatGPT** is segítséget nyújtott.
- Az AI segítségével készült tartalmakat a projekt készítői ellenőrizték, javították és a projekt igényeihez igazították.