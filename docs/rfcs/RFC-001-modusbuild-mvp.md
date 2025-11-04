RFC‑001: ModusBuild – MVP teknisk specifikation (Draft)

Dato: 4. november 2025
Status: Draft (til review)
Ejer: Product/Architecture
Forfatter: Daniel Duvald m.fl.
Version: 0.1
Produkt (arbejdstitel): ModusBuild
Tagline: Orden i projekteringen.
Produkt (arbejdstitel): ModusBuild
Tagline: Orden i projekteringen.
Produkt (arbejdstitel): ModusBuild
Tagline: Orden i projekteringen.

⸻

0. Formål & Scope

Formål. Definere en Minimum Viable Product (MVP) for ModusBuild (Intelligent Project Management & Guidance System), så et udviklingsteam entydigt kan forstå mål, afgrænsning, arkitektur, sikkerhed og acceptance criteria.
Scope (MVP). Alt under Prioritet 1 (Projektets Livscyklus) inkl. kerne‑CDE (dokumenter, versioner, simple workflows) og ModusBuild’ Intelligent Procesguide (gate‑logik).

Out of scope (MVP). Mobil offline‑app, avanceret BIM‑analyse, fuld eIDAS‑signatur, avanceret realtidstilsyn (kommer i P2/P3).

⸻

1. Ordliste & forkortelser
•ModusBuild: Intelligent Project Management & Guidance System.
•CDE: Common Data Environment (fælles dokument‑/versionsstyring).
•Gate: Formelt beslutningspunkt mellem faser.
•Phase/Fase: Projekttrin (fx A4/A5) med start/slut og status.
•Transmittal: Pakke af dokumenter sendt til modtagere med kvitteringslog.
•RBAC/ABAC: Role‑/Attribute‑based access control.
•RLS: Row Level Security (PostgreSQL).
•JSONB: Postgres JSON‑datatype til fleksible metadata.
•Outbox: Transaktionel event‑udbakning til pålidelig event‑publicering.
•BIM7AA/Molio A104: Klassifikations-/fasestruktur for DK byggeprojekter (bruges som felter i metadata).

⸻

2. Arkitekturprincipper
1.Modulith: Monolitisk deployment, modulær kode (bounded contexts), lav kobling via events.
2.Kontrakter før kode: Service‑interfaces, events, API’er dokumenteres før implementering.
3.Simpelt først: MVP optimerer for leverance og driftssikkerhed.
4.Sikkerhed som standard: Least privilege, audit, GDPR.
5.Observability: Logning, metrics, tracing fra start.
6.Testbarhed: Kritiske flows dækkes af automatiske tests.

⸻

3. Systemoversigt
•Central kerne: Auth/identitet, organisationer/brugere, multitenancy, global event‑bus, API‑gateway.
•Domænemoduler (MVP):
•Salg & Økonomi (Offer→Contract)
•Proces & Styring (templates, faser, gates, ekstern godkendelse)
•Dokumenter & Granskning (CDE: dokumenter, versioner, workflows, transmittals)

3.1 Multitenancy (SaaS)

Valg: Single database med tenant_id på alle rækker + Postgres RLS.
Rationale: Enkel drift, stærk isolation, fleksibel skalering.
Håndhævelse: Global scopes + Policies + RLS; audit på kryds‑tenant adgang (forbudt).

3.2 Modulith & bounded contexts
•Hvert modul har eget namespace, migrations (med tabelprefix), services og events.
•Ingen direkte tværmodulkald til intern kode; brug service‑kontrakter/events.

3.3 Events & Transactional Outbox
•Alle domæneevents skrives til outbox_events i samme DB‑transaktion.
•Baggrundsworker publicerer events; consumers er idempotente (dedupe key).

⸻

4. Teknologistak

KomponentTeknologiBegrundelse
BackendPHP 8.x / Laravel (LTS)Hurtig udvikling, moden økosystem
DatabasePostgreSQL 15+ (JSONB, RLS)Relationer + fleksible metadata + sikkerhed
FrontendVue 3 + Inertia.js + TailwindSPA‑oplevelse, enkel routing fra Laravel
Asynkrone jobsRedis (Queues/Cache)Upload‑/preview‑pipeline, notifikationer
StorageS3‑kompatibel (presigned URLs)Skalerbar, kryptering, livscyklus
ViewerPDF.js (2D), IFC.js/Three.js (light IFC)Indlejret visning
RealtimeLaravel Reverb/WebSockets (senere P2)Notifikationer/live status
CI/CDGitHub Actions/Forge/Envoyer (eksempel)Test, build, zero‑downtime deploy

⸻

5. Sikkerhed & Compliance (GDPR)
•RBAC via Spatie + Policies; projekt‑scopede roller (Owner, Lead, Engineer, External Reviewer).
•ABAC‑udvidelse for document access rules (fase, fag, rolle).
•Gæsteadgang via signerede, tidsbegrænsede tokens (scope‑begrænset).
•Kryptering: TLS i transit, S3 SSE i hvile; hemmeligheder i KMS/Secrets‑manager.
•Audit log: læs/skriv/download, login, rolleændringer, gate‑beslutninger.
•Privacy: Databehandleraftale, dataportabilitet/sletning pr. tenant, data i EU.
•Virus‑scan ved upload (ClamAV), MIME‑whitelist, filstørrelsesgrænser.

⸻

6. Ikke‑funktionelle krav (MVP SLO’er)
•Ydelse: Dokumentliste P95 < 400 ms ved 10k dokumenter/tenant; upload init < 200 ms.
•Tilgængelighed: Public API 99.9% pr. måned.
•Skalerbarhed: 100 samtidige aktive brugere/tenant uden kø‑backlog.
•Sikkerhed: OWASP ASVS L2; afhængighedsscanning i CI.
•Observability: Struktureret logning, 10 centrale metrics, trace‑sampling ≥ 10%.

⸻

7. Domænemodel (oversigt)

Kerne (multitenancy/identitet)
tenants(id, name, plan_id, ...)
organizations(id, tenant_id, name, ...)
users(id, ...) + organization_user(user_id, org_id, role)

Projekter & proces
projects(id, tenant_id, org_id, template_id, status, ...)
project_templates(id, tenant_id, name, jsonb_structure)
phases(id, project_id, code, status, starts_at, ends_at)
gates(id, project_id, phase_id, status, decided_at, decided_by)
external_approvals(id, phase_id, approver_ref, approved_at, token_hash)

Salg & økonomi
offers(id, tenant_id, org_id, project_ref, total, status)
contracts(id, offer_id, text_md, signed_at, signer_ref)

CDE (dokumenter)
documents(id, project_id, code, discipline, phase_code, classification_jsonb, status, current_version_id)
document_versions(id, document_id, rev, version, storage_key, checksum, size, mime, approved_at, approved_by)
workflows(id, project_id, name, steps_jsonb)
transmittals(id, project_id, number, recipients_jsonb, sent_at, receipt_log_jsonb)

Infrastruktur
outbox_events(id, event_type, payload_jsonb, dedupe_key, published_at)
audit_logs(id, actor_id, action, target_type, target_id, ts, meta_jsonb)

Metadatafelter (eksempler)
•discipline (Molio fag), phase_code (A104), classification_jsonb (BIM7AA typekode/typenr/tekst), ansvarlig rolle, distributionsmatrix, transmittal‑nr., statusmaskine: Draft → for_review → approved_to_publish → Published → Superseded.

⸻

8. MVP‑moduler & krav

8.1 Salg & Økonomi

Formål: Opret tilbud → kontrakt → trig projektoprettelse.
Krav:
•Offer med status (Draft, Sent, Accepted, Rejected).
•Contract bundet til accepteret Offer.
•E‑signatur (MVP): mail‑token + 2‑faktor kvittering (kode via mail/SMS).
•Event OfferAccepted publiceres via Outbox.
Acceptance: Accepteret tilbud skaber kontrakt og emitterer event; audit spores.

8.2 Proces & Styring (ModusBuild kerne)

