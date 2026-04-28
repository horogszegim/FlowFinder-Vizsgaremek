# Bruno API tesztek dokumentációja

## Alapbeállítás

A manuális API teszteléshez a Bruno programot használtuk.

A rendszer API címe:

```text
http://backend.vm1.test/api
```

Minden lekérésnél ezt kell használni alap URL-ként.

Az elkészített Bruno munkafüzet neve:

```text
FlowFinder API tesztek
```

A munkafüzet mappákra van bontva, így az API végpontok külön táblák és funkciók szerint vannak rendezve.

## Tesztelés előtti feltételek

A tesztek futtatása előtt a backendnek működnie kell.

Az adatbázist migrálni és seedelni kell, mert több kérés seedelt adatokkal dolgozik.

Ajánlott parancs:

```bash
php artisan migrate:fresh --seed
```

Fontos megjegyzés:

- A seederek és factory-k miatt bizonyos adatok futtatásról futtatásra eltérhetnek.
- Emiatt egyes ID-k, például `1`, más adatbázis állapot mellett módosításra szorulhatnak.
- A Bruno munkafüzetben használt ID-k tesztelési példák.
- Ha egy kérés `404` választ ad, akkor valószínűleg az adott ID már nem létezik.

## Tokenek használata

A védett végpontok Laravel Sanctum Bearer tokennel működnek.

A munkafüzetben két token változó szerepel:

```text
{{user_token}}
{{admin_token}}
```

Használat:

1. Futtasd az `auth.login.user` kérést.
2. A válaszban kapott `data.token` értéket másold be a `user_token` változóba.
3. Futtasd az `auth.login.admin` kérést.
4. A válaszban kapott `data.token` értéket másold be az `admin_token` változóba.
5. Ezután futtathatók a védett végpontok.

A JSON kéréseknél használt alap fejlécek:

```text
Accept: application/json
Content-Type: application/json
```

A fájlfeltöltésnél a Bruno automatikusan kezeli a multipart formátumot.

---

## 1. Hitelesítés

Bruno mappa neve: `auth`

### 1.1. `auth.registration`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/registration`
- Relatív URL: `/registration`
- Hitelesítés: `Nem szükséges.`
- Body típusa: `JSON`

Body:

```json
{
  "username": "bruno_test_user_01",
  "email": "bruno.test.user.01@flowfinder.hu",
  "password": "User123!",
  "password_confirmation": "User123!"
}
```

Elvárt eredmény: Sikeres regisztráció. A válaszban egy `data.message` üzenet érkezik vissza.

---

### 1.2. `auth.login.user`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/login`
- Relatív URL: `/login`
- Hitelesítés: `Nem szükséges.`
- Body típusa: `JSON`

Body:

```json
{
  "email": "testuser.1@flowfinder.hu",
  "password": "TestUser1!"
}
```

Elvárt eredmény: Sikeres belépés normál felhasználóval. A válaszban `data.token` és `data.user` érkezik vissza. A tokent a `user_token` változóba kell bemásolni.

---

### 1.3. `auth.login.admin`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/login`
- Relatív URL: `/login`
- Hitelesítés: `Nem szükséges.`
- Body típusa: `JSON`

Body:

```json
{
  "email": "testadmin.1@flowfinder.hu",
  "password": "TestAdmin1!"
}
```

Elvárt eredmény: Sikeres belépés admin felhasználóval. A válaszban `data.token` és `data.user` érkezik vissza. A tokent az `admin_token` változóba kell bemásolni.

---

### 1.4. `auth.user`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/user`
- Relatív URL: `/user`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az aktuálisan bejelentkezett felhasználó adatait.

---

### 1.5. `auth.logout`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/logout`
- Relatív URL: `/logout`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Kijelentkezteti az aktuális felhasználót, és törli az aktuális Sanctum tokent.

---

## 2. Felhasználók

Bruno mappa neve: `users`

### 2.1. `user.index`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/users`
- Relatív URL: `/users`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja a felhasználók listáját.

---

### 2.2. `user.show`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/users/1`
- Relatív URL: `/users/1`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az adott azonosítójú felhasználó adatait.

---

## 3. Sportok és tagek

Bruno mappa neve: `sports_and_tags`

### 3.1. `sports-and-tag.index`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/sports-and-tags`
- Relatív URL: `/sports-and-tags`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az összes sport és tag listáját.

---

### 3.2. `sports-and-tag.show`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/sports-and-tags/1`
- Relatív URL: `/sports-and-tags/1`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az adott azonosítójú sport vagy tag adatait.

---

## 4. Spotok

Bruno mappa neve: `spots`

### 4.1. `spot.index`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/spots`
- Relatív URL: `/spots`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az összes publikus spot listáját.

---

### 4.2. `spot.show`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/spots/1`
- Relatív URL: `/spots/1`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az adott azonosítójú spot adatait.

---

### 4.3. `spot.store`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/spots`
- Relatív URL: `/spots`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: `JSON`

Body:

```json
{
  "title": "Bruno Teszt Spot",
  "description": "Bruno által létrehozott teszt spot.",
  "latitude": "47.4979",
  "longitude": "19.0402",
  "sports_and_tags": [1, 2, 3]
}
```

