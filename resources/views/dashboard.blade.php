<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ModusBuild · Project cockpit (prototype)</title>
    @vite(['resources/js/app.ts'])
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased">
    @php
        $projects = [
            ['code' => 'PRJ-2025-001', 'name' => 'Rækkehuse – Fase A4', 'status' => 'Active', 'phase' => 'A4 · Forprojektering', 'progress' => 62],
            ['code' => 'PRJ-2025-002', 'name' => 'Nyt domicil – Design review', 'status' => 'Gate review', 'phase' => 'A5 · Myndighed', 'progress' => 38],
            ['code' => 'PRJ-2024-019', 'name' => 'Renovering – Udbud', 'status' => 'On track', 'phase' => 'B1 · Udbud', 'progress' => 81],
        ];

        $gateChecklist = [
            ['title' => 'Dokumenter godkendt', 'value' => '12 / 12', 'state' => 'done'],
            ['title' => 'Ekstern godkendelse', 'value' => 'Ventende', 'state' => 'pending'],
            ['title' => 'Workflow trin åbne', 'value' => '1 aktivitet', 'state' => 'warning'],
        ];

        $documents = [
            ['code' => 'A-010', 'title' => 'Facader', 'discipline' => 'A', 'status' => 'For review', 'updated' => '3 timer siden'],
            ['code' => 'S-021', 'title' => 'Statik – dæk', 'discipline' => 'S', 'status' => 'Approved', 'updated' => 'I går 14:12'],
            ['code' => 'V-004', 'title' => 'VVS oversigt', 'discipline' => 'V', 'status' => 'Changes requested', 'updated' => 'I går 09:31'],
            ['code' => 'E-112', 'title' => 'El schema – plan 2', 'discipline' => 'E', 'status' => 'Draft', 'updated' => '4 dage siden'],
        ];

        $activityFeed = [
            ['time' => '10:24', 'actor' => 'Julie Madsen', 'action' => 'godkendte version 1.1 af “Statik – dæk”', 'type' => 'success'],
            ['time' => '09:10', 'actor' => 'Ekstern reviewer', 'action' => 'anmodede om ændringer i “VVS oversigt”', 'type' => 'warning'],
            ['time' => 'I går', 'actor' => 'System', 'action' => 'sendte transmittal TR-045 til Bygherre', 'type' => 'info'],
            ['time' => 'I går', 'actor' => 'Martin Feldt', 'action' => 'startede gate review for Fase A5', 'type' => 'default'],
        ];
    @endphp

    <div class="min-h-full">
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-6 py-4 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">ModusBuild prototype</p>
                        <h1 class="mt-1 text-2xl font-semibold text-slate-900">Projektcockpit</h1>
                        <p class="mt-1 text-sm text-slate-500">Visualiseret ud fra RFC-001: gate readiness, dokumentstatus og seneste aktiviteter.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Sandbox environment
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-right">
                                <span class="block text-sm font-medium">Sofie Holm</span>
                                <span class="block text-xs text-slate-500">Lead Engineer</span>
                            </span>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">SH</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-7xl gap-6 px-6 py-8 lg:flex lg:px-8">
            <aside class="mb-6 w-full shrink-0 lg:mb-0 lg:w-64">
                <nav class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Navigation</h2>
                    <ul class="mt-3 space-y-1 text-sm">
                        <li>
                            <a href="#" class="flex items-center justify-between rounded-xl bg-indigo-50 px-3 py-2 font-medium text-indigo-700">
                                <span class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                    Oversigt
                                </span>
                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold">Aktiv</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-600 transition hover:bg-slate-100">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                Projekter
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-600 transition hover:bg-slate-100">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                Dokumenter
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-600 transition hover:bg-slate-100">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                Godkendelser
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-600 transition hover:bg-slate-100">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                Transmittals
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-600 transition hover:bg-slate-100">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                Indstillinger
                            </a>
                        </li>
                    </ul>

                    <div class="mt-6 rounded-xl bg-slate-900 p-4 text-slate-100">
                        <p class="text-sm font-semibold">Gate-regel</p>
                        <p class="mt-2 text-xs text-slate-300">Alle dokumenter i fase skal være <span class="font-semibold text-white">approved_to_publish</span>, og ekstern godkendelse registreret før gate kan passeres.</p>
                        <button class="mt-3 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                            <span>Se regelsæt</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 1 1-1.414-1.414L13.586 10 10.293 6.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </nav>
            </aside>

            <main class="flex-1 space-y-6">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($projects as $project)
                        <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $project['code'] }}</p>
                                    <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $project['name'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $project['phase'] }}</p>
                                </div>
                                <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $project['status'] }}</span>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                                    <span>Fremdrift</span>
                                    <span>{{ $project['progress'] }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-100">
                                    <span class="block h-full rounded-full bg-indigo-500" style="width: {{ $project['progress'] }}%"></span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="grid gap-6 lg:grid-cols-5">
                    <div class="lg:col-span-3 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-900">Gate readiness</h2>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Fase A5</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">Status baseret på GateService::validate – kriterier fra RFC-001 §8.2.</p>
                            <ul class="mt-6 space-y-3">
                                @foreach ($gateChecklist as $item)
                                    <li class="flex items-start justify-between rounded-xl border px-4 py-3 @class([
                                        'border-emerald-200 bg-emerald-50 text-emerald-700' => $item['state'] === 'done',
                                        'border-amber-200 bg-amber-50 text-amber-700' => $item['state'] === 'warning',
                                        'border-slate-200 bg-slate-50 text-slate-600' => $item['state'] === 'pending',
                                    ])">
                                        <div>
                                            <p class="text-sm font-semibold">{{ $item['title'] }}</p>
                                            <p class="text-xs opacity-80">{{ $item['state'] === 'pending' ? 'Afventer ekstern handling' : 'Opdateret for 2 minutter siden' }}</p>
                                        </div>
                                        <span class="text-sm font-semibold">{{ $item['value'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <button class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                                Start gate review
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 1 1-1.414-1.414L13.586 10 10.293 6.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-900">Dokumenter i fokus</h2>
                                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Se alle</a>
                            </div>
                            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3">Kode</th>
                                            <th class="px-4 py-3">Titel</th>
                                            <th class="px-4 py-3">Disciplin</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 text-right">Opdateret</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-sm">
                                        @foreach ($documents as $doc)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 font-medium text-slate-900">{{ $doc['code'] }}</td>
                                                <td class="px-4 py-3 text-slate-600">{{ $doc['title'] }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $doc['discipline'] }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $statusColours = [
                                                            'Approved' => 'bg-emerald-100 text-emerald-700',
                                                            'For review' => 'bg-amber-100 text-amber-700',
                                                            'Draft' => 'bg-slate-100 text-slate-600',
                                                            'Changes requested' => 'bg-rose-100 text-rose-700',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColours[$doc['status']] ?? 'bg-slate-100 text-slate-600' }}">{{ $doc['status'] }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-right text-slate-500">{{ $doc['updated'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Aktiviteter</h2>
                            <p class="mt-1 text-sm text-slate-500">Audit- og eventfeed inspireret af RFC-001 §5 og §24.</p>
                            <ol class="mt-4 space-y-4 text-sm">
                                @foreach ($activityFeed as $event)
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full @class([
                                            'bg-emerald-500' => $event['type'] === 'success',
                                            'bg-amber-500' => $event['type'] === 'warning',
                                            'bg-indigo-500' => $event['type'] === 'info',
                                            'bg-slate-300' => $event['type'] === 'default',
                                        ])"></span>
                                        <div class="flex-1">
                                            <p><span class="font-semibold text-slate-900">{{ $event['actor'] }}</span> {{ $event['action'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $event['time'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-indigo-600 via-indigo-500 to-slate-900 p-6 text-indigo-50 shadow-sm">
                            <h2 class="text-lg font-semibold">Næste skridt</h2>
                            <p class="mt-2 text-sm text-indigo-100">Plan for prototype → produkt:</p>
                            <ul class="mt-3 space-y-2 text-sm">
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-300"></span>
                                    <span>Tilføj live-data via Inertia + API v1 endpoints.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-300"></span>
                                    <span>Udvid dokumentlisten med filtrering og søgning.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-300"></span>
                                    <span>Integrér gate beslutninger og transmittal events.</span>
                                </li>
                            </ul>
                            <button class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                Se roadmap
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M3 9a1 1 0 0 1 1-1h9.586l-3.293-3.293a1 1 0 0 1 1.414-1.414l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 1 1-1.414-1.414L13.586 11H4a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
