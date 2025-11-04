# ModusBuild

Monolitten for ModusBuilds MVP er bygget på Laravel 11 med Vue/Inertia frontend-opsætning. Denne repo indeholder både tekniske RFC'er og det faktiske applikationskodegrundlag.

## Hurtig start

1. Kopiér `.env.example` til `.env` og opdater eventuelle hemmeligheder.
2. Start udviklingsmiljøet:

   ```bash
   make up
   ```

   Første kørsel forsøger at installere Composer-afhængigheder inde i containeren. I miljøer uden netværksadgang skal installationen køres lokalt på en maskine med adgang og artefakterne mountes ind i containeren.

3. Installer Node-afhængigheder og start Vite-dev serveren til frontend:

   ```bash
   npm install
   npm run dev
   ```

   Mangler der netværksadgang, kan pakker installeres på en ekstern maskine og kopieres ind via bind mounts.

4. Besøg `http://localhost:8080` for Laravel-app'en og `http://localhost:5173` for Vite dev server proxy.

## Kvalitetssikring

- `make pint` – kører Laravel Pint kodeformattering.
- `make phpstan` – kører Larastan statisk analyse.
- `make test` – kører Pest test-suiten.

## Dokumentation

- [RFC-001: ModusBuild – MVP teknisk specifikation (Draft)](docs/rfcs/RFC-001-modusbuild-mvp.md)

## Håndtering af GitHub-konflikter

Hvis GitHub viser en besked om konflikter, når du forsøger at merge en pull request, kan du løse dem lokalt med følgende fremgangsmåde:

1. Sørg for, at din lokale `main` (eller den branch, du vil merge ind i) er opdateret:
   ```bash
   git checkout main
   git fetch origin
   git pull origin main
   ```
2. Skift tilbage til din feature-branch (f.eks. `work`) og merge de seneste ændringer fra `main` ind:
   ```bash
   git checkout work
   git merge origin/main
   ```
   Git markerer nu de filer, der er i konflikt (fx `Makefile`, `composer.json`, `routes/web.php`).
3. Åbn hver konfliktfil i din editor og fjern konfliktmarkeringerne (`<<<<<<<`, `=======`, `>>>>>>>`) ved at vælge, kombinere eller omskrive indholdet, så det afspejler den ønskede endelige version.
4. Når alle konflikter er løst, stage filerne og fuldfør mergingen:
   ```bash
   git add Makefile bootstrap/cache/.gitignore composer.json config/database.php routes/web.php storage/.gitignore
   git commit
   ```
   Hvis merge-committen allerede blev oprettet automatisk, kan du nøjes med `git commit` for at afslutte den.
5. Afslut ved at pushe den opdaterede branch til GitHub:
   ```bash
   git push origin work
   ```
6. Gå tilbage til pull requesten på GitHub og verificér, at konflikten er væk. Herefter kan du fortsætte med review og merge.

> Tip: Hvis du foretrækker rebase-fremgangsmåden, kan du erstatte trin 2 med `git rebase origin/main` og afslutte eventuelle konflikter trin for trin. Husk at pushe med `--force-with-lease`, hvis du rebaser.
