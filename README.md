# FlowFinder – Felhasználói dokumentáció

Ez a dokumentáció a **FlowFinder** webalkalmazás felhasználói működését mutatja be. A célja, hogy egy új felhasználó önállóan megértse, mire való az oldal, hogyan tud regisztrálni, bejelentkezni, spotokat keresni, menteni, feltölteni, szerkeszteni és kezelni a saját profilját.

## Telepítés és indítás

A FlowFinder használatához először el kell indítani a projektet a [telepítési és indítási útmutató](docs/content/telepites-es-inditas.md) alapján.

## Tartalomjegyzék

1. [A FlowFinder célja](#1-a-flowfinder-célja)
2. [Felhasználói szerepkörök](#2-felhasználói-szerepkörök)
3. [Tesztfelhasználók](#3-tesztfelhasználók)
4. [Az oldal általános felépítése](#4-az-oldal-általános-felépítése)
5. [Főoldal használata](#5-főoldal-használata)
6. [Regisztráció](#6-regisztráció)
7. [Bejelentkezés](#7-bejelentkezés)
8. [Kijelentkezés](#8-kijelentkezés)
9. [Spot kereső használata](#9-spot-kereső-használata)
10. [Spot részleteinek megtekintése](#10-spot-részleteinek-megtekintése)
11. [Spot mentése és mentés törlése](#11-spot-mentése-és-mentés-törlése)
12. [Spot feltöltése](#12-spot-feltöltése)
13. [Képek kezelése spot feltöltésnél](#13-képek-kezelése-spot-feltöltésnél)
14. [Saját profil oldal használata](#14-saját-profil-oldal-használata)
15. [Saját spotok kezelése](#15-saját-spotok-kezelése)
16. [Spot szerkesztése](#16-spot-szerkesztése)
17. [Spot törlése](#17-spot-törlése)
18. [Mentett spotok kezelése](#18-mentett-spotok-kezelése)
19. [Hibák és visszajelzések](#19-hibák-és-visszajelzések)
20. [Gyakori felhasználói helyzetek](#20-gyakori-felhasználói-helyzetek)
21. [Javasolt képernyőképek a README-hez](#21-javasolt-képernyőképek-a-readme-hez)

## 1. A FlowFinder célja

A **FlowFinder** egy közösségi alapon működő spotkereső weboldal, ahol a felhasználók sporthelyeket fedezhetnek fel, elmenthetik őket későbbre, vagy akár új spotokat is feltölthetnek az adatbázisba. Rengeteg jó hely létezik, ami nem hivatalos, ezért nem jelenik meg a klasszikus térképes appokban – például a Google Mapsen. Ennek ellenére az emberek aktívan használják ezeket, mert ezek a spotok általában szájról szájra jól terjednek. 

Szerintünk baromi menő, hogy ezeket a helyeket így is sokan ismerik és használják, annak ellenére, hogy a neten vagy a közösségi médiában alig van róluk infó. Mi viszont szeretnénk ennek egy platformot adni. Itt a közösség könnyen tud egymásnak spotokat ajánlani, újakat felfedezni, és ezen keresztül akár kapcsolatokat is építeni. Szerintünk a kezdőknek különösen hasznos lehet, hisz nekik jó eséllyel még nincs ilyen közegük – itt simán el tudnak indulni. A későbbiekben be szeretnénk építeni az értékelés és a kommentelés lehetőségét is. Ezek alapján még jobban szűrhetőek lesznek a legjobb helyek. 

A FlowFinder fő funkciói:

- spotok keresés kulcsszavak és tagek szerint,
- spotok megtekintése részletes adatokkal,
- spotok elmentése későbbi megtekintéshez,
- új spotok feltöltése az adatbázisba,
- saját spotok szerkesztése vagy törlése.

## 2. Felhasználói szerepkörök

A FlowFinder használata során alapvetően kétféle állapot van:

### Vendég felhasználó

Vendég felhasználó az, aki még nincs regisztrálva (és bejelentkezve sem). Ő az oldal publikus részeit tudja használni.

Vendégként elérhető funkciók:

- főoldal megtekintése,
- spotok böngészése,
- spotok részleteinek megtekintése,
- regisztráció,
- bejelentkezés.

### Bejelentkezett felhasználó

Bejelentkezett felhasználó az, aki rendelkezik regisztrált fiókkal és sikeresen belépett a rendszerbe.

Bejelentkezéssel feloldott funkciók:

- spotok mentése későbbre,
- új spotok feltöltése,
- saját spotok szerkesztése vagy törlése.

A rendszer lényege bejelentkezett felhasználóként használható ki teljesen, mert a feltöltés, mentés és saját tartalmak kezelése csak így érhető el.

## 3. Az oldal általános felépítése

A weboldal több fő részből áll.

### Navigációs sáv

Az oldal tetején található a navigációs sáv. Innen lehet gyorsan elérni a főbb oldalakat.

Elérhető menüpontok:

- **Kezdőlap**
- **Spotkereső**
- **Feltöltés**
- **Profil (vagy Bejelentkezés/Regisztráció)**

### Tartalmi rész

A középső részen jelenik meg az adott oldal fő tartalma. Itt található például a spotkereső, a feltöltő űrlap, a profil oldal vagy a spot részletes nézete.

### Lábléc

Az oldal alján található a lábléc. Ez általános információkat és kiegészítő megjelenési elemeket tartalmaz.

## 4. Regisztráció

A regisztrációval a felhasználó saját fiókot hozhat létre a rendszerben.

### Regisztráció lépései

1. A felhasználó megnyitja a **Regisztráció** oldalt.
2. Megadja a felhasználónevét.
3. Megadja az e-mail címét.
4. Megadja a jelszavát.
5. Megerősíti a jelszavát.
6. Elküldi az űrlapot.

### Regisztrációnál megadandó adatok

| Mező | Leírás |
|---|---|
| Felhasználónév | A felhasználó megjelenített neve a rendszerben |
| E-mail cím | Ezzel lehet később bejelentkezni |
| Jelszó | A fiókhoz tartozó jelszó |
| Jelszó megerősítése | Ugyanazt a jelszót kell újra megadni |

### Fontos szabályok regisztrációnál

A regisztráció csak akkor sikeres, ha a kötelező mezők helyesen vannak kitöltve.

A felhasználónév nem lehet üres. Az e-mail címnek érvényes formátumúnak kell lennie. A jelszónak meg kell felelnie a rendszer által elvárt biztonsági feltételeknek, és a jelszó megerősítésének egyeznie kell az eredeti jelszóval.

Ha valamelyik adat hibás, a rendszer erről élőben ad visszajelzést a felhasználónak.

### Sikeres regisztráció után

Sikeres regisztráció után a felhasználót automatikusan bejelentkezteti a rendszer a vadonatúj fiókjába.

## 5. Bejelentkezés

A bejelentkezés segítségével a már regisztrált felhasználók hozzáférnek a saját fiókjukhoz.

### Bejelentkezés lépései

1. A felhasználó megnyitja a **Bejelentkezés** oldalt.
2. Megadja az e-mail címét.
3. Megadja a jelszavát.
4. Rákattint a bejelentkezés gombra.

### Bejelentkezési adatok

| Mező | Leírás |
|---|---|
| E-mail cím | A regisztrációkor megadott e-mail cím |
| Jelszó | A regisztrációkor megadott jelszó |

Ha az adatok helyesek, a rendszer belépteti a felhasználót. Ha az e-mail cím vagy a jelszó hibás, a rendszer hibaüzenetet ad.

### Mire figyeljen a felhasználó?

A bejelentkezésnél pontosan azt az e-mail címet kell használni, amellyel a regisztráció történt. A jelszó mező érzékeny a kis- és nagybetűkre, ezért elgépelés esetén a bejelentkezés sikertelen lehet.

## 6. Kijelentkezés

A kijelentkezés a bejelentkezett állapot megszüntetésére szolgál.

A felhasználó a profil oldalról tud kijelentkezni. Kijelentkezés után a rendszer vendégként kezeli a felhasználót.

Kijelentkezés után nem érhetők el az alábbi funkciók:

- spotok mentése későbbre,
- új spotok feltöltése,
- saját spotok szerkesztése vagy törlése.

A publikus oldalak, például a főoldal és a spotkereső továbbra is használhatók.

## 7. Spotkereső használata

A **Spotkereső** az alkalmazás egyik legfontosabb oldala. Itt lehet böngészni a rendszerben elérhető spotok közt.

A spotkeresőben a felhasználó listázva látja a feltöltött helyeket. Egy spotkártyán megjelenik:

- a spot első képe,
- a spot címe,
- a feltöltő neve,
- a hozzá tartozó tagek,
- a spot rövid leírása.

A spotkártyára kattintva megnyílik az adott spot részletes oldala.

### Szöveges keresés

A keresőmező segítségével a felhasználó szöveg alapján kereshet a spotok között.

A keresés használható például:

- spot címére,
- leírásban szereplő szavakra,
- feltöltő nevére,
- tagek nevére.

Például ha a felhasználó beírja, hogy `skate`, akkor azok a spotok jelennek meg, amelyek címében, leírásában, feltöltője felhasználónevében vagy tagei között szerepel ehhez kapcsolódó szöveg.

### Tag alapú szűrés

A keresőmező alatt tagek találhatók. Ezekkel gyorsan lehet szűrni a spotokat sporttípus, helyszíntípus vagy egyéb tulajdonság alapján.

A tagre kattintva a tag kijelölődik. A kijelölt tag vizuálisan kiemelve jelenik meg, így látható, hogy éppen milyen szűrés aktív.

Több tag is kijelölhető. Ilyenkor a rendszer az összes kijelölt tag alapján szűri a megjelenő spotokat.

### Tagek vízszintes görgetése

Mivel sok tag van a rendszerben, a tagek vízszintesen görgethető sávban jelennek meg.

A sáv használható:

- egérgörgővel,
- touchpaddel,
- az oldalsó nyíl gombokkal.

### Szűrés törlése

A szűrés törléséhez a kijelölt tagekre újra rá lehet kattintani. Ezzel a tag kijelölése megszűnik, és a találati lista újra bővül.

### Találatok hiánya

Ha a keresésre vagy szűrésre nincs találat, a rendszer jelzi, hogy nem található megfelelő spot.

### Lapozás

Ha sok spot van a rendszerben, a spotkereső lapozással jeleníti meg őket. A felhasználó az előző és következő illetve a megjelenített lapszám gombokkal tud navigálni az oldalak között.

Ez segít abban, hogy a lista átlátható maradjon, és ne egyszerre jelenjen meg túl sok spot.

## 8. Spot részleteinek megtekintése

A spot részletes oldalán a felhasználó minden fontos információt megkap az adott helyről.

A részletes oldal megnyitásához a spotkeresőben vagy a profiloldalon rá kell kattintani egy spotkártyára.

A spot részletes oldalán megjelenik:

- a spot címe,
- a feltöltő neve,
- a spot leírása,
- a hozzá tartozó tagek,
- a mentés (könyvjelző) gomb,
- a spothoz összes feltöltött kép,
- a koordináták alapján térképes megjelenítés.

### Képgaléria

A felhasználó a képekre kattintva nagyobb nézetben is megtekintheti azokat.

A nagyított galériában a felhasználó:

- lapozhat a képek között,
- bezárhatja a galériát, ezzel visszatérve a spot részletes oldalára.

### Leírás

A leírásban a feltöltő bemutathatja a spotot. Itt szerepelhet például, hogy milyen sporthoz alkalmas, milyen a környezet, milyen a talaj, van-e világítás, mennyire kezdőbarát, vagy mire érdemes figyelni.

### Térkép

A spot részletes oldalán térképes megjelenítés is található. Ez a feltöltéskor megadott szélességi és hosszúsági koordináták alapján mutatja meg a helyet.

A térkép segít abban, hogy a felhasználó könnyebben megtalálja a spot pontos helyét.

## 9. Spot mentése és mentés törlése

Bejelentkezett felhasználóként a spotokat el lehet menteni. Ez akkor hasznos, ha a felhasználó később is gyorsan vissza szeretne térni egy érdekes helyhez.

### Spot mentése

- A felhasználó a keresőben a spot mellett rányom a mentés gombra

vagy

1. Egy spotra kattintva megnyitja a részletes oldal.
2. Rákattint a mentés (könyvjelző) gombra.
3. A spot bekerül a felhasználó mentett spotjai közé.

A mentett spotok a profiloldalon tekinthetők meg.

### Mentés törlése

Ha a felhasználó már nem szeretné megtartani a spotot a mentett listában, a mentés törölhető.

A mentés törölhető:

- a kereső oldalán a mentés gomb ismételt használatával,
- a spot részletes oldalán a mentés gomb ismételt használatával,
- a profiloldalon a mentett spotok között.

A mentés törlése nem törli magát a spotot a rendszerből. Csak a felhasználó saját mentett listájából kerül ki.

## 10. Spot feltöltése

Új spotot csak bejelentkezett felhasználó tud feltölteni.

A feltöltés célja, hogy a felhasználó új helyeket adjon hozzá a FlowFinder közösségi adatbázisához.

### Feltöltés lépései

1. A felhasználó regisztrál/bejelentkezik.
2. Megnyitja a **Feltöltés** oldalt.
3. Megadja a spot címét.
4. Feltölt legalább egy és maximum tíz képet (max 3MB/db).
5. Megadja a spot leírását.
6. Megadja a szélességi és hosszúsági koordinátákat
7. Kiválasztja a megfelelő tageket (max ötöt).
8. Ellenőrzi az adatokat.
9. Feltölti a spotot.

### Spot feltöltésénél megadandó adatok

| Mező | Leírás |
|---|---|
| Cím | A spot neve vagy rövid megnevezése |
| Képek | A spotról feltöltött képek |
| Leírás | A spot részletes bemutatása |
| Szélességi koordináta | A hely földrajzi szélessége |
| Hosszúsági koordináta | A hely földrajzi hosszúsága |
| Tagek | A spothoz kapcsolódó kategóriák és jellemzők |

### Cím megadása

A cím legyen rövid és egyértelmű. Olyan nevet érdemes megadni, amelyből más felhasználók gyorsan megértik, milyen helyről van szó.

Példák jó címekre:

- Városligeti skate spot
- Astoria ikonikus curb
- Deák BMX spot
- Fővám tér lépcsőszett

### Leírás megadása

A leírásban érdemes részletesen bemutatni a spotot.

Hasznos információk lehetnek a már feljebb említettek:

- milyen sporthoz alkalmas,
- milyen a talaj,
- mennyire forgalmas,
- van-e világítás,
- kezdőknek is ajánlott-e,
- van-e a közelben parkoló,
- mennyire biztonságos,
- milyen napszakban érdemes menni,
- van-e ivókút, pad vagy mosdó a közelben.

Minél pontosabb a leírás, annál hasznosabb lesz a spot más felhasználók számára.

### Koordináták megadása

A spot pontos helyét szélességi és hosszúsági koordinátával kell megadni.

A koordináták alapján jelenik meg a spot a térképen. Ezért fontos, hogy a megadott értékek pontosak legyenek.

Példa koordinátákra:

- Szélesség: `47.4979`
- Hosszúság: `19.0402`

A koordinátákat érdemes térképszolgáltatásból kimásolni, hogy a hely pontosan jelenjen meg.

### Tagek kiválasztása

A tagek segítenek a spot kategorizálásában és kereshetőségében.

A felhasználó a taglistából kiválaszthatja azokat a címkéket, amelyek legjobban leírják a spotot.

Például egy kültéri gördeszkás helyhez használható tagek:

- Gördeszka,
- Szabadtéri,
- Stairs,
- Kezdőbarát,
- Street spot.

A rendszer maximum 5 tag kiválasztását engedélyezheti. Ez segít abban, hogy egy spot ne legyen túl sok, pontatlan címkével ellátva.

## 11. Képek kezelése spot feltöltésnél

A képek fontos szerepet játszanak, mert ezek alapján a felhasználók gyorsan eldönthetik, hogy érdekes-e számukra az adott spot.

### Képek feltöltése

A képeket a feltöltő felületen lehet hozzáadni. A rendszer támogatja a fájl kiválasztását, és a felület drag and drop jelleggel is használható, ha a böngésző és az eszköz ezt támogatja.

### Képfeltöltési szabályok

A képek feltöltésénél a következő szabályokra kell figyelni:

- legalább 1 kép szükséges,
- maximum 10 kép tölthető fel,
- csak JPG és PNG formátum használható,
- egy kép legfeljebb 3 MB méretű lehet.

Ha a felhasználó nem megfelelő fájlt választ ki, a rendszer hibaüzenetet jelenít meg.

### Képek előnézete

Feltöltés után a képek előnézetként megjelennek az űrlapon. Így a felhasználó még beküldés előtt ellenőrizheti, hogy jó képeket választott-e ki.

### Képek sorrendje

A képek sorrendje módosítható. Ez azért fontos, mert az első kép kiemelt képként jelenik meg a spotkártyán és a spot részletes oldalán.

A felhasználó a képek sorrendjét a felületen található mozgató gombokkal tudja módosítani.

### Kép eltávolítása

Ha a felhasználó rossz képet töltött fel, azt a spot feltöltése előtt eltávolíthatja.

Szerkesztésnél a korábban feltöltött képek is eltávolíthatók, de legalább egy képnek a spothoz kell tartoznia.

## 12. Saját profil oldal használata

A profiloldal a bejelentkezett felhasználó saját központi oldala.

A profiloldalon a felhasználó áttekintheti:

- saját feltöltött spotjait,
- mentett spotjait,
- kijelentkezési lehetőségét.

A profiloldal csak bejelentkezés után érhető el.

### Profiloldal célja

A profiloldal azért fontos, mert innen lehet kezelni a felhasználó saját tartalmait. A spotkereső minden spotot mutat, a profil viszont csak azokat emeli ki, amelyek a bejelentkezett felhasználó által feltöltött vagy lementett spotok.

## 13. Saját spotok kezelése

A profiloldalon külön részben jelennek meg a felhasználó saját spotjai.

Saját spotnak az számít, amelyet az adott bejelentkezett felhasználó töltött fel.

A saját spotoknál a felhasználó a következő műveleteket végezheti el:

- spot megnyitása,
- spot szerkesztése,
- spot törlése.

### Szerkesztés gomb

A szerkesztés gombbal a felhasználó módosíthatja a saját spotját.

Szerkeszthető adatok:

- cím,
- leírás,
- koordináták,
- tagek,
- képek,
- képek sorrendje.

### Szerkesztés megszakítása

Ha a felhasználó mégsem szeretné elmenteni a változtatásokat, visszaléphet a profiloldalra. Ilyenkor az el nem mentett módosítások elveszhetnek.

### Mentés

Sikeres mentés után a módosított adatok jelennek meg a spot részletes oldalán és a spotkeresőben.

### Törlés gomb

A törlés gombbal a felhasználó eltávolíthatja a saját spotját a rendszerből.

A törlés komoly művelet, mert a spot a listából és a részletes nézetből is eltűnik. A törlés után más felhasználók sem fogják látni az adott spotot.

### Mire kell figyelni törlés előtt?

Törlés előtt érdemes ellenőrizni, hogy valóban a megfelelő spot kerül-e törlésre. Ha egy spotot csak javítani kell, akkor törlés helyett a szerkesztés használata ajánlott.

## 14. Mentett spotok kezelése

A mentett spotok olyan helyek, amelyeket a felhasználó későbbre elmentett.

A mentett spotok a profiloldalon külön részben jelennek meg.

### Mire jó a mentett spot funkció?

A mentés akkor hasznos, ha a felhasználó:

- később vissza szeretne térni egy spothoz,
- több helyet szeretne összehasonlítani,
- el szeretné menteni a kedvenc helyeit,

### Mentett spot megnyitása

A mentett spot kártyájára kattintva a felhasználó megnyithatja a spot részletes oldalát.

### Mentés eltávolítása

A mentett spot eltávolítható a mentett listából. Ez nem törli magát a spotot a rendszerből, csak a felhasználó saját mentései közül veszi ki.

## 15. Gyakori kérdések & válaszok

### Új felhasználó vagyok, mit csináljak először?

Először érdemes megnyitni a főoldalt és a spotkeresőt. Így regisztráció nélkül is meg lehet nézni, milyen spotok vannak a rendszerben. Ha a felhasználó saját spotot is szeretne feltölteni vagy spotokat szeretne menteni, akkor regisztrálnia kell.

### Találtam egy jó helyet, hogyan mentem el?

A spotkereső oldalán a mentés gombra kell kattintani. Ezután a spot megjelenik a profiloldalon a mentett spotok között.

vagy

A spot részletes oldalán a mentés gombra kell kattintani. Ezután a spot megjelenik a profiloldalon a mentett spotok között.

### Hogyan töltök fel saját spotot?

Be kell jelentkezni, majd meg kell nyitni a feltöltés oldalt. Ki kell tölteni a címet, a leírást, a koordinátákat, ki kell választani a tageket, fel kell tölteni legalább egy képet, majd a **spot feltöltése** gombra kattint.

### Hogyan javítok ki egy elírást a saját spotomnál?

A profiloldalon a saját spotok között meg kell keresni az adott spotot, majd a szerkesztés gombra kell kattintani. A módosítások mentése után az új adatok fognak megjelenni.

### Hogyan törlök egy saját spotot?

A profiloldalon a saját spotok között meg kell keresni a törölni kívánt spotot, majd a törlés gombbal el lehet távolítani.

### Miért nem tudok spotot feltölteni?

Spotot csak bejelentkezett felhasználó tud feltölteni. Ha a felhasználó nincs bejelentkezve, először be kell lépnie. Az is lehet, hogy a feltöltés oldalon hiányzik egy kötelező adat, nincs kép feltöltve, vagy nem megfelelő fájltípus lett kiválasztva.

### Miért nem jelenik meg kép egy spotnál?

Ha egy kép nem elérhető vagy hiányzik, a rendszer helyettesítő képet jeleníthet meg. Ez azt jelenti, hogy a spot adatai megvannak, de a kép nem tölthető be megfelelően.

### Miért nem találok semmit a keresőben?

Lehet, hogy túl szűk keresést vagy túl sok szűrőt használ a felhasználó. Ilyenkor érdemes törölni a keresőmezőt, kikapcsolni néhány taget, vagy másik keresőszóval próbálkozni.

### Hogyan jutok vissza a spotkeresőbe?

A navigációs sávban a **Spotkereső** menüpontra kell kattintani. Innen újra elérhető az összes spot, a keresés és a szűrés.

# FlowFinder – Fejlesztői dokumentáció

A fejlesztői dokumentáció tartalmazza a projekt technikai felépítését, telepítését, adatbázisát, backend és frontend működését, valamint a tesztelési folyamatokat.

[Fejlesztői dokumentáció megnyitása](./docs/content/fejlesztoi-dokumentacio.md)