# Vue alap | 26

> Az alap tartalmaz minden olyan csomagot amely az órai feladatok elkészítéséhez szükséges.

## Tartalom

- [Vue alap | 26](#vue-alap-26)
  - [Tartalom](#tartalom)
  - [Node és a Vite kezelése](#node-és-a-vite-kezelése)
    - [Telepítés](#telepítés)
    - [Fejlesztői szerver indítás](#fejlesztői-szerver-indítás)
    - [Közzétenni kívánt verzió előállítása](#közzétenni-kívánt-verzió-előállítása)
  - [Mappaszerkezet](#mappaszerkezet)
  - [Dokumnetációk](#dokumnetációk)

## Node és a Vite kezelése

### Telepítés

Első indítás alkalmával:

```bash
npm i
```

Egyéb csomagok telepítése:

```bash
pnpm i <csomag_neve>
```

Fejlesztői csomagok telepítése:

```bash
pnpm i -D <csomag_neve>
```

### Fejlesztői szerver indítás

A fejlesztői szervet a következő paranccsal tudod elnidítani. Ezt követően a jelzett linken éred el a szervert.

```bash
pnpm dev
```

### Közzétenni kívánt verzió előállítása

A következő parancs futtatásával egy olyan mappát állít elő a Vite, amit fel tudsz tölteni egy statikus tárhelyre (Pl. [Vercel](https://vercel.com/), [Netlify](https://www.netlify.com/)), ezzel elérhetővé téve az elkészült oldaladat. Az elkészült fájlokat a `dist` könyvtárban találod.

```bash
pnpm build
```

Ahhoz, hogy ellenőrizni tudd, hogy helyesen működik az alkalmazásod, a következő paranccsal egy olyan szervert tudsz elindítani, ami az elkészült fájlokat mutatja meg neked. Ekkor már fejlesztői eszközök nem fognak működni az oldalon.

```bash
pnpm preview
```

## Mappaszerkezet

- `components`: Újrahasnosítható komponensek
  - `layout`: Az olal elrendezéséhez tartozó komponensek (Navbar, Footer)
- `pages`: Az oldalakat tartalmazó komponensek
- `router`: Routerhez tartozó scriptek
  - `guards`: Router Guardokat tartalmazó scriptek
- `stores`: Pinia tárolók
- `test`: Unit tesztek
- `utils`: Kiegészítű scriptek, pl.: Axios

## Dokumentációk

- Vite.js: [https://vite.dev](https://vite.dev)
- Vue.js: [https://vuejs.org](https://vuejs.org)
- Vue Router: [https://router.vuejs.org](https://router.vuejs.org)
- Unplugin Vue Router: [https://uvr.esm.is](https://uvr.esm.is)
- Pinia: [https://pinia.vuejs.org](https://pinia.vuejs.org)
- FormKit: [https://formkit.com/](https://formkit.com/)
- TailwindCSS: [https://tailwindcss.com](https://tailwindcss.com)
- Shadcn/vue: [https://www.shadcn-vue.com/](https://www.shadcn-vue.com/)# njit-frontend