Formål: Skabelon‑drevet livscyklus med gates.
Krav:
•ProjectTemplate i JSONB (mappestruktur, faser, gates, checklister).
•Oprettelse af Project, Phases, Gates fra template.
•GateService::validate() tjekker:
1.Interne krav: alle dokumenter i fase approved_to_publish
2.Eksterne krav: ExternalApproval registreret
3.Ingen åbne workflow‑trin
•Gæstegodkendelse via signerede tokens (udløb).
Acceptance: Gate kan kun passeres når regler er opfyldt; afslag giver årsagsliste.

8.3 Dokumenter & Granskning (CDE)

Formål: Robust filhåndtering, versionering, visning.
Krav:
•Document + DocumentVersion; automatisk revision (A→B) og version (1.0→1.1).
•Upload‑pipeline i queue: virus‑scan, MIME‑check, metadata ekstrakt, preview/thumbnail.
•Storage via S3 presigned URLs; server‑side encryption.
•Viewer: PDF.js (2D). IFC.js (light) til simpelt IFC‑preview (P3 kan udbygges).
•Workflows: Trin pr. rolle (fx Ingeniør → Projektleder).
•Transmittals: Send, kvittering, historik.
Acceptance: Versionhistorik synlig; diff (metadata & side‑by‑side) for PDF; statusmaskine håndhæves i Policies.

⸻

9. Domain events (eksempler)
•offer.accepted
Payload: { offer_id, org_id, project_seed, total, accepted_at }
•project.initialized
Payload: { project_id, template_id, phases:[...], gates:[...] }
•document.version.approved
Payload: { document_id, version_id, approver_id, approved_at }
•gate.passed
Payload: { project_id, phase_id, gate_id, decided_by, decided_at }

Webhooks (eksternt, signerede): Kan abonneres pr. tenant på ovenstående events (rate‑limited, retry med exponential backoff).

⸻

10. Nøgleflows (tekstlig sekvens)

Flow A: Offer → Project
1.Offer → Accepted → emitter offer.accepted.
2.Proces‑modul lytter → CreateProjectFromTemplate → project.initialized.
3.Brugere tildeles projektroller.

Flow B: Upload → Approve → Gate
1.Upload ny fil → queue pipeline → document.version.created.
2.Workflow fuldføres → approver godkender → document.version.approved.
3.Gate review → hvis alle krav opfyldt → gate.passed.

Flow C: Transmittal
1.Opret pakke → send signerede links → spor åbninger/kvitteringer → log i transmittals.

⸻

11. API (overblik)
•Auth: Laravel Sanctum (PAT) for integratorer.
•V1 Endpoints (eksempler):
•POST /api/v1/offers/{id}/accept
•POST /api/v1/projects/{id}/gates/{gateId}/request-review
•POST /api/v1/documents (metadata)
•POST /api/v1/documents/{id}/versions (presigned upload)
•POST /api/v1/document-versions/{id}/approve
•POST /api/v1/transmittals / GET /api/v1/transmittals/{id}
•Webhooks: POST https://client.example/webhooks/modusbuild (HMAC‑signatur, idempotency key).

⸻

12. Frontend/UX (Inertia/Vue)
•Skærme: Dashboard (gate readiness), Projekter, Dokumenter (kraftig filtrering), Versionvisning/diff, Transmittals, Godkendelser.
•UX‑principper: Tom‑tilstande med næste skridt, inline fejl, tastaturgenveje, tilgængelighed (ARIA).
•Ydelse: Lazy data‑hentning, pagination, client‑side caching af seneste filtre.

⸻

13. Teststrategi
•Unit/Feature: GateService, VersioningService, Access Policies.
•Queue: Uploadpipeline med stubs for virus‑scan.
•Contract/Snapshot: API‑responses v1.
•E2E (Dusk):
1.Offer→Project
2.Upload→Approve→GatePass
3.Transmittal→Receipt.

⸻

14. Observability & Drift
•Logging: Struktureret (JSON), korrelerings‑ID’er, PII‑redaction.
•Metrics: Job‑latens/svigt, request P95/P99, uploads/min, godkendelser/dag, gate‑throughput.
•Tracing: OpenTelemetry.
•Backups: DB dagligt + 7/30 rotation; restore‑øvelse dokumenteret.
•Feature flags: Laravel Pennant for gradvis udrulning.

⸻

15. Roadmap (P2/P3 – kort)
•P2: Realtime notifikationer, mobil venlige flows, forbedret IFC‑viewer, BCF‑issues.
•P3: eIDAS/MitID Erhverv signatur, avanceret BIM‑mængdeudtræk (evt. Python mikroservice), offline mobilapp.

⸻

16. Åbne spørgsmål & antagelser
•Skal ProjectTemplate dække både mappe‑/dokumentstruktur og fase/gate‑checklister, eller adskilles?
•Krav til datalokation/regioner pr. tenant?
•Behov for kundespecifikke klassifikationer ud over BIM7AA/Molio?
•Minimum password/2FA‑politik ved ekstern godkendelse i MVP?

⸻

17. Acceptance Criteria (MVP “Definition of Done”)
1.Gate‑styring: En fase kan kun passeres hvis (a) alle dokumenter er approved_to_publish, (b) ExternalApproval foreligger, (c) ingen åbne workflow‑trin.
2.CDE: Versionhistorik med automatisk revision/version; presigned upload; PDF preview; audit på visning/download.
3.Salg→Projekt: Accept af tilbud opretter kontrakt og initierer projekt + faser/gates fra template.
4.Sikkerhed: Tenant-isolation håndhæves via global scopes + Policies; (valgfrit) Postgres RLS kan aktiveres og anbefales fra P2 for ekstra isolation.
5.SLO’er: Ydelses‑ og tilgængelighedsmål i afsnit 6 dokumenteret i produktion‑lignende miljø.
6.Tests: Alle kerne‑featuretests grønne; e2e for tre nøgleflows passerer.
7.Drift: Backups/restore testet; logs/metrics/alerts aktive; release‑procedure dokumenteret.

⸻

18. Implementeringsplan (Sprint 0 → Sprint 2)

Sprint 0 – Fundament (1 uge)
•Repo, CI (GitHub Actions), PHPStan/Larastan, Pint, Pest.
•Miljø: Docker Compose (php-fpm, nginx, pg, redis, clamav optional).
•Laravel install, Sanctum, Spatie Permissions, Pennant, Telescope (dev).
•Multitenancy: tenant_id kolonne + global scope + Policy‑guard (RLS design dokumenteret; aktiveres i P2).
•Skabelon for modul‑migrations (prefiks pr. modul).

Sprint 1 – CDE & Outbox (2 uger)
•documents, document_versions, workflows, transmittals.
•Upload‑pipeline (queues), presigned S3, virus‑scan stub, thumbnails/preview for PDF.
•Outbox: tabel, publisher job, idempotente consumers.
•API v1 for dokumenter/versions; basic UI: liste, upload, preview.
•Audit log grundlag (visning/download/approve).

Sprint 2 – Proces & Gates + Salg (2 uger)
•project_templates, projects, phases, gates, external_approvals.
•GateService m. regler + e2e flow (Upload→Approve→GatePass).
•offers, contracts, acceptflow m. 2‑faktor kvittering.
•Webhooks for document.version.approved, gate.passed.

⸻

19. Databaseskema (detaljer)

Notation: pk=primærnøgle, fk=fremmednøgle, idx=indeks, uq=unik.

19.1 Multitenancy & identitet

tenants
•id (uuid, pk)
•name (text, uq)
•plan_id (text)
•created_at/updated_at

organizations
•id (uuid, pk), tenant_id (uuid, fk tenants.id, idx)
•name (text), slug (text, uq per tenant)
•created_at/updated_at

organization_user
•organization_id (uuid, fk), user_id (uuid, fk), role (text)
•pk(organization_id,user_id); idx(role)

19.2 Salg & Økonomi

offers
•id (uuid, pk), tenant_id (uuid, idx), org_id (uuid, fk organizations.id, idx)
•project_seed (jsonb)
•total (numeric(12,2))
•status (text: draft|sent|accepted|rejected)
•sent_at, accepted_at

