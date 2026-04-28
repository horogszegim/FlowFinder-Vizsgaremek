# FlowFinder - Tesztelési dokumentáció

Ez a dokumentum a FlowFinder projekt tesztelését írja le. Itt szerepel, hogy milyen tesztelési anyagok vannak a projektben, hol találhatók, és hogyan kell őket futtatni vagy használni.

A projektben egy közös tesztjegyzőkönyv van, amelyben minden teszteset szerepel. Nincs külön Bruno tesztjegyzőkönyv és nincs külön Selenium tesztjegyzőkönyv. A Bruno API tesztek, a Laravel Feature tesztek és a Selenium frontend tesztek eredményei ugyanabba a fő tesztjegyzőkönyvbe kerülnek.

## Tartalomjegyzék

- [1. Tesztelés célja](#1-tesztelés-célja)
- [2. Tesztelési anyagok helye](#2-tesztelési-anyagok-helye)
- [3. Fő tesztjegyzőkönyv](#3-fő-tesztjegyzőkönyv)
- [4. Backend Laravel Feature tesztek](#4-backend-laravel-feature-tesztek)
- [5. Bruno API tesztek](#5-bruno-api-tesztek)
- [6. Selenium frontend tesztek](#6-selenium-frontend-tesztek)
- [7. Tesztadatok és seederek](#7-tesztadatok-és-seedek)
- [8. Tesztek futtatása](#8-tesztek-futtatása)
- [9. Tesztelés értékelése](#9-tesztelés-értékelése)

## 1. Tesztelés célja

A tesztelés célja, hogy a FlowFinder webalkalmazás fő funkciói ellenőrizhetően működjenek backend, API és frontend szinten is.

A tesztelés az alábbi fő területeket érinti:

- regisztráció,
- bejelentkezés,
- kijelentkezés,
- bejelentkezett felhasználó adatainak lekérése,
- felhasználók listázása és lekérése,
- tagek lekérése,
- spotok listázása,
- spot részleteinek lekérése,
- spot létrehozása,
- spot módosítása,
- spot törlése,
- spot képeinek sorrendezése,
- képek listázása,
- kép részleteinek lekérése,
- kép feltöltése,
- kép törlése,
- mentett spotok listázása,
- spot mentése,
- mentett spot lekérése,
- mentett spot törlése,
- fő frontend felhasználói folyamatok böngészős ellenőrzése.

A tesztelés három szinten történik:

| Tesztelési szint | Eszköz | Végrehajtás módja | Mire szolgál |
| --- | --- | --- | --- |
| Backend automatizált teszt | Laravel Feature tesztek | Automatikus | API végpontok, validációk, jogosultságok és adatbázisműveletek ellenőrzése |
| API manuális teszt | Bruno | Manuális | API végpontok kézi ellenőrzése requestekkel és válaszokkal |
| Frontend automatizált teszt | Selenium WebDriver | Automatikus | Valódi böngészőben végigfuttatott felhasználói folyamatok ellenőrzése |

## 2. Tesztelési anyagok helye

A teszteléssel kapcsolatos fájlok a `docs/content/tests/` mappában, illetve a Laravel backend tesztek a `backend/tests/Feature/` mappában találhatók.

| Anyag | Útvonal | Leírás |
| --- | --- | --- |
| Fő tesztjegyzőkönyv | [tests/FlowFinder_tesztelesi_jegyzokonyv.xlsx](./tests/FlowFinder_tesztelesi_jegyzokonyv.xlsx) | Ebben van az összes teszteset, nem külön fájlokban |
| Backend Laravel Feature tesztek | `backend/tests/Feature/` | Automatikus backend feature tesztek |
| Bruno API teszt YAML export | [tests/Bruno API/FlowFinder_API_tesztek.yml](./tests/Bruno%20API/FlowFinder_API_tesztek.yml) | Bruno API tesztek exportált YAML fájlja |
| Bruno API teszt dokumentáció | [tests/Bruno API/FlowFinder_API_tesztek-dokumentacio.md](./tests/Bruno%20API/FlowFinder_API_tesztek-dokumentacio.md) | Bruno API tesztek leírása Markdown formában |
| Bruno API HTML dokumentáció | [tests/Bruno API/FlowFinder_API_tesztek-bruno_automata_dokumentacio.html](./tests/Bruno%20API/FlowFinder_API_tesztek-bruno_automata_dokumentacio.html) | Bruno API tesztek HTML dokumentációja |
| Selenium teszt projekt ZIP | [tests/Selenium/FlowFinder_Selenium_tesztek.zip](./tests/Selenium/FlowFinder_Selenium_tesztek.zip) | C# Selenium WebDriver tesztprojekt ZIP formátumban |

Fontos: a Bruno API tesztekhez nem külön Bruno collection mappa szerepel a dokumentációban, hanem a `FlowFinder_API_tesztek.yml` YAML export, valamint a hozzá tartozó Markdown és HTML dokumentáció.

## 3. Fő tesztjegyzőkönyv

A fő tesztjegyzőkönyv helye:

```text
docs/content/tests/FlowFinder_tesztelesi_jegyzokonyv.xlsx
```

Link: [FlowFinder_tesztelesi_jegyzokonyv.xlsx](./tests/FlowFinder_tesztelesi_jegyzokonyv.xlsx)

Ebben az Excel fájlban szerepel minden teszteset. Nem kell külön Bruno vagy Selenium tesztjegyzőkönyvet keresni, mert minden teszteset ebben az egy fájlban van vezetve.

A tesztjegyzőkönyv oszlopai:

| Oszlop | Jelentés |
| --- | --- |
| Azonosító | A teszteset egyedi azonosítója, például `T001` |
| Teszt neve | A teszt rövid neve |
| Teszt típusa | Backend / Bruno API, Backend / Laravel Feature vagy Frontend / Selenium |
| Végrehajtás módja | Manuális vagy Automatikus |
| Rövid leírás | Mit ellenőriz a teszt |
| Elvárt eredmény | Mi számít sikeres működésnek |
| Állapot | A teszt eredménye, például Sikeres |
| Tesztelő | A tesztet végző személy neve |

A tesztjegyzőkönyvben szereplő fő tesztcsoportok:

| Azonosítók | Teszt típusa | Végrehajtás módja | Tesztelő |
| --- | --- | --- | --- |
| `T001` - `T023` | Backend / Bruno API | Manuális | Horogszegi Miklós Dávid |
| `T024` - `T034` | Backend / Laravel Feature | Automatikus | Balla Ádám Levente |
| `T035` - `T042` | Frontend / Selenium | Automatikus | Király Márton |

A tesztjegyzőkönyvben csak a tesztesetek és azok eredményei vannak rögzítve. A futtatási útmutató ebben a Markdown dokumentumban szerepel.

## 4. Backend Laravel Feature tesztek

A backend feature tesztek helye:

```text
backend/tests/Feature/
```

Ezek Laravel PHPUnit alapú automatikus tesztek. HTTP szinten ellenőrzik az API működését, vagyis tényleges API kéréseket futtatnak, és vizsgálják a válaszokat, státuszkódokat, validációkat, jogosultságokat és adatbázisműveleteket.

A projektben szereplő fő backend feature tesztfájlok:

| Tesztfájl | Mit ellenőriz |
| --- | --- |
| `AuthTest.php` | Regisztráció, bejelentkezés, aktuális felhasználó lekérése, kijelentkezés, hibás login, védett route auth nélkül |
| `UserTest.php` | Felhasználók listázása és egy felhasználó lekérése |
| `SportsAndTagTest.php` | Sportok és tagek listázása, valamint egy sport részleteinek lekérése |
| `SpotTest.php` | Spot CRUD műveletek, validáció, jogosultságok, képsorrend frissítés |
| `ImageTest.php` | Képek listázása, lekérése, feltöltése, törlése és validációja |
| `SavedSpotTest.php` | Mentett spotok listázása, mentése, lekérése, törlése és duplikáció kezelése |

A Laravel Feature tesztek a fő tesztjegyzőkönyvben `T024` és `T034` közötti azonosítókkal szerepelnek.

## 5. Bruno API tesztek

A Bruno API tesztek célja, hogy az API végpontok frontend nélkül is ellenőrizhetők legyenek. A Bruno tesztelés manuális API tesztelés, tehát a kéréseket a Bruno kliensben kell futtatni, majd az eredményeket a fő tesztjegyzőkönyvben kell rögzíteni.

A Bruno API tesztekhez kapcsolódó fájlok:

| Fájl | Link | Mire való |
| --- | --- | --- |
| `FlowFinder_API_tesztek.yml` | [Megnyitás](./tests/Bruno%20API/FlowFinder_API_tesztek.yml) | Bruno API tesztek exportált YAML fájlja |
| `FlowFinder_API_tesztek-dokumentacio.md` | [Megnyitás](./tests/Bruno%20API/FlowFinder_API_tesztek-dokumentacio.md) | API tesztek Markdown dokumentációja |
| `FlowFinder_API_tesztek-bruno_automata_dokumentacio.html` | [Megnyitás](./tests/Bruno%20API/FlowFinder_API_tesztek-bruno_automata_dokumentacio.html) | API tesztek HTML dokumentációja |

A Bruno API tesztek a fő tesztjegyzőkönyvben `T001` és `T023` közötti azonosítókkal szerepelnek.

A Bruno tesztek fő területei:

- auth végpontok,
- user végpontok,
- sport és tag végpontok,
- spot végpontok,
- image végpontok,
- saved spot végpontok.

A Bruno teszteknél fontos, hogy a bejelentkezést igénylő végpontokhoz érvényes Bearer token szükséges. Emiatt először login kérést kell futtatni, majd a válaszban kapott tokent kell használni a védett API végpontoknál.

## 6. Selenium frontend tesztek

A Selenium teszt projekt helye:

```text
docs/content/tests/Selenium/FlowFinder_Selenium_tesztek.zip
```

Selenium teszt fájl:

[FlowFinder_Selenium_tesztek.zip](./tests/Selenium/FlowFinder_Selenium_tesztek.zip)

Ez egy C# alapú Selenium WebDriver tesztprojekt. A ZIP fájlt ki kell csomagolni, majd a projektet VS Code-ból vagy terminálból lehet futtatni.

A Selenium teszt nem API szinten dolgozik, hanem valódi böngészőben járja végig a frontend működését. Ezért a futtatás előtt a frontendnek, a backendnek és az adatbázisnak is működnie kell.

A Selenium tesztek a fő tesztjegyzőkönyvben `T035` és `T042` közötti azonosítókkal szerepelnek.

A Selenium tesztek főbb ellenőrzött folyamatai:

- kezdőoldal betöltése,
- regisztráció,
- bejelentkezés,
- spotkereső oldal használata,
- spot feltöltés/törlés,
- képkezelés ellenőrzése,
- profil oldal ellenőrzése,
- kijelentkezés.

A pontos böngészős lépések a Selenium projektben található `Program.cs` fájlban vannak megírva.

## 7. Tesztadatok és seedek

A teszteléshez fontos, hogy az adatbázisban legyenek alapadatok. Ezeket a Laravel seederek hozzák létre.

A seederek többek között az alábbi adatokat készítik elő:

- felhasználók,
- admin felhasználók,
- tagek,
- spotok,
- képek,
- mentett spotok.

A seedelt adatok egy része randomizált, ezért az adatok futtatásonként eltérhetnek. Ez főleg a spotok, koordináták, képek és mentett spotok esetében fontos. Emiatt előfordulhat, hogy a Bruno API teszteknél egyes `id` értékeket a frissen seedelt adatbázis alapján módosítani kell.

A fix teszt felhasználók és teszt adminok forrása backend oldalon:

```text
backend/database/seeders/UserSeeder.php
```

Tesztelés előtt ajánlott az adatbázist frissen újraépíteni és seedelni:

```bash
php artisan migrate:fresh --seed
```

Docker Compose használatával:

```bash
docker compose exec backend php artisan migrate:fresh --seed
```

Ha a backend konténerben fish shellt használtok:

```bash
docker compose exec backend fish
php artisan migrate:fresh --seed
```

## 8. Tesztek futtatása

### 8.1. Előkészületek

A tesztek futtatása előtt a projektet el kell indítani a telepítési és indítási dokumentáció alapján:

[telepites-es-inditas.md](./telepites-es-inditas.md)

Általános előfeltételek:

- a Docker konténerek fussanak,
- a backend legyen elérhető,
- a frontend legyen elérhető,
- az adatbázis legyen migrálva és seedelve,
- a szükséges függőségek legyenek telepítve,
- Selenium tesztnél legyen telepített Google Chrome böngésző,
- Selenium tesztnél legyen telepített .NET SDK.

### 8.2. Backend Laravel Feature tesztek futtatása

A backend feature tesztek automatikusan futtathatók Laravel Artisan paranccsal.

A teljes backend tesztcsomag futtatása a backend konténerben:

```bash
php artisan test
```

Ugyanez Docker Compose segítségével a projekt gyökérmappájából:

```bash
docker compose exec backend php artisan test
```

Ha fish shellt használtok a backend konténerben:

```bash
docker compose exec backend fish
php artisan test
```

Egy konkrét tesztfájl futtatása:

```bash
php artisan test tests/Feature/SpotTest.php
```

Docker Compose segítségével:

```bash
docker compose exec backend php artisan test tests/Feature/SpotTest.php
```

Példák további konkrét tesztfájlokra:

```bash
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/UserTest.php
php artisan test tests/Feature/SportsAndTagTest.php
php artisan test tests/Feature/ImageTest.php
php artisan test tests/Feature/SavedSpotTest.php
```

Sikeres futás esetén a Laravel a terminálban jelzi, hogy a tesztek sikeresen lefutottak. Az eredményeket a fő tesztjegyzőkönyv megfelelő soraihoz kell viszonyítani.

### 8.3. Bruno API tesztek futtatása

A Bruno API tesztek manuális tesztek, ezért ezeket a Bruno alkalmazásban kell futtatni.

A Bruno tesztekhez használt fájl:

```text
docs/content/tests/Bruno API/FlowFinder_API_tesztek.yml
```

Futtatás menete:

1. Indítsd el a projekt backend részét.
2. Ellenőrizd, hogy az API elérhető-e.
3. Nyisd meg a Bruno alkalmazást.
4. Importáld vagy nyisd meg a `FlowFinder_API_tesztek.yml` fájlt.
5. Először futtasd a regisztrációs vagy login kéréseket.
6. A login válaszából kapott tokent másold be a védett kérések Bearer token mezőjébe vagy a Bruno változóiba.
7. Futtasd sorban az API teszteket.
8. Ellenőrizd a válasz státuszkódját és tartalmát.
9. Az eredményt a fő tesztjegyzőkönyvben rögzítsd.

A védett végpontoknál szükséges fejléc:

```text
Authorization: Bearer <token>
```

Ha egy kérés `id` alapján dolgozik, például `/spots/{id}` vagy `/images/{id}`, akkor az `id` értékét az aktuális adatbázis alapján kell megadni. Mivel a seederek részben random adatokat hoznak létre, az azonosítók eltérhetnek.

Ha a regisztrációs teszt azért hibázik, mert az e-mail cím már létezik, akkor két megoldás van:

- új e-mail címet kell megadni a Bruno kérésben,
- vagy újra kell építeni az adatbázist:

```bash
php artisan migrate:fresh --seed
```

Docker Compose használatával:

```bash
docker compose exec backend php artisan migrate:fresh --seed
```

Képfeltöltés tesztelésénél multipart/form-data kérést kell használni. Ha a YAML exportban szereplő képútvonal a gépen nem érvényes, akkor a Bruno-ban ki kell választani egy létező képfájlt.

### 8.4. Selenium frontend tesztek futtatása

A Selenium tesztprojekt ZIP fájlban található:

```text
docs/content/tests/Selenium/FlowFinder_Selenium_tesztek.zip
```

Futtatás előtt szükséges:

- működő backend,
- működő frontend,
- frissen seedelt adatbázis,
- Google Chrome böngésző,
- .NET SDK,
- a ZIP fájl kicsomagolása.

Futtatás lépései:

1. Csomagold ki a ZIP fájlt:

```text
docs/content/tests/Selenium/FlowFinder_Selenium_tesztek.zip
```

2. Nyisd meg a kicsomagolt projektet VS Code-ban.

3. Lépj be a C# projekt mappájába, ahol a `.csproj` fájl található.

Példa:

```bash
cd docs/content/tests/Selenium
```

A ZIP kicsomagolása után a konkrét mappanév szerint kell belépni a projektbe. A cél az a mappa, ahol ez a fájl található:

```text
FlowFinder_Selenium_tesztek.csproj
```

4. Telepítsd vissza a NuGet csomagokat:

```bash
dotnet restore
```

5. Futtasd a Selenium tesztet:

```bash
dotnet run
```

A Selenium teszt futás közben megnyitja a böngészőt, majd automatikusan végigmegy a `Program.cs` fájlban megírt lépéseken.

Ha a teszt nem indul el, először ezeket kell ellenőrizni:

- fut-e a frontend,
- fut-e a backend,
- elérhető-e a frontend URL,
- az adatbázis seedelve van-e,
- telepítve van-e a Chrome,
- telepítve van-e a .NET SDK,
- a NuGet csomagok vissza lettek-e állítva `dotnet restore` paranccsal,
- a `Program.cs` fájlban szereplő URL-ek egyeznek-e a lokális környezettel.

Ha a Selenium teszt egy elemre vár és ott megáll, akkor általában az egyik oldal nem töltött be, az adott elem szövege vagy szelektora megváltozott, vagy a tesztadat nincs jelen az adatbázisban.

## 9. Tesztelés értékelése

A tesztelés eredményeit a fő tesztjegyzőkönyv tartalmazza:

[FlowFinder_tesztelesi_jegyzokonyv.xlsx](./tests/FlowFinder_tesztelesi_jegyzokonyv.xlsx)

A sikeres teszt azt jelenti, hogy az adott funkció az elvárt módon működött, és az eredmény megfelelt a tesztjegyzőkönyvben rögzített elvárt eredménynek.

Sikertelen teszt esetén a hibát a következő szempontok alapján kell visszakeresni:

- backend route elérhető-e,
- validáció megfelelő-e,
- autentikációs token helyes-e,
- adatbázisban létezik-e a szükséges rekord,
- jogosultsági szabály nem tiltja-e a műveletet,
- frontend oldalon megváltozott-e egy szöveg, gomb vagy szelektor,
- seedelt adatok eltérnek-e az előző futtatástól.

A tesztelési dokumentáció célja, hogy a projekt tesztelési része egy helyen, átláthatóan legyen leírva, míg a konkrét tesztesetek és eredmények a közös Excel tesztjegyzőkönyvben szerepelnek.

---

### AI és képi tartalmak

- A projektben használt egyes képek mesterséges intelligenciával készültek.
- A képgenerálásban és a dokumentáció egyes részeinek megfogalmazásában a **ChatGPT** is segítséget nyújtott.
- Az AI segítségével készült tartalmakat a projekt készítői ellenőrizték, javították és a projekt igényeihez igazították.