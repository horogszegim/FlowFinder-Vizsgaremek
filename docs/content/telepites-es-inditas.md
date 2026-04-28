# FlowFinder telepítési és indítási útmutató

Ez a README a **FlowFinder** projekt lokális elindítását mutatja be VirtualBox alapú virtuális gépen. A leírás végigvezet a szükséges környezet előkészítésén, a virtuális gép importálásán, a hosts fájl módosításán, az SSH kapcsolaton és a projekt indításán.

## Tartalom

- [Szükséges eszközök](#szükséges-eszközök)
- [VirtualBox telepítése](#virtualbox-telepítése)
- [Extension Pack telepítése](#extension-pack-telepítése)
- [Hosts fájl beállítása](#hosts-fájl-beállítása)
- [Virtuális gép importálása OVA fájlból](#virtuális-gép-importálása-ova-fájlból)
- [Projekt indítása](#projekt-indítása)
- [Projekt elérése böngészőből](#projekt-elérése-böngészőből)
- [Gyakori hibák](#gyakori-hibák)

## Szükséges eszközök

A projekt futtatásához az alábbiakra van szükség:

- VirtualBox
- FlowFinder OVA fájl
- SSH kapcsolatot támogató terminál
- Visual Studio Code, ajánlott a kényelmesebb távoli fejlesztéshez
- Git

A VirtualBox letöltési oldala:

[VirtualBox Downloads](https://www.virtualbox.org/wiki/Downloads)

A FlowFinder virtuális gép OVA fájlja:

[OVA fájl letöltése Google Drive-ról](https://drive.google.com/file/d/1-Ez-kn5GucoEJugvwBnfb2iWX_FNDxkD/view)

## VirtualBox telepítése

### Windows

1. Töltsd le a Windows hosts rendszerhez tartozó VirtualBox telepítőt.
2. Futtasd a letöltött `.exe` fájlt.
3. A telepítőben haladj végig az alapértelmezett beállításokkal.
4. Ha a Windows illesztőprogramok telepítésére figyelmeztet, engedélyezd őket.
5. A telepítés végén kattints a `Finish` gombra.

### macOS

1. Töltsd le a macOS rendszerhez tartozó `.dmg` fájlt.
2. Nyisd meg a letöltött fájlt.
3. Indítsd el a `VirtualBox.pkg` telepítőt.
4. Kövesd a telepítő lépéseit.
5. Ha a macOS biztonsági engedélyt kér, engedélyezd a VirtualBox működését.

### Linux

Linuxon a telepítés disztribúciótól függ. Debian vagy Ubuntu alapú rendszeren például `.deb` csomaggal telepíthető.

**Command:**

```bash
sudo dpkg -i virtualbox-<verzio>.deb
```

Ha a telepítés közben hiba jelentkezik, szükséges lehet a kernel headers csomagok telepítése is. RedHat vagy Fedora alapú rendszeren a megfelelő `.rpm` csomagot kell használni.

## Extension Pack telepítése

Az Extension Pack nem kötelező, de hasznos lehet például USB 2.0/3.0 támogatáshoz.

1. Töltsd le az Extension Pack fájlt a VirtualBox letöltési oldaláról.
2. Nyisd meg a VirtualBox alkalmazást.
3. Lépj a `File / Beállítások / Extensions` menüpontra.
4. Kattints a plusz ikonra.
5. Válaszd ki a letöltött Extension Pack fájlt.
6. Fogadd el a licencet, majd telepítsd.

## Hosts fájl beállítása

Ahhoz, hogy a virtuális gépen futó szolgáltatások domain nevekkel is elérhetők legyenek, módosítani kell a gazda operációs rendszer hosts fájlját.

### Hosts fájl helye

| Operációs rendszer | Hosts fájl helye |
| --- | --- |
| Windows | `C:\Windows\System32\drivers\etc\hosts` |
| Linux | `/etc/hosts` |
| macOS | `/etc/hosts` |

A fájl szerkesztéséhez rendszergazdai jogosultság szükséges.

### Windows

1. Nyisd meg a Jegyzettömböt rendszergazdaként.
2. Válaszd a `Fájl / Megnyitás` menüpontot.
3. Navigálj el ide:

```text
C:\Windows\System32\drivers\etc
```

4. Állítsd a fájltípust `Minden fájl` értékre.
5. Nyisd meg a `hosts` nevű fájlt.
6. Illeszd be a szükséges bejegyzéseket a fájl végére.
7. Mentsd el a fájlt.

### Linux vagy macOS

Nyiss terminált, majd futtasd az alábbi parancsot.

**Command:**

```bash
sudo nano /etc/hosts
```

Mentés `nano` szerkesztőben:

```text
Ctrl + O
Enter
Ctrl + X
```

### Hozzáadandó hosts bejegyzések

A hosts fájl végére az alábbi sorokat kell hozzáadni.

```text
127.0.0.1 vm1.test
127.0.0.1 api.vm1.test
127.0.0.1 frontend.vm1.test
127.0.0.1 responsive.vm1.test
127.0.0.1 backend.vm1.test
127.0.0.1 pma.vm1.test
127.0.0.1 docs.vm1.test
127.0.0.1 swagger.vm1.test
127.0.0.1 jsonserver.vm1.test
127.0.0.1 mailcatcher.vm1.test
```

## Virtuális gép importálása OVA fájlból

Ebben a lépésben az előre elkészített virtuális gépet kell importálni VirtualBoxba. Így nem szükséges külön operációs rendszert telepíteni, mert a környezet már elő van készítve.

### OVA fájl letöltése

1. Nyisd meg az OVA fájl Google Drive hivatkozását:

[OVA fájl letöltése](https://drive.google.com/file/d/1-Ez-kn5GucoEJugvwBnfb2iWX_FNDxkD/view)

2. Töltsd le az `.ova` fájlt.
3. Jegyezd meg, hova mentetted, mert az importálásnál ki kell választani.

### Importálás VirtualBoxban

1. Indítsd el a VirtualBox alkalmazást.
2. A felső menüben válaszd a `Fájl / Importálás eszköz` opciót.
3. Kattints a `Tallózás` gombra.
4. Válaszd ki a letöltött `.ova` fájlt.
5. Ellenőrizd a virtuális gép alapbeállításait:
   - virtuális gép neve
   - memória mennyisége
   - processzormagok száma
   - hálózati beállítások
6. Ha minden megfelelő, kattints az `Importálás` gombra.
7. Az importálás befejezése után jelöld ki a virtuális gépet.
8. Kattints a `Start` gombra.

Az első indítás során a rendszer betölti az előre telepített operációs rendszert. Ha a rendszer elindult, a virtuális gép használatra kész.

## Projekt indítása

### Virtuális gép indítása

Indítsd el a virtuális gépet VirtualBoxban, majd várd meg, amíg a rendszer teljesen betölt.

### SSH kapcsolat létrehozása

A gazda gépen nyiss egy terminált. Windows esetén használható a PowerShell vagy a Parancssor is.

**Command:**

```bash
ssh neu@vm1.test
```

A rendszer jelszót kér. A virtuális gép jelszava:

```text
docker1234
```

Sikeres bejelentkezés után a terminál már a virtuális géphez kapcsolódik.

### Kapcsolódás VS Code-dal

1. Indítsd el a Visual Studio Code-ot.
2. Kattints a bal alsó sarokban található Remote Window ikonra.
3. Válaszd a `Connect to Host` opciót.
4. Kattints az `Add New SSH Host` lehetőségre.
5. Add meg az SSH kapcsolatot:

```bash
ssh neu@vm1.test
```

6. Mentsd el a kapcsolatot.
7. A listában válaszd ki az új SSH Hostot, majd csatlakozz.

### Projekt mappa létrehozása

Miután csatlakoztál a virtuális géphez, nyiss egy új terminált VS Code-ban, majd hozz létre egy projektmappát.

**Command:**

```bash
mkdir -p projekt
cd projekt
```

### Projekt letöltése Git segítségével

A forráskód letöltése:

**Command:**

```bash
git clone https://gitlab.neumann-bp.edu.hu/72713754795/flowfinder-vizsgaremek.git flowfinder-vizsgaremek
cd flowfinder-vizsgaremek
```

Ez a forma külön projektmappába klónozza a repositoryt, ezért a `cd flowfinder-vizsgaremek` parancs valóban működni fog.

### Projekt indítása

A projekt indításához futtasd a következő parancsot a projekt gyökérmappájában.

**Command:**

```bash
sh start.sh
```

A parancs elindítja a szükséges szolgáltatásokat.

## Projekt elérése böngészőből

Sikeres indítás után a frontend az alábbi címen érhető el:

[http://frontend.vm1.test](http://frontend.vm1.test)

A projekthez tartozó további helyi domainek:

| Szolgáltatás | URL |
| --- | --- |
| Frontend | [http://frontend.vm1.test](http://frontend.vm1.test) |
| Backend | [http://backend.vm1.test](http://backend.vm1.test) |
| API | [http://api.vm1.test](http://api.vm1.test) |
| phpMyAdmin | [http://pma.vm1.test](http://pma.vm1.test) |
| Dokumentáció | [http://docs.vm1.test](http://docs.vm1.test) |
| Swagger | [http://swagger.vm1.test](http://swagger.vm1.test) |
| JSON Server | [http://jsonserver.vm1.test](http://jsonserver.vm1.test) |
| Mailcatcher | [http://mailcatcher.vm1.test](http://mailcatcher.vm1.test) |

## Gyakori hibák

### SSH host key hiba

Ha az SSH kapcsolat létrehozásakor az alábbi hiba jelenik meg:

```text
WARNING: REMOTE HOST IDENTIFICATION HAS CHANGED!
```

akkor a korábban eltárolt SSH host kulcs ütközik az aktuális virtuális géppel.

Windows esetén nyisd meg ezt a mappát:

```text
%USERPROFILE%\.ssh
```

Töröld a `known_hosts` fájlt, majd próbálj meg újra csatlakozni.

**Command:**

```bash
ssh neu@vm1.test
```

Ezután add meg újra a jelszót:

```text
docker1234
```

### A helyi domainek nem töltődnek be

Ha a `frontend.vm1.test` vagy más helyi domain nem nyílik meg:

1. Ellenőrizd, hogy a virtuális gép fut-e.
2. Ellenőrizd, hogy a hosts fájlba bekerültek-e a szükséges sorok.
3. Ellenőrizd, hogy a `start.sh` lefutott-e hiba nélkül.
4. Próbáld újraindítani a böngészőt.

### VirtualBox hálózati probléma

Ha a virtuális gép nem érhető el hálózaton keresztül:

1. Ellenőrizd a VirtualBox hálózati beállításait.
2. Windows alatt ellenőrizd, hogy a VirtualBox hálózati illesztőprogramjai telepítve vannak-e.
3. Indítsd újra a virtuális gépet.

## Rövid indítási összefoglaló

Ha a VirtualBox, a virtuális gép és a hosts fájl már be van állítva, a projekt indítása röviden így néz ki:

**Command:**

```bash
ssh neu@vm1.test
cd projekt/flowfinder-vizsgaremek
sh start.sh
```

Ezután böngészőben megnyitható:

[http://frontend.vm1.test](http://frontend.vm1.test)