contracts
•id (uuid, pk), offer_id (uuid, fk offers.id, uq)
•text_md (text)
•signed_at (timestamptz)
•signer_ref (jsonb)

19.3 Projekter & proces

project_templates
•id (uuid, pk), tenant_id (uuid, idx)
•name (text), jsonb_structure (jsonb, GIN idx)

projects
•id (uuid, pk), tenant_id (uuid, idx), org_id (uuid, fk)
•template_id (uuid, fk)
•status (text: active|archived)
•code (text)

phases
•id (uuid, pk), project_id (uuid, fk, idx)
•code (text), name (text), status (text: active|closed|pending_gate_review)
•starts_at, ends_at


gates
•id (uuid, pk), project_id (uuid, fk, idx), phase_id (uuid, fk, uq)
•status (text: open|passed|rejected)
•decided_by (uuid, fk users.id), decided_at, reason (text)

external_approvals
•id (uuid, pk), phase_id (uuid, fk, idx)
•approver_ref (jsonb), approved_at (timestamptz)
•token_hash (text, uq)

19.4 CDE

documents
•id (uuid, pk), project_id (uuid, fk, idx)
•code (text), title (text), discipline (text)
•phase_code (text), classification_jsonb (jsonb, GIN idx)
•status (text: draft|for_review|approved_to_publish|published|superseded)
•current_version_id (uuid, fk document_versions.id, null ok)
•locked_version (int) (optimistic locking)
•UQ (project_id, code)

document_versions
•id (uuid, pk), document_id (uuid, fk, idx)
•rev (text) (A,B,C…), version (text) (1.0,1.1…)
•storage_key (text, uq), checksum (text), size (bigint), mime (text)
•approved_at, approved_by (uuid, fk users.id)

workflows
•id (uuid, pk), project_id (uuid, fk, idx)
•name (text), steps_jsonb (jsonb)

transmittals
•id (uuid, pk), project_id (uuid, fk, idx)
•number (text, uq per project), recipients_jsonb (jsonb), sent_at (timestamptz)
•receipt_log_jsonb (jsonb)

19.5 Infrastruktur

outbox_events
•id (bigserial, pk), event_type (text, idx), payload_jsonb (jsonb)
•dedupe_key (text, uq), published_at (timestamptz null)

audit_logs
•id (bigserial, pk), actor_id (uuid, idx), action (text)
•target_type (text), target_id (uuid)
•ts (timestamptz, idx), meta_jsonb (jsonb)

Indekser (eksempler)
•GIN på classification_jsonb, project_templates.jsonb_structure.
•Komposit: idx_documents_project_status (project_id,status).
•UQ: (document_id, rev, version) på document_versions.

⸻

20. State machines & regler

Document.status: draft → for_review → approved_to_publish → published → superseded
Overgange styres af Policies + Workflow‑trin.
Gate.status: open → passed|rejected (med reason).
Offer.status: draft → sent → accepted|rejected.

Revision/Version
•Ny fil på eksisterende dokument: version bump (1.0→1.1).
•Betydende ændring besluttet af rolle: rev bump (A→B).
•approved_to_publish sætter documents.current_version_id.

⸻

21. Permissions‑matrix (uddrag)

RolleDokument uploadSætte for_reviewApproveGate decisionTransmittal
Owner✓✓✓✓✓
Lead✓✓✓✓ (deleg.)✓
Engineer✓✓–––
External Reviewer––––kvittering

Politikker håndhæver projekt‑scope + tenant‑scope. Download/preview kræver tilknytning til projekt eller signerede links.

⸻

22. API v1 – specifikation (uddrag)

Auth: Bearer (Sanctum PAT). X-Tenant header (tenant slug/uuid).

22.1 Dokumenter

POST /api/v1/documents
Req:

{ "project_id": "uuid", "code": "A-001", "title": "Situationsplan", "discipline": "C", "phase_code": "A4", "classification": {"bim7aa": {"type_code": "C07.01", "type_no": "...", "type_text": "..."}} }

Res 201:

{ "id": "uuid", "status": "draft" }

POST /api/v1/documents/{id}/versions → presigned upload
Req:

{ "mime": "application/pdf", "size": 1048576 }

Res 200:

{ "upload": {"url": "https://s3...", "fields": {"key": "...", "policy": "..."}}, "version_id": "uuid" }

POST /api/v1/document-versions/{id}/approve
Res 200: { "approved_at": "...", "document": {"status": "approved_to_publish"} }

22.2 Gates

POST /api/v1/projects/{id}/gates/{gateId}/request-review → trigger validation
Res 200 (ok): { "result": "passed", "decided_at": "..." }

22.3 Offers

POST /api/v1/offers/{id}/accept → opretter contract + emitter event
Res 200: { "contract_id": "uuid", "project": {"id": "uuid"} }

⸻

23. Webhooks

Event: document.version.approved
Payload:

{ "event":"document.version.approved", "id":"evt_123", "occurred_at":"2025-11-04T10:00:00Z", "tenant_id":"...", "data": { "document_id":"...", "version_id":"...", "approved_by":"..." } }

Sikkerhed: X-ModusBuild-Signature (HMAC-SHA256 over body).
Retry: Exponential backoff op til 24t; idempotency via id.

⸻

24. Upload‑pipeline (jobs)
1.InitUploadJob: valider mime/size → generér presigned.
2.IngestVersionJob: efter klientupload (S3 event/webhook eller klient‑confirm) → hent head → checksum → virus‑scan (stub) → udtræk metadata → generér thumbnail/pdf preview → opdater document_versions.
3.SetCurrentIfApprovedJob: hvis version approved → set documents.current_version_id.
Fejl & retries: Job retries m. dead‑letter queue; audit på fejl.

⸻

25. Domain events (skemaer)

offer.accepted:

{ "offer_id":"uuid", "org_id":"uuid", "project_seed":{}, "total":12345.67, "accepted_at":"..." }

project.initialized:

{ "project_id":"uuid", "template_id":"uuid", "phases":[{"id":"uuid","code":"A4"}], "gates":[{"id":"uuid","phase_id":"uuid"}] }

gate.passed:

{ "project_id":"uuid", "phase_id":"uuid", "gate_id":"uuid", "decided_by":"uuid", "decided_at":"..." }

⸻

26. Testskabeloner (Pest)

GateService.feature
•it_prevents_gate_when_docs_missing_approval()
•it_allows_gate_when_requirements_met()
•it_logs_audit_on_gate_decision()

VersioningService.feature
•it_bumps_version_on_new_file()
•it_bumps_revision_when_flagged()

Outbox.feature
•it_writes_event_in_same_transaction()
•it_publishes_and_is_idempotent()

⸻

27. Seed data (dev)
•Tenant demo, Org Demo A/S, Users: owner/lead/engineer.
•Project template Rækkehuse – A4→A5, simple gate‑checkliste.
•10 demo‑dokumenter m. BIM7AA stubklassifikation.

⸻

28. AI Prompt Pack (til udvikling)

Generelt mønster

“Du er Laravel‑arkitekt. Følg RFC‑001. Generér migration, model, policy og Pest‑tests for <entity> med felter: <felter>; brug uuid for pk; tilføj indekser og tenant_id. Implementér Repository + Service skeleton. Skriv controller m. endpoints fra §22 og returnér JSON resources.”

Eksempel – Document & Version
•Migration prompt: “Skriv Laravel migrations for documents og document_versions jf. RFC‑001 §19.4 inkl. indekser og fremmednøgler; uuid pk; soft deletes fravælges.”
•Model prompt: “Eloquent models med relations + casts (classification_jsonb→array), fillable, scopes for project/status.”
•Policy prompt: “Policies der begrænser til projektroller og tenant; tests for allow/deny.”
•Service prompt: “VersioningService med bumpVersion()/bumpRevision() + unit tests.”
•Controller prompt: “Store/Show/Index; POST /versions med presigned S3 via Storage facade.”

Eksempel – GateService

