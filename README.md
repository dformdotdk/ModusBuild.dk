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
