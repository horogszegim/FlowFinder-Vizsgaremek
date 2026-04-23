<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
</script>

<template>
  <BaseLayout>
    <div class="w-full flex flex-col gap-20 mb-10 md:mb-15">

      <div class="flex items-center justify-center gap-3 -translate-x-1 mt-5 lg:mt-10 -mb-15 lg:-mb-10">
        <img src="@assets/img/dark-logo.svg" alt="logo" class="h-16 sm:h-22 lg:h-32 w-auto" />
        <span class="text-5xl sm:text-7xl lg:text-8xl font-bold text-text">
          FlowFinder
        </span>
      </div>

      <section class="w-full">

        <div class="rounded-xl border border-text-muted shadow-lg overflow-hidden bg-background">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 p-8 md:p-12 lg:p-14 items-center">
            <div class="flex flex-col gap-5">

              <h1 class="text-4xl md:text-6xl font-bold text-text leading-tight">
                Urban sport spotok egy közösségi platformon
              </h1>

              <p class="text-lg md:text-xl text-text-muted leading-relaxed max-w-3xl">
                A FlowFinder célja, hogy egy helyen összegyűjtse a gördeszkás, BMX-es, MTB-os és egyéb urban sport
                spotokat,
                köztük azokat is, amelyek más térképes platformokon egyáltalán nem jelennek meg.
              </p>

              <p class="text-lg text-text-muted leading-relaxed max-w-3xl">
                Nem <i>csak</i> hivatalos parkokat szeretnénk gyűjteni és megjeleníteni, hanem random lépcsősorokat,
                street
                spotokat, eldugott kis helyeket és minden olyan helyszínt, amit a közösség használ.
              </p>

              <div class="flex flex-wrap gap-5 pt-2">
                <RouterLink to="/spotkereso"
                  class="px-7 py-3 flex items-center justify-center transition font-semibold rounded-xl shadow-lg bg-primary-dark text-white hover:brightness-120 hover:-translate-y-1">
                  Spotkereső megnyitása
                </RouterLink>

                <RouterLink to="/feltoltes"
                  class="px-7 py-3 flex items-center justify-center transition font-semibold rounded-xl shadow-lg bg-background text-primary-dark border-2 border-primary-dark hover:bg-primary-dark hover:text-white hover:border-primary-dark hover:brightness-120 hover:-translate-y-1">
                  Új spot feltöltése
                </RouterLink>
              </div>
            </div>

            <div class="w-full">
              <div class="rounded-xl overflow-hidden border border-text-muted shadow-lg">
                <img class="w-full" src="@assets/img/spot-placeholder.png" alt="Hero Spot Kép">
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
          <div class="flex flex-col gap-5">

            <h2 class="text-3xl md:text-4xl font-bold text-text">
              Miről szól a projekt?
            </h2>

            <p class="text-lg text-text-muted leading-relaxed">
              A FlowFinder egy közösségi alapon működő spotkereső weboldal, ahol a felhasználók sporthelyeket
              fedezhetnek fel, elmenthetik őket későbbre, vagy akár új spotokat is feltölthetnek az adatbázisba.
              Rengeteg jó hely létezik, ami nem hivatalos, ezért nem jelenik meg a klasszikus térképes appokban –
              például a Google Mapsen. Ennek ellenére az emberek aktívan használják ezeket, mert ezek a spotok általában
              szájról szájra jól terjednek.
            </p>

            <p class="text-lg text-text-muted leading-relaxed">
              Szerintünk baromi menő, hogy ezeket a helyeket így is sokan ismerik és használják, annak ellenére, hogy a
              neten vagy a közösségi médiában alig van róluk infó. Mi viszont szeretnénk ennek egy platformot adni. Itt
              a közösség könnyen tud egymásnak spotokat ajánlani, újakat felfedezni, és ezen keresztül akár
              kapcsolatokat is építeni. Szerintünk a kezdőknek különösen hasznos lehet, hisz nekik jó eséllyel még nincs
              ilyen közegük – itt simán el tudnak indulni. A későbbiekben be szeretnénk építeni az értékelés és a
              kommentelés lehetőségét is. Ezek alapján még jobban szűrhetőek lesznek a legjobb helyek.
            </p>

          </div>

          <div class="rounded-xl border border-text-muted bg-background shadow-lg p-8 flex flex-col gap-5">

            <h2 class="text-3xl md:text-4xl font-bold text-text">
              Miért csináljuk?
            </h2>

            <p class="text-lg text-text-muted leading-relaxed">
              A projektet eredetileg egy sulis vizsga miatt kezdtük el fejleszteni. Azonban szerettünk volna egy valós
              problémára megoldást építeni, nem csak egy technikai
              demót készíteni. Valamint maga a téma is közel áll hozzánk, szeretjük ezeket a városi sportokat és
              szeretjük azt, hogy ezek mennyire közösség alapúak.
            </p>

            <p class="text-lg text-text-muted leading-relaxed">
              A célunk az, hogy a platformunk, a FlowFinder ne csak egy egyszerű lista legyen, hanem egy olyan platform,
              ami valóban
              segít az embereknek. Segít az új helyek felfedezésében, és reméljük, hogy akár a tényleges emberek közti
              kapcsolódásban is.
              Ugyanis ezeket a spotokat meglátogatni egyedül is poén, de másokkal mindig egy emlékezetesebb élmény.
            </p>

          </div>
        </div>
      </section>

      <section class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

          <div class="rounded-xl border border-text-muted bg-background shadow-lg p-8 flex flex-col gap-5">
            <h2 class="text-3xl md:text-4xl font-bold text-text">
              Rólunk
            </h2>

            <p class="text-lg text-text-muted leading-relaxed">
              A FlowFindert hárman készítjük: Balla Ádám Levente, Horogszegi Miklós Dávid és Király Márton.
              A célunk egy modern, könnyen átlátható és valóban használható webes alkalmazás fejlesztése.
            </p>

            <p class="text-lg text-text-muted leading-relaxed">
              Számunkra ez a projekt egyszerre portfóliómunka és gyakorlati fejlesztés is. Nem csak azt szerettük volna
              megmutatni, hogy tudunk weboldalt készíteni, hanem azt is, hogy képesek vagyunk egy összetettebb ötletet
              végiggondolni, megtervezni és fokozatosan felépíteni.
            </p>
          </div>

          <div class="w-full flex flex-col">
            <div class="rounded-xl overflow-hidden border border-text-muted shadow-lg">
              <img class="w-full h-auto" src="@assets/img/rolunk.png" alt="Rólunk Kép">
            </div>

            <div class="mt-[-30px] px-5">
              <div class="rounded-xl border border-text-muted bg-background shadow-lg p-5">
                <div class="grid grid-cols-4 text-center text-text-muted text-sm">
                  <span>Balla Ádám Levente</span>
                  <span>Fehér István Jonatán (tanár/mentor)</span>
                  <span>Horogszegi Miklós Dávid</span>
                  <span>Király Márton</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

      <section class="w-full flex flex-col gap-10">
        <div class="max-w-4xl flex flex-col gap-5">
          <h2 class="text-3xl md:text-4xl font-bold text-text">
            Mit tud a FlowFinder?
          </h2>

          <p class="text-lg text-text-muted leading-relaxed">
            A rendszer jelenlegi funkciói közül a legfontosabbak az alábbiak. A már elkészült részek – amikről lejjebb
            olvashatsz – már használhatóak, a későbbi funkciók pedig a projekt további bővítésének alapját adják.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
          <div
            class="rounded-2xl border border-text-muted bg-background shadow-lg px-5 py-8 flex flex-col gap-5 justify-center">
            <h3 class="text-2xl font-bold text-text">🔎| Spotok böngészése</h3>
            <p class="text-text-muted text-lg leading-relaxed">
              A spotkereső maga az, amiért a projektünk nevében benne van, hogy "Finder". Ez a kererő oldalunk, itt a
              felhasználók a spotok között böngészhetnek és új helyeket találhatnak.
            </p>
            <RouterLink to="/spotkereso" class="relative inline-block w-fit font-medium text-text link-hover italic">
              Ugrás a spotkeresőre
            </RouterLink>
          </div>

          <div
            class="rounded-2xl border border-text-muted bg-background shadow-lg px-5 py-8 flex flex-col gap-5 justify-center">
            <h3 class="text-2xl font-bold text-text">📍| Spot adatlap</h3>
            <p class="text-text-muted text-lg leading-relaxed">
              Minden spot rendelkezik egy saját oldallal is ami megjeleníti a "felfedező" által feltöltött képeket,
              leírást, tageket és a koordináta alapján térképes megjelenítést is kapunk.
            </p>
            <p class="text-text-muted">
              Ez az oldal a keresőből lesz elérhető.
            </p>
          </div>

          <div
            class="rounded-2xl border border-text-muted bg-background shadow-lg px-5 py-8 flex flex-col gap-5 justify-center">
            <h3 class="text-2xl font-bold text-text">⬆️| Spot feltöltés</h3>
            <p class="text-text-muted text-lg leading-relaxed">
              A felhasználók ezen az oldalon tudnak új spotokat feltölteni. A spotnak adni kell egy címet, majd
              tölthetünk fel képeket, írhatunk leírást, megadhatjuk a koordinátáit és tageket is kiválaszthatunk.
            </p>
            <RouterLink to="/feltoltes" class="relative inline-block w-fit font-medium text-text link-hover italic">
              Feltöltök egy spotot
            </RouterLink>
          </div>

          <div
            class="rounded-2xl border border-text-muted bg-background shadow-lg px-5 py-8 flex flex-col gap-5 justify-center">
            <h3 class="text-2xl font-bold text-text">🔖| Mentés és profil</h3>
            <p class="text-text-muted text-lg leading-relaxed">
              A regisztrált felhasználók elmenthetik kedvenc spotjaikat, amelyeket a profil oldalon később
              megtekinthetnek. Továbbá szerkeszthetik illetve törölhetik az általuk feltöltött spotokat.
            </p>
            <RouterLink to="/profil" class="relative inline-block w-fit font-medium text-text link-hover italic">
              Saját profil oldal
            </RouterLink>
          </div>
        </div>
      </section>

      <section class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:items-center items-start">

          <div class="flex flex-col gap-5 order-2 lg:order-1">
            <RouterLink to="/regisztracio"
              class="w-full rounded-2xl border border-text-muted bg-background-light shadow-lg p-5 transition hover:-translate-y-1">
              <div class="text-2xl font-bold text-text mb-2">Regisztráció</div>
              <p class="text-text-muted text-lg">
                Új fiók létrehozása a teljes élmény kipróbálásához.
              </p>
            </RouterLink>

            <RouterLink to="/bejelentkezes"
              class="w-full rounded-2xl border border-text-muted bg-background-light shadow-lg p-5 transition hover:-translate-y-1">
              <div class="text-2xl font-bold text-text mb-2">Bejelentkezés</div>
              <p class="text-text-muted text-lg">
                Meglévő fiókkal belépés a személyes funkciókhoz.
              </p>
            </RouterLink>
          </div>

          <div class="flex flex-col gap-5 justify-center h-full order-1 lg:order-2">
            <h2 class="text-3xl md:text-4xl font-bold text-text">
              Fiókhoz kötött funkciók
            </h2>

            <p class="text-lg text-text-muted leading-relaxed">
              A <RouterLink to="/spotkereso"
                class="relative inline-block w-fit font-medium text-text link-hover italic">
                spotok böngészése
              </RouterLink> egy teljesen nyitott funkció, ami azt jelenti, hogy bárki aki fel lép az oldalra,
              használhatja. Azonban sok funkcióhoz – új spot feltöltéséhez és a spotok mentéséhez
              felhasználói fiókra van szükség.
            </p>

            <p class="text-lg text-text-muted leading-relaxed">
              Ez azt jelenti, hogy először regisztrálni kell, vagy ha ez korábban már megtörtént és van már fiókod,
              akkor be kell jelentkezni. Ezután használhatóvá válik a
              <RouterLink to="/feltoltes" class="relative inline-block w-fit font-medium text-text link-hover italic">
                spotfeltöltés
              </RouterLink> oldal. Továbbá ha elmentesz egy spotot akkor az általad feltöltött spotok mellett ezeket meg
              tudod tekinteni a
              <RouterLink to="/profil" class="relative inline-block w-fit font-medium text-text link-hover italic">
                profil oldalon.
              </RouterLink>
            </p>
          </div>

        </div>
      </section>

    </div>
  </BaseLayout>
</template>

<route lang="yaml">
name: kezdolap
meta:
  title: FlowFinder
</route>