“Implementér GateService::validate(project, nextPhase) iht. §8.2. Returnér objekt m. ok:boolean, reasons:[]. Skriv 3 Pest‑tests fra §26.”

Eksempel – Outbox

“Migration + Model for outbox_events; Publisher job; trait EmitsDomainEvents til atomar skrivning i samme transaktion; Pest‑tests for idempotens.”

⸻

29. Miljø & konfiguration

.env (uddrag)

APP_ENV=local
APP_URL=https://modusbuild.local
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=modusbuild
DB_USERNAME=modusbuild
DB_PASSWORD=secret
QUEUE_CONNECTION=redis
REDIS_HOST=redis
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_DEFAULT_REGION=eu-central-1
AWS_BUCKET=modusbuild-dev
SIGNING_SECRET=change-me
MAX_UPLOAD_MB=250
CLAMAV_HOST=clamav
CLAMAV_PORT=3310
SANCTUM_STATEFUL_DOMAINS=modusbuild.local

Nøgleskifter
•FEATURE_RLS=false (kan aktiveres i P2).
•FEATURE_IFC_PREVIEW=false (P3).

⸻

30. Projektstruktur (foreslået)

app/
  Domain/
    CDE/
    Process/
    Sales/
  Http/Controllers/Api/V1/
  Models/
  Policies/
  Services/
  Jobs/
  Events/
  Listeners/
  Resources/
config/
database/migrations/
resources/js/ (Vue/Inertia)

⸻

31. CI/CD (GitHub Actions – skitse)
•Trig på PR: composer install, cache, pint --test, phpstan, pest, laravel pint.
•Byg & deploy (main): env‑vars via secrets; migrations step; horizon restart.

⸻

32. OpenAPI (skitse)

openapi: 3.1.0
info:
  title: ModusBuild API
  version: 1.0.0
paths:
  /api/v1/documents:
    post:
      summary: Create document
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/DocumentCreate'
      responses:
        '201': { description: Created }
components:
  schemas:
    DocumentCreate:
      type: object
      required: [project_id, code, title]
      properties:
        project_id: { type: string, format: uuid }
        code: { type: string }
        title: { type: string }
        discipline: { type: string }
        phase_code: { type: string }
        classification: { type: object }

⸻

33. Definition of Ready (DoR) for user stories
•API‑kontrakt defineret, felter & validering anført.
•Acceptance tests (Gherkin) skitseret.
•Sikkerhedsforventninger (roller/policies) beskrevet.
•Observability‑events/målepunkter noteret.

⸻

34. Bilag: Gherkin‑scenarier (uddrag)

Gate pass

Given a project with phase A4 and all documents approved_to_publish
And an ExternalApproval exists for phase A4
When I request gate review for A4
Then the gate decision is passed
And an audit log entry is stored

Upload & approve

Given a document in status draft
When I upload a PDF version and approve it
Then the document status becomes approved_to_publish
And current_version_id points to the approved version

⸻

35. OpenAPI.yaml (MVP)

Maskinlæsbar API‑kontrakt for MVP. Kan importeres i Postman/Insomnia, bruges til mock servere (fx Prism) og til generering af typed klienter.

openapi: 3.1.0
info:
  title: ModusBuild API (MVP)
  version: 0.1.0
  description: Contract-first specifikation for ModusBuild v1 (MVP). Se RFC‑001 for forretningsregler.
servers:
  - url: https://api.modusbuild.local
    description: Local/dev
security:
  - bearerAuth: []