Elvárt eredmény: Létrehoz egy új spotot a megadott adatokkal.

---

### 4.4. `spot.update`

Új kérés adatai:

- Módszer: `PUT`
- Teljes URL: `http://backend.vm1.test/api/spots/1`
- Relatív URL: `/spots/1`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: `JSON`

Body:

```json
{
  "title": "Bruno Teszt Spot Módosítva",
  "description": "Bruno által módosított teszt spot.",
  "latitude": "47.4979",
  "longitude": "19.0402",
  "sports_and_tags": [1, 2]
}
```

Elvárt eredmény: Módosítja az adott azonosítójú spot adatait.

---

### 4.5. `spot.image-order.update`

Új kérés adatai:

- Módszer: `PATCH`
- Teljes URL: `http://backend.vm1.test/api/spots/1/images/order`
- Relatív URL: `/spots/1/images/order`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: `JSON`

Body:

```json
{
  "image_order": [1]
}
```

Elvárt eredmény: Módosítja az adott spothoz tartozó képek sorrendjét.

---

### 4.6. `spot.destroy`

Új kérés adatai:

- Módszer: `DELETE`
- Teljes URL: `http://backend.vm1.test/api/spots/550`
- Relatív URL: `/spots/550`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Törli az adott azonosítójú spotot. Siker esetén üres, `204 No Content` válasz érkezik.

---

## 5. Képek

Bruno mappa neve: `images`

### 5.1. `image.index`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/images`
- Relatív URL: `/images`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az összes kép listáját.

---

### 5.2. `image.show`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/images/1`
- Relatív URL: `/images/1`
- Hitelesítés: `Nem szükséges.`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az adott azonosítójú kép adatait.

---

### 5.3. `image.store`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/images`
- Relatív URL: `/images`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: `Multipart form`

Body:

- `spot_id`: `1`
- `image`: fájl, példa: `./frontend/src/assets/img/spot-placeholder.png`

Elvárt eredmény: Feltölt egy új képet az adott spothoz.

---

### 5.4. `image.destroy`

Új kérés adatai:

- Módszer: `DELETE`
- Teljes URL: `http://backend.vm1.test/api/images/1`
- Relatív URL: `/images/1`
- Hitelesítés: `Bearer {{admin_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Törli az adott azonosítójú képet. Siker esetén üres, `204 No Content` válasz érkezik.

---

## 6. Mentett spotok

Bruno mappa neve: `saved_spots`

### 6.1. `saved-spot.index`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/saved-spots`
- Relatív URL: `/saved-spots`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja a bejelentkezett felhasználó mentett spotjait.

---

### 6.2. `saved-spot.store`

Új kérés adatai:

- Módszer: `POST`
- Teljes URL: `http://backend.vm1.test/api/saved-spots`
- Relatív URL: `/saved-spots`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: `JSON`

Body:

```json
{
  "spot_id": 1
}
```

Elvárt eredmény: Elmenti a megadott spotot a bejelentkezett felhasználóhoz.

---

### 6.3. `saved-spot.show`

Új kérés adatai:

- Módszer: `GET`
- Teljes URL: `http://backend.vm1.test/api/saved-spots/1`
- Relatív URL: `/saved-spots/1`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Visszaadja az adott mentett spot bejegyzést.

---

### 6.4. `saved-spot.destroy`

Új kérés adatai:

- Módszer: `DELETE`
- Teljes URL: `http://backend.vm1.test/api/saved-spots/1`
- Relatív URL: `/saved-spots/1`
- Hitelesítés: `Bearer {{user_token}}`
- Body típusa: nincs

Body:

Nincs.

Elvárt eredmény: Törli az adott mentett spot bejegyzést. Siker esetén üres, `204 No Content` válasz érkezik.

---

## Javasolt futtatási sorrend

A kérések kézzel is futtathatók, de a következő sorrenddel a legkevesebb hibába futsz bele:

1. `auth.registration`
2. `auth.login.user`
3. `auth.login.admin`
4. `auth.user`
5. `user.index`
6. `user.show`
7. `sports-and-tag.index`
8. `sports-and-tag.show`
9. `spot.index`
10. `spot.show`
11. `spot.store`
12. `spot.update`
13. `image.store`
14. `spot.image-order.update`
15. `image.index`
16. `image.show`
17. `saved-spot.index`
18. `saved-spot.store`
19. `saved-spot.show`
20. `saved-spot.destroy`
21. `image.destroy`
22. `spot.destroy`
23. `auth.logout`

A törlő kéréseket érdemes a végére hagyni, mert ezek után bizonyos ID-k már nem lesznek elérhetők.

## Rövid összefoglaló

A Bruno munkafüzet az `api.php` route fájlban található API végpontokat teszteli.

A publikus végpontok token nélkül futtathatók.

A létrehozás, módosítás, törlés és mentett spot műveletek tokenhez vannak kötve.

Az adminhoz kötött tesztkérések az `{{admin_token}}` változót használják.

A normál felhasználóhoz kötött tesztkérések az `{{user_token}}` változót használják.