paths:
  /api/v1/documents:
    get:
      summary: List documents
      parameters:
        - in: query
          name: project_id
          schema: { type: string, format: uuid }
        - in: query
          name: status
          schema: { type: string, enum: [draft, for_review, approved_to_publish, published, superseded] }
        - in: query
          name: q
          schema: { type: string }
        - in: query
          name: page
          schema: { type: integer, minimum: 1 }
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items: { $ref: '#/components/schemas/Document' }
                  meta: { $ref: '#/components/schemas/Pagination' }
    post:
      summary: Create document (metadata)
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/DocumentCreate' }
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Document' }
  /api/v1/documents/{id}:
    get:
      summary: Get document by id
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200': { description: OK, content: { application/json: { schema: { $ref: '#/components/schemas/Document' } } } }
        '404': { $ref: '#/components/responses/NotFound' }
  /api/v1/documents/{id}/versions:
    post:
      summary: Init new version upload (presigned URL)
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [mime, size]
              properties:
                mime: { type: string }
                size: { type: integer, minimum: 1 }
      responses:
        '200':
          description: Upload details
          content:
            application/json:
              schema:
                type: object
                properties:
                  upload:
                    type: object
                    properties:
                      url: { type: string, format: uri }
                      fields: { type: object, additionalProperties: true }
                  version: { $ref: '#/components/schemas/DocumentVersion' }
  /api/v1/document-versions/{id}/approve:
    post:
      summary: Approve a version
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200': { description: Approved, content: { application/json: { schema: { $ref: '#/components/schemas/Document' } } } }
        '409': { description: Conflict (policy/state fails), content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
  /api/v1/transmittals:
    post:
      summary: Create transmittal
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/TransmittalCreate' }
      responses:
        '201': { description: Created, content: { application/json: { schema: { $ref: '#/components/schemas/Transmittal' } } } }
    get:
      summary: List transmittals
      parameters:
        - in: query
          name: project_id
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data: { type: array, items: { $ref: '#/components/schemas/Transmittal' } }
                  meta: { $ref: '#/components/schemas/Pagination' }
  /api/v1/transmittals/{id}:
    get:
      summary: Get transmittal
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200': { description: OK, content: { application/json: { schema: { $ref: '#/components/schemas/Transmittal' } } } }
        '404': { $ref: '#/components/responses/NotFound' }
  /api/v1/projects:
    get:
      summary: List projects
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data: { type: array, items: { $ref: '#/components/schemas/Project' } }
                  meta: { $ref: '#/components/schemas/Pagination' }
  /api/v1/projects/{id}/gates/{gateId}/request-review:
    post:
      summary: Request gate review/decision
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
        - in: path
          name: gateId
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: Decision
          content:
            application/json:
              schema:
                type: object
                properties:
                  result: { type: string, enum: [passed, rejected] }
                  decided_at: { type: string, format: date-time }
                  reasons: { type: array, items: { type: string } }
        '422': { description: Validation failed, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
  /api/v1/offers/{id}/accept:
    post:
      summary: Accept offer → create contract and project
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: Accepted
          content:
            application/json:
              schema:
                type: object
                properties:
                  contract: { $ref: '#/components/schemas/Contract' }
                  project: { $ref: '#/components/schemas/Project' }
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
  responses:
    NotFound:
      description: Not found
      content:
        application/json:
          schema: { $ref: '#/components/schemas/Error' }
  schemas:
    Pagination:
      type: object
      properties:
        current_page: { type: integer }
        per_page: { type: integer }
        total: { type: integer }
    Error:
      type: object
      properties:
        message: { type: string }
        code: { type: string }
    Project:
      type: object
      properties:
        id: { type: string, format: uuid }
        org_id: { type: string, format: uuid }
        template_id: { type: string, format: uuid }
        status: { type: string, enum: [active, archived] }
        code: { type: string }
    Contract:
      type: object
      properties:
        id: { type: string, format: uuid }
        offer_id: { type: string, format: uuid }
        signed_at: { type: string, format: date-time }
    Document:
      type: object
      properties:
        id: { type: string, format: uuid }
        project_id: { type: string, format: uuid }
        code: { type: string }
        title: { type: string }
        discipline: { type: string }
        phase_code: { type: string }
        classification: { type: object, additionalProperties: true }
        status:
          type: string
          enum: [draft, for_review, approved_to_publish, published, superseded]
        current_version_id: { type: string, format: uuid, nullable: true }
    DocumentCreate:
      type: object
      required: [project_id, code, title]
      properties:
        project_id: { type: string, format: uuid }
        code: { type: string }
        title: { type: string }
        discipline: { type: string }
        phase_code: { type: string }
        classification: { type: object, additionalProperties: true }
    DocumentVersion:
      type: object
      properties:
        id: { type: string, format: uuid }
        document_id: { type: string, format: uuid }
        rev: { type: string }
        version: { type: string }
        storage_key: { type: string }
        mime: { type: string }
        size: { type: integer }
        approved_at: { type: string, format: date-time, nullable: true }
    Transmittal:
      type: object
      properties:
        id: { type: string, format: uuid }
        project_id: { type: string, format: uuid }
        number: { type: string }
        recipients: { type: array, items: { type: string } }
        sent_at: { type: string, format: date-time, nullable: true }
    TransmittalCreate:
      type: object
      required: [project_id, number, recipients]
      properties:
        project_id: { type: string, format: uuid }
        number: { type: string }
        recipients: { type: array, items: { type: string, format: email } }
webhooks:
  document.version.approved:
    post:
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                event: { type: string, enum: [document.version.approved] }
                id: { type: string }
                occurred_at: { type: string, format: date-time }
                tenant_id: { type: string }
                data:
                  type: object
                  properties:
                    document_id: { type: string, format: uuid }
                    version_id: { type: string, format: uuid }
                    approved_by: { type: string, format: uuid }


⸻

36. Artisan‑kommandoer (scaffolding‑bibliotek)

Kør disse i feature‑branches pr. modul. Tilpas namespaces til din projektstruktur (se §30). AI kan generere filindhold fra RFC‑felter og regler.

# === Infrastruktur ===
php artisan make:model Domain/Infrastructure/OutboxEvent -m
php artisan make:job OutboxPublisherJob
php artisan make:model Domain/Infrastructure/AuditLog -m

# === Identitet / multitenancy ===
php artisan make:model Domain/Core/Tenant -mfs
php artisan make:model Domain/Core/Organization -mfs
php artisan make:migration create_organization_user_table

# === Sales (offers/contracts) ===
php artisan make:model Domain/Sales/Offer -mfs
php artisan make:model Domain/Sales/Contract -mfs
php artisan make:event OfferAccepted
php artisan make:listener InitializeProjectFromOffer
php artisan make:controller Http/Controllers/Api/V1/OfferController --api

# === Process (projects/phases/gates/external approvals) ===
php artisan make:model Domain/Process/ProjectTemplate -mfs
php artisan make:model Domain/Process/Project -mfs
php artisan make:model Domain/Process/Phase -mfs
php artisan make:model Domain/Process/Gate -mfs
php artisan make:model Domain/Process/ExternalApproval -mfs
php artisan make:policy ProjectPolicy --model=Domain/Process/Project
php artisan make:controller Http/Controllers/Api/V1/ProjectController --api
php artisan make:controller Http/Controllers/Api/V1/GateController --api
php artisan make:event ProjectInitialized
php artisan make:event GatePassed
php artisan make:listener CreateProjectFromTemplate
php artisan make:service GateService  # (alternativ: opret app/Services/GateService.php manuelt)

# === CDE (documents/versions/workflows/transmittals) ===
php artisan make:model Domain/CDE/Document -mfs
php artisan make:model Domain/CDE/DocumentVersion -mfs
php artisan make:model Domain/CDE/Workflow -mfs
php artisan make:model Domain/CDE/Transmittal -mfs
php artisan make:policy DocumentPolicy --model=Domain/CDE/Document
php artisan make:controller Http/Controllers/Api/V1/DocumentController --api
php artisan make:controller Http/Controllers/Api/V1/DocumentVersionController --api
php artisan make:controller Http/Controllers/Api/V1/TransmittalController --api
php artisan make:job IngestVersionJob
php artisan make:job SetCurrentIfApprovedJob
php artisan make:event DocumentVersionApproved
php artisan make:listener PublishApprovedVersion
php artisan make:resource DocumentResource
php artisan make:resource DocumentVersionResource
php artisan make:resource TransmittalResource

# === Tests (Pest) ===
php artisan make:test GateServiceTest --pest
php artisan make:test VersioningServiceTest --pest
php artisan make:test OutboxEventTest --pest
php artisan make:test DocumentPolicyTest --pest

# === API Routes scaffold (manuelt i routes/api.php) ===
# Route::prefix('v1')->middleware(['auth:sanctum'])->group(function() { ... });

⸻

37. “First commit” tjekliste (kopi‑klar)

Repo & licens
•Init git repo + .gitignore (Laravel)
•LICENSE (MIT)
•README med runbook

Dev‑miljø
•Docker Compose (php-fpm, nginx, postgres, redis, mailhog, optional clamav)
•.env.example (se §29)
•Makefile/NPM scripts: make up, make test, make pint

Laravel & pakker
•Laravel install + php artisan key:generate
•Sanctum, Spatie Permissions, Pennant, Telescope (local)
•Konfigurer logging (json i prod), Horizon til queues

Kvalitet & CI
•Pint config (pint.json)
•PHPStan/Larastan (phpstan.neon)
•Pest set‑up + tests/ mappe
•GitHub Actions workflow: pint, phpstan, pest

Struktur
•Domænmapper: app/Domain/{CDE,Process,Sales,Core,Infrastructure}
•app/Services, app/Policies, app/Jobs, app/Events, app/Listeners, app/Http/Controllers/Api/V1
•routes/api.php v1‑prefix + rate limit

OpenAPI & klient
•Commit openapi.yaml (se §35)
•(Valgfrit) Installer openapi‑generator eller Orval til TS‑klient
•Postman/Insomnia collection genereret fra OpenAPI

Observability
•Sentry DSN (prod), Telescope (local), health endpoint
•Core metrics defineret (se §14)

Security/GDPR
•Secrets via .env/CI secrets
•Standard password/2FA politik dokumenteret
•Audit log strategi committet

Bootstrap commits (forslag)
1.chore: bootstrap Laravel + Docker + base CI (pint/phpstan/pest)
2.feat(core): add Sanctum, Permissions, Pennant; base folders & logging
3.chore(env): add .env.example and docker configs for pg/redis/s3
4.docs: add README and OpenAPI contract (v1 scaffolding)

Første feature‑branch
•feature/cde-documents: migrations, models, policies, controllers, jobs, tests
•Implementér endpoints fra OpenAPI §35 for documents/versions
•Grøn CI + demo seed‑data

⸻

38. Bonus: Generering af TypeScript‑klient (frontend)

Orval (simpelt):

npm i -D orval
# orval.config.ts peger på openapi.yaml og genererer /resources/js/api/modusbuild.ts
npx orval --config orval.config.ts

OpenAPI Generator (alternativ):

npm i -D @openapitools/openapi-generator-cli
npx openapi-generator-cli generate \
  -i openapi.yaml \
  -g typescript-fetch \
  -o resources/js/api

Anvendelse i Vue (Inertia):
•Importér genereret klient, brug typed metoder i stores/components.
•Mock backend via Prism under UI‑udvikling.

⸻

39. OpenAPI.yaml (udvidet – kontrakt med fejlmodel, parametre, headers, examples)

Denne version udvider §35 og er den gældende kontrakt (rev 0.2). Den tilføjer RFC7807-fejlmodel, standardiserede query-parametre, headers (RateLimit, ETag, Idempotency-Key), operationIds og eksempler.

openapi: 3.1.0
info:
  title: ModusBuild API (MVP)
  version: 0.2.0
  description: Contract-first specifikation for ModusBuild v1 (MVP). Se RFC‑001 for forretningsregler.
servers:
  - url: https://api.modusbuild.local
    description: Local/dev
security:
  - bearerAuth: []
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
  parameters:
    ProjectId:
      in: query
      name: project_id
      schema: { type: string, format: uuid }
    Status:
      in: query
      name: status
      schema: { type: string, enum: [draft, for_review, approved_to_publish, published, superseded] }
    Q:
      in: query
      name: q
      schema: { type: string }
    Page:
      in: query
      name: page
      schema: { type: integer, minimum: 1, default: 1 }
    PerPage:
      in: query
      name: per_page
      schema: { type: integer, minimum: 1, maximum: 100, default: 25 }
    Sort:
      in: query
      name: sort
      description: 'fx: code,-updated_at'
      schema: { type: string }
    DateFrom:
      in: query
      name: date_from
      schema: { type: string, format: date-time }
    DateTo:
      in: query
      name: date_to
      schema: { type: string, format: date-time }
    TenantHeader:
      in: header
      name: X-Tenant
      schema: { type: string }
      description: Optional tenant identifier; if omitted, derived from token
  headers:
    RateLimit-Limit:
      schema: { type: integer }
      description: Requests per timevindue for klienten
    RateLimit-Remaining:
      schema: { type: integer }
      description: Resterende requests i nuværende vindue
    RateLimit-Reset:
      schema: { type: integer }
      description: Sekunder til reset af vindue
    ETag:
      schema: { type: string }
      description: Entity tag til conditional GET
  responses:
    Problem:
      description: RFC7807 Problem Details
      content:
        application/problem+json:
          schema:
            $ref: '#/components/schemas/Problem'
    Unauthorized:
      description: Unauthorized
      content:
        application/problem+json:
          schema: { $ref: '#/components/schemas/Problem' }
    Forbidden:
      description: Forbidden
      content:
        application/problem+json:
          schema: { $ref: '#/components/schemas/Problem' }
    NotFound:
      description: Not found
      content:
        application/problem+json:
          schema: { $ref: '#/components/schemas/Problem' }
    TooManyRequests:
      description: Rate limit exceeded
      headers:
        Retry-After:
          schema: { type: integer }
      content:
        application/problem+json:
          schema: { $ref: '#/components/schemas/Problem' }
  schemas:
    Pagination:
      type: object
      properties:
        current_page: { type: integer }
        per_page: { type: integer }
        total: { type: integer }
    Problem:
      type: object
      properties:
        type: { type: string, format: uri }
        title: { type: string }
        status: { type: integer }
        detail: { type: string }
        instance: { type: string }
        errors:
          type: object
          description: Valideringsfejl pr. felt
          additionalProperties:
            type: array
            items: { type: string }
    Project:
      type: object
      properties:
        id: { type: string, format: uuid }
        org_id: { type: string, format: uuid }
        template_id: { type: string, format: uuid }
        status: { type: string, enum: [active, archived] }
        code: { type: string }
    Contract:
      type: object
      properties:
        id: { type: string, format: uuid }
        offer_id: { type: string, format: uuid }
        signed_at: { type: string, format: date-time, nullable: true }
    Document:
      type: object
      properties:
        id: { type: string, format: uuid }
        project_id: { type: string, format: uuid }
        code: { type: string }
        title: { type: string }
        discipline: { type: string }
        phase_code: { type: string }
        classification: { type: object, additionalProperties: true }
        status:
          type: string
          enum: [draft, for_review, approved_to_publish, published, superseded]
        current_version_id: { type: string, format: uuid, nullable: true }
        updated_at: { type: string, format: date-time }
    DocumentCreate:
      type: object
      required: [project_id, code, title]
      properties:
        project_id: { type: string, format: uuid }
        code: { type: string }
        title: { type: string }
        discipline: { type: string }
        phase_code: { type: string }
        classification: { type: object, additionalProperties: true }
    DocumentVersion:
      type: object
      properties:
        id: { type: string, format: uuid }
        document_id: { type: string, format: uuid }
        rev: { type: string }
        version: { type: string }
        storage_key: { type: string }
        mime: { type: string }
        size: { type: integer }
        approved_at: { type: string, format: date-time, nullable: true }

paths:
  /api/v1/documents:
    get:
      operationId: listDocuments
      summary: List documents
      parameters:
        - $ref: '#/components/parameters/ProjectId'
        - $ref: '#/components/parameters/Status'
        - $ref: '#/components/parameters/Q'
        - $ref: '#/components/parameters/Page'
        - $ref: '#/components/parameters/PerPage'
        - $ref: '#/components/parameters/Sort'
        - $ref: '#/components/parameters/DateFrom'
        - $ref: '#/components/parameters/DateTo'
      responses:
        '200':
          description: OK
          headers:
            RateLimit-Limit: { $ref: '#/components/headers/RateLimit-Limit' }
            RateLimit-Remaining: { $ref: '#/components/headers/RateLimit-Remaining' }
            RateLimit-Reset: { $ref: '#/components/headers/RateLimit-Reset' }
            ETag: { $ref: '#/components/headers/ETag' }
          content:
            application/json:
              examples:
                ok:
                  value:
                    data:
                      - { id: 'uuid-1', code: 'A-001', title: 'Situationsplan', status: 'approved_to_publish' }
                    meta: { current_page: 1, per_page: 25, total: 120 }
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items: { $ref: '#/components/schemas/Document' }
                  meta: { $ref: '#/components/schemas/Pagination' }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '429': { $ref: '#/components/responses/TooManyRequests' }
    post:
      operationId: createDocument
      summary: Create document (metadata)
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/DocumentCreate' }
            examples:
              create:
                value: { project_id: 'uuid', code: 'A-010', title: 'Facader', discipline: 'A', phase_code: 'A4' }
      parameters:
        - in: header
          name: Idempotency-Key
          required: false
          schema: { type: string }
          description: Unik nøgle for at gøre POST idempotent
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Document' }
        '400': { $ref: '#/components/responses/Problem' }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '422': { $ref: '#/components/responses/Problem' }

  /api/v1/documents/{id}:
    get:
      operationId: getDocument
      summary: Get document by id
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: OK
          headers:
            ETag: { $ref: '#/components/headers/ETag' }
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Document' }
        '304': { description: Not Modified }
        '404': { $ref: '#/components/responses/NotFound' }

  /api/v1/documents/{id}/versions:
    post:
      operationId: initVersionUpload
      summary: Init new version upload (presigned URL)
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
        - in: header
          name: Idempotency-Key
          required: false
          schema: { type: string }
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [mime, size]
              properties:
                mime: { type: string }
                size: { type: integer, minimum: 1 }
            examples:
              init:
                value: { mime: 'application/pdf', size: 1048576 }
      responses:
        '200':
          description: Upload details
          content:
            application/json:
              schema:
                type: object
                properties:
                  upload:
                    type: object
                    properties:
                      url: { type: string, format: uri }
                      fields: { type: object, additionalProperties: true }
                  version: { $ref: '#/components/schemas/DocumentVersion' }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '409': { $ref: '#/components/responses/Problem' }

  /api/v1/document-versions/{id}/approve:
    post:
      operationId: approveVersion
      summary: Approve a version
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: Approved
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Document' }
              examples:
                ok:
                  value: { id: 'uuid', status: 'approved_to_publish', current_version_id: 'uuid-v' }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '403': { $ref: '#/components/responses/Forbidden' }
        '409': { $ref: '#/components/responses/Problem' }

  /api/v1/transmittals:
    get:
      operationId: listTransmittals
      summary: List transmittals
      parameters:
        - $ref: '#/components/parameters/ProjectId'
        - $ref: '#/components/parameters/Page'
        - $ref: '#/components/parameters/PerPage'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data: { type: array, items: { $ref: '#/components/schemas/Transmittal' } }
                  meta: { $ref: '#/components/schemas/Pagination' }
    post:
      operationId: createTransmittal
      summary: Create transmittal
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/TransmittalCreate' }
      responses:
        '201': { description: Created }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '422': { $ref: '#/components/responses/Problem' }

  /api/v1/projects:
    get:
      operationId: listProjects
      summary: List projects
      parameters:
        - $ref: '#/components/parameters/Page'
        - $ref: '#/components/parameters/PerPage'
        - $ref: '#/components/parameters/Sort'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data: { type: array, items: { $ref: '#/components/schemas/Project' } }
                  meta: { $ref: '#/components/schemas/Pagination' }

  /api/v1/projects/{id}/gates/{gateId}/request-review:
    post:
      operationId: requestGateReview
      summary: Request gate review/decision
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
        - in: path
          name: gateId
          required: true
          schema: { type: string, format: uuid }
      responses:
        '200':
          description: Decision
          content:
            application/json:
              schema:
                type: object
                properties:
                  result: { type: string, enum: [passed, rejected] }
                  decided_at: { type: string, format: date-time }
                  reasons: { type: array, items: { type: string } }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '422': { $ref: '#/components/responses/Problem' }

  /api/v1/offers/{id}/accept:
    post:
      operationId: acceptOffer
      summary: Accept offer → create contract and project
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: string, format: uuid }
        - in: header
          name: Idempotency-Key
          required: false
          schema: { type: string }
      responses:
        '200':
          description: Accepted
          content:
            application/json:
              schema:
                type: object
                properties:
                  contract: { $ref: '#/components/schemas/Contract' }
                  project: { $ref: '#/components/schemas/Project' }
        '401': { $ref: '#/components/responses/Unauthorized' }
        '409': { $ref: '#/components/responses/Problem' }

webhooks:
  document.version.approved:
    post:
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                event: { type: string, enum: [document.version.approved] }
                id: { type: string }
                occurred_at: { type: string, format: date-time }
                tenant_id: { type: string }
                data:
                  type: object
                  properties:
                    document_id: { type: string, format: uuid }
                    version_id: { type: string, format: uuid }
                    approved_by: { type: string, format: uuid }
      headers:
        X-ModusBuild-Signature:
          schema: { type: string }
          description: HMAC-SHA256 signatur af request body (hex)

---

## 40. Postman Collection v2.1 (auto-afledt eksempel)
> Importér JSON’en i Postman/Insomnia. Sæt `{{baseUrl}}` og `{{token}}` i miljøvariabler. Collections bør genereres fra OpenAPI i praksis – dette er en startpakke.

```json
{
  "info": {"name": "ModusBuild v1 (MVP)", "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"},
  "item": [
    {
      "name": "Documents / List",
      "request": {
        "method": "GET",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"} ],
        "url": {"raw": "{{baseUrl}}/api/v1/documents?project_id={{project_id}}&page=1&per_page=25", "host": ["{{baseUrl}}"], "path": ["api","v1","documents"], "query": [
          {"key":"project_id","value":"{{project_id}}"},{"key":"page","value":"1"},{"key":"per_page","value":"25"}
        ]}
      }
    },
    {
      "name": "Documents / Create",
      "event": [{
        "listen":"prerequest","script":{"exec":["pm.variables.set('idem', Date.now()+'-'+Math.random().toString(36).slice(2));"],"type":"text/javascript"}
      }],
      "request": {
        "method": "POST",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"}, {"key":"Idempotency-Key","value":"{{idem}}"} ],
        "body": {"mode":"raw","raw":"{
  \"project_id\": \"{{project_id}}\",\n  \"code\": \"A-010\",\n  \"title\": \"Facader\",\n  \"discipline\": \"A\",\n  \"phase_code\": \"A4\"\n}"},
        "url": {"raw": "{{baseUrl}}/api/v1/documents", "host": ["{{baseUrl}}"], "path": ["api","v1","documents"]}
      }
    },
    {
      "name": "Document Versions / Init Upload",
      "event": [{
        "listen":"prerequest","script":{"exec":["pm.variables.set('idem', Date.now()+'-'+Math.random().toString(36).slice(2));"],"type":"text/javascript"}
      }],
      "request": {
        "method": "POST",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"}, {"key":"Idempotency-Key","value":"{{idem}}"} ],
        "body": {"mode":"raw","raw":"{
  \"mime\": \"application/pdf\",\n  \"size\": 1048576\n}"},
        "url": {"raw": "{{baseUrl}}/api/v1/documents/{{document_id}}/versions", "host": ["{{baseUrl}}"], "path": ["api","v1","documents","{{document_id}}","versions"]}
      }
    },
    {
      "name": "Document Versions / Approve",
      "request": {
        "method": "POST",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"} ],
        "url": {"raw": "{{baseUrl}}/api/v1/document-versions/{{version_id}}/approve", "host": ["{{baseUrl}}"], "path": ["api","v1","document-versions","{{version_id}}","approve"]}
      }
    },
    {
      "name": "Projects / Gates / Request review",
      "request": {
        "method": "POST",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"} ],
        "url": {"raw": "{{baseUrl}}/api/v1/projects/{{project_id}}/gates/{{gate_id}}/request-review", "host": ["{{baseUrl}}"], "path": ["api","v1","projects","{{project_id}}","gates","{{gate_id}}","request-review"]}
      }
    },
    {
      "name": "Offers / Accept",
      "event": [{
        "listen":"prerequest","script":{"exec":["pm.variables.set('idem', Date.now()+'-'+Math.random().toString(36).slice(2));"],"type":"text/javascript"}
      }],
      "request": {
        "method": "POST",
        "header": [ {"key": "Authorization", "value": "Bearer {{token}}"}, {"key":"Idempotency-Key","value":"{{idem}}"} ],
        "url": {"raw": "{{baseUrl}}/api/v1/offers/{{offer_id}}/accept", "host": ["{{baseUrl}}"], "path": ["api","v1","offers","{{offer_id}}","accept"]}
      }
    }
  ],
  "variable": [
    {"key": "baseUrl", "value": "https://api.modusbuild.local"},
    {"key": "token", "value": ""},
    {"key": "project_id", "value": ""},
    {"key": "document_id", "value": ""},
    {"key": "version_id", "value": ""},
    {"key": "gate_id", "value": ""},
    {"key": "offer_id", "value": ""}
  ]
}
```

Brug:
1.Importér JSON’en i Postman → vælg/opsæt miljø (baseUrl, token, ids).
2.Kald Documents / Create → brug svaret til at sætte {{document_id}}.
3.Kald Document Versions / Init Upload → upload til upload.url i S3.
4.Kald Document Versions / Approve.
5.Kald Projects / Gates / Request review for at passere gate, når kriterier er opfyldt.

⸻

41. QC: OpenAPI 0.2.1 – præciseringer (delta)

Formål: Skærpe cache‑semantik, eksempler og fejlmodeller uden at genskrive hele §39.

41.1 Globale parametre

Tilføj denne header‑parameter til components.parameters:

IfNoneMatch:
  in: header
  name: If-None-Match
  schema: { type: string }
  description: Returnér 304 Not Modified hvis ETag matcher

41.2 Dokumentliste (GET /api/v1/documents)

Patch til §39:
•Tilføj parameteren IfNoneMatch under parameters.
•Tilføj response '304': { description: Not Modified }.
•Bevar RateLimit/ETag headers på 200.

YAML‑patch:

  /api/v1/documents:
    get:
      parameters:
        - $ref: '#/components/parameters/IfNoneMatch'
      responses:
        '304': { description: Not Modified }

41.3 Fejleksempler (Problem Details)

Tilføj repræsentative examples under relevante responses:

components:
  responses:
    Problem:
      content:
        application/problem+json:
          examples:
            validation:
              value:
                type: "https://modusbuild/errors/validation"
                title: "Unprocessable Entity"
                status: 422
                detail: "Feltvalidering fejlede"
                errors: { code: ["code must be unique within project"] }
            conflict:
              value:
                type: "https://modusbuild/errors/conflict"
                title: "Conflict"
                status: 409
                detail: "Version cannot be approved from current state"

41.4 Eksempel på 409 ved approve

Patch POST /api/v1/document-versions/{id}/approve med 409 example:

  /api/v1/document-versions/{id}/approve:
    post:
      responses:
        '409':
          content:
            application/problem+json:
              examples:
                not_allowed:
                  value:
                    type: "https://modusbuild/errors/conflict"
                    title: "Conflict"
                    status: 409
                    detail: "Only latest version can be approved"

⸻

42. ETag & If-None-Match – API‑semantik
•ETag genereres for alle GET‑responses (enkeltressourcer og lister).
•Klient kan sende If-None-Match for at få 304 ved uændrede data (båndbredde og latency ned).
•Hash‑grundlag: stable JSON‑serialisering af response body eller updated_at‑maks + total count for lister.
•Caching må ikke lække across tenants; ETag indeholder implicit tenant i hash‑input.

⸻

43. Databaseskema – constraints & guards (migrations)

Formål: Fange fejl tidligt i DB‑laget og lette politikker i kode.

43.1 Statusfelter (CHECK)

Document.status:

$table->string('status');
DB::statement("ALTER TABLE documents ADD CONSTRAINT documents_status_chk CHECK (status IN ('draft','for_review','approved_to_publish','published','superseded'))");

Gate.status:

$table->string('status');
DB::statement("ALTER TABLE gates ADD CONSTRAINT gates_status_chk CHECK (status IN ('open','passed','rejected'))");

Offer.status:

$table->string('status');
DB::statement("ALTER TABLE offers ADD CONSTRAINT offers_status_chk CHECK (status IN ('draft','sent','accepted','rejected'))");

43.2 Unikheder & referentielle regler
•documents: UQ(project_id, code) for at sikre projektspecifik unik kode.
•document_versions: UQ(document_id, rev, version) for at undgå duplikater.
•gates: UQ(phase_id) da hver fase har præcis én gate i MVP.
•ON DELETE: document_versions.document_id → CASCADE; documents.project_id → RESTRICT (undgå slet projekt med aktive docs i MVP).

43.3 Concurrency (optimistic locking)
•documents.locked_version INT NOT NULL DEFAULT 0.
•Controllers anvender If-Match (senere) eller DB‑check: opdatering med WHERE locked_version = :expected og inkrement ved succes.

⸻

44. Publicering (published) – præcis forretningsregel

Mål: Skelne mellem “godkendt til udgivelse” og “faktisk publiceret”.
1.Approved ≠ Published. approved_to_publish er kvalitetsstatus; publicering er en distributionshandling.
2.Standardregel (MVP): Et dokument bliver published kun når det indgår i en Transmittal, der sendes (transmittals.sent_at IS NOT NULL).
3.Konsekvenser:
•SetCurrentIfApprovedJob sætter kun current_version_id ved approve – ændrer ikke documents.status til published.
•Når en Transmittal sendes, kører MarkPublishedOnTransmittalSentJob og opdaterer alle reference‑dokumenter fra approved_to_publish → published.
•published dokumenter kan stadig blive superseded ved ny godkendt version.
4.Event: transmittal.sent (payload: { transmittal_id, project_id, document_ids[], sent_at }).
5.OpenAPI: POST /api/v1/transmittals returnerer 201 og sætter sent_at (nul hvis kladde). PATCH /api/v1/transmittals/{id}/send (kan tilføjes i P2) udløser send + event.
6.Policies: Kun roller Owner/Lead kan sende transmittals.

Acceptance update (§17):
•“Et dokument skifter til published når (og kun når) det er inkluderet i en sendt Transmittal.”

⸻

45. Eksempel‑migrations (Laravel) – uddrag

// documents
Schema::create('documents', function (Blueprint $t) {
  $t->uuid('id')->primary();
  $t->uuid('project_id');
  $t->string('code');
  $t->string('title');
  $t->string('discipline')->nullable();
  $t->string('phase_code')->nullable();
  $t->jsonb('classification_jsonb')->nullable();
  $t->string('status')->default('draft');
  $t->uuid('current_version_id')->nullable();
  $t->integer('locked_version')->default(0);
  $t->timestampsTz();
  $t->unique(['project_id','code']);
  $t->index(['project_id','status']);
});
DB::statement("ALTER TABLE documents ADD CONSTRAINT documents_status_chk CHECK (status IN ('draft','for_review','approved_to_publish','published','superseded'))");

// document_versions
Schema::create('document_versions', function (Blueprint $t) {
  $t->uuid('id')->primary();
  $t->uuid('document_id');
  $t->string('rev',8)->default('A');
  $t->string('version',8)->default('1.0');
  $t->string('storage_key')->unique();
  $t->string('checksum')->nullable();
  $t->bigInteger('size')->nullable();
  $t->string('mime',128)->nullable();
  $t->timestampTz('approved_at')->nullable();
  $t->uuid('approved_by')->nullable();
  $t->timestampsTz();
  $t->unique(['document_id','rev','version']);
  $t->index(['document_id']);
});

// gates (unik phase)
Schema::table('gates', function (Blueprint $t) {
  $t->unique('phase_id');
});

⸻

46. QC‑tjek: konsistensliste (gennemført)
•Statusværdier i snake_case overalt.
•Gate‑afslag via 200/result: rejected + reasons.
•Unikheder: documents(project_id,code), document_versions(document_id,rev,version), gates(phase_id).
•Upload‑pipeline jobnavne afklaret.
•ETag/If-None-Match specificeret for lister og entiteter.
•Problem Details med eksempler (422/409).
•Publicering koblet til Transmittal‑send (ikke approve).

⸻

47. Prism Mock – setup & brug (kontrakt‑drevet fake API)

Mål: Køre en lokal mock af OpenAPI.yaml (rev 0.2.1) så frontend/QA kan bygge og teste uden backend.

47.1 Installation

# i projektets rod (hvor openapi.yaml ligger)
npm init -y
npm i -D @stoplight/prism-cli

Tilføj scripts i package.json:

{
  "scripts": {
    "mock:api": "prism mock openapi.yaml -p 4010",
    "mock:api:dynamic": "prism mock openapi.yaml -p 4010 -d"
  }
}

•mock:api svarer med faste eksempler fra kontrakten.
•mock:api:dynamic genererer realistiske værdier (randomiseret) ud fra skemaer.

Kør:

npm run mock:api
# Server kører på http://localhost:4010

47.2 Hurtigtest (cURL)

Husk Authorization header (Bearer), da kontrakten kræver det.

# List documents
curl -s \
  -H "Authorization: Bearer test-token" \
  "http://localhost:4010/api/v1/documents?project_id=11111111-1111-1111-1111-111111111111&page=1&per_page=10"

# Create document (idempotent POST med nøgle)
curl -s -X POST \
  -H "Authorization: Bearer test-token" \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: demo-$(date +%s)" \
  -d '{"project_id":"11111111-1111-1111-1111-111111111111","code":"A-010","title":"Facader","discipline":"A","phase_code":"A4"}' \
  http://localhost:4010/api/v1/documents

# Init upload for version
curl -s -X POST \
  -H "Authorization: Bearer test-token" \
  -H "Content-Type: application/json" \
  -d '{"mime":"application/pdf","size":1048576}' \
  http://localhost:4010/api/v1/documents/22222222-2222-2222-2222-222222222222/versions

# Approve version
curl -s -X POST \
  -H "Authorization: Bearer test-token" \
  http://localhost:4010/api/v1/document-versions/33333333-3333-3333-3333-333333333333/approve

# Request gate review
curl -s -X POST \
  -H "Authorization: Bearer test-token" \
  http://localhost:4010/api/v1/projects/44444444-4444-4444-4444-444444444444/gates/55555555-5555-5555-5555-555555555555/request-review

47.3 Vite proxy (frontend uden CORS‑problemer)

I vite.config.ts:

import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [laravel({ input: ['resources/js/app.js'], refresh: true })],
  server: {
    proxy: {
      '/api': 'http://localhost:4010'
    }
  }
})

•Frontend kan nu kalde /api/v1/... direkte; dev‑server videresender til Prism.

47.4 Tips
•Static vs dynamic mock: Brug mock:api til stabile snaps og mock:api:dynamic når du vil strese UI’et med varierende data.
•ETag/304: Klient kan sende If-None-Match som i kontrakten; Prism svarer 304 når muligt i forhold til examples.
•Idempotency-Key: God vane at sende på POST i dev også (matches produktion).
•Webhooks: Mockes separat (fx med en lille local server eller RequestBin) – Prism mocker ikke udgående kald.

47.5 Næste skridt
•Generér Postman/Insomnia direkte fra openapi.yaml, peg {{baseUrl}} på http://localhost:4010.
•Når UI‑skeletter er klar: begynd at erstatte Prism‑kald med rigtige endpoints i backend branch, men behold mock til hurtige UI‑tests.

