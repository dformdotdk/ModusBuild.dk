<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ModusBuild · Cockpit prototype</title>
    @vite(['resources/js/app.ts'])
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased">
    @php
        $projects = [
            [
                'code' => 'PRJ-2025-001',
                'name' => 'Rækkehuse · Fase A4',
                'status' => 'On track',
                'phase' => 'A4 · Forprojektering',
                'progress' => 62,
                'owner' => 'Bygherre A/S',
            ],
            [
                'code' => 'PRJ-2025-002',
                'name' => 'Nyt domicil · Design review',
                'status' => 'Gate review',
                'phase' => 'A5 · Myndighed',
                'progress' => 38,
                'owner' => 'Vision Architects',
            ],
            [
                'code' => 'PRJ-2024-019',
                'name' => 'Renovering · Udbud',
                'status' => 'Attention',
                'phase' => 'B1 · Udbud',
                'progress' => 81,
                'owner' => 'Ejendomsselskab Nord',
            ],
        ];

        $summary = [
            ['label' => 'Aktive projekter', 'value' => 12, 'delta' => '+2 siden sidste uge'],
            ['label' => 'Dokumenter klar til gate', 'value' => 42, 'delta' => '6 mangler review'],
            ['label' => 'Åbne workflows', 'value' => 5, 'delta' => '3 i kritiske faser'],
            ['label' => 'Transmittals denne uge', 'value' => 4, 'delta' => '2 til bygherre'],
        ];

        $gateChecklist = [
            ['title' => 'Dokumenter godkendt', 'value' => '12 / 12', 'state' => 'done', 'hint' => 'Alle filer er approved_to_publish'],
            ['title' => 'Ekstern godkendelse', 'value' => 'Afventer', 'state' => 'pending', 'hint' => 'Ekstern reviewer mangler at signere'],
            ['title' => 'Workflow trin åbne', 'value' => '1 aktivitet', 'state' => 'warning', 'hint' => 'Ingeniør-teamet har 1 åben handling'],
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

    <div class="relative isolate min-h-full overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.18),_transparent_55%)]"></div>
        <div class="absolute inset-y-0 right-0 -z-10 hidden w-1/2 bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.16),_transparent_60%)] lg:block"></div>

        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/20 text-lg font-semibold text-indigo-300 ring-1 ring-inset ring-indigo-500/40">MB</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-indigo-300/80">ModusBuild prototype</p>
                        <h1 class="mt-1 text-2xl font-semibold text-white">Projektcockpit</h1>
                    </div>
                </div>
                <div class="hidden items-center gap-4 md:flex">
                    <button class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-slate-100 transition hover:border-white/30 hover:bg-white/10">
                        Skift projekt
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-3 text-left">
                        <div class="leading-tight">
                            <span class="block text-sm font-semibold text-white">Sofie Holm</span>
                            <span class="block text-xs text-slate-300">Lead Engineer</span>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500 text-sm font-semibold text-white">SH</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-16 pt-10 lg:px-8 lg:pt-12">
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($summary as $item)
                    <article class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.8)] transition hover:border-indigo-300/40 hover:bg-white/10">
                        <div class="flex items-baseline justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">{{ $item['label'] }}</p>
                            <span class="text-[11px] font-medium text-indigo-300">{{ $item['delta'] }}</span>
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $item['value'] }}</p>
                        <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-indigo-400/70 via-sky-400/70 to-indigo-500/70 opacity-0 transition group-hover:opacity-100"></div>
                    </article>
                @endforeach
            </section>

            <section class="mt-10 grid gap-6 xl:grid-cols-3">
                <div class="space-y-6 xl:col-span-2">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-7 shadow-lg shadow-indigo-950/40">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Gate readiness · Fase A5</h2>
                                <p class="mt-1 text-sm text-slate-300">Status baseret på <span class="font-semibold text-indigo-200">GateService::validate</span> og regler fra RFC-001 §8.2.</p>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-400/10 px-3 py-1 text-xs font-semibold text-amber-200">
                                <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                                Afventer ekstern godkendelse
                            </span>
                        </div>
                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            @foreach ($gateChecklist as $item)
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="text-slate-200">{{ $item['title'] }}</span>
                                        <span class="text-indigo-200">{{ $item['value'] }}</span>
                                    </div>
                                    <p class="mt-2 text-[13px] text-slate-400">{{ $item['hint'] }}</p>
                                    <div class="mt-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wide">
                                        @php
                                            $states = [
                                                'done' => ['label' => 'Klar', 'dot' => 'bg-emerald-400', 'text' => 'text-emerald-200', 'border' => 'border-emerald-400/30'],
                                                'warning' => ['label' => 'Handling kræves', 'dot' => 'bg-amber-300', 'text' => 'text-amber-200', 'border' => 'border-amber-300/30'],
                                                'pending' => ['label' => 'Afventer', 'dot' => 'bg-slate-400', 'text' => 'text-slate-200', 'border' => 'border-slate-400/30'],
                                            ];
                                            $meta = $states[$item['state']];
                                        @endphp
                                        <span class="h-2.5 w-2.5 rounded-full {{ $meta['dot'] }}"></span>
                                        <span class="rounded-full border px-2 py-0.5 {{ $meta['border'] }} {{ $meta['text'] }}">{{ $meta['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-sm text-slate-400">Sidst evalueret <span class="font-semibold text-white">for 8 minutter siden</span></div>
                            <button class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-400 via-indigo-500 to-sky-400 px-4 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-indigo-500/30 transition hover:from-indigo-300 hover:via-indigo-400 hover:to-sky-300">
                                Start gate review
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 1 1-1.414-1.414L13.586 10 10.293 6.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-7 shadow-lg shadow-indigo-950/40">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Dokumenter i fokus</h2>
                                <p class="mt-1 text-sm text-slate-300">Direkte koblet til statusmaskinen fra RFC-001 §20.</p>
                            </div>
                            <a href="#" class="text-sm font-semibold text-indigo-200 transition hover:text-indigo-100">Åbn dokumentliste</a>
                        </div>
                        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10">
                            <table class="min-w-full divide-y divide-white/10 text-sm">
                                <thead class="bg-white/5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3">Kode</th>
                                        <th class="px-4 py-3">Titel</th>
                                        <th class="px-4 py-3">Disciplin</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Opdateret</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-slate-200">
                                    @php
                                        $statusColours = [
                                            'Approved' => 'bg-emerald-400/15 text-emerald-200 ring-1 ring-inset ring-emerald-300/30',
                                            'For review' => 'bg-amber-400/15 text-amber-200 ring-1 ring-inset ring-amber-300/30',
                                            'Draft' => 'bg-slate-400/10 text-slate-200 ring-1 ring-inset ring-slate-300/20',
                                            'Changes requested' => 'bg-rose-400/15 text-rose-200 ring-1 ring-inset ring-rose-300/30',
                                        ];
                                    @endphp
                                    @foreach ($documents as $doc)
                                        <tr class="transition hover:bg-white/5">
                                            <td class="px-4 py-3 font-semibold text-white">{{ $doc['code'] }}</td>
                                            <td class="px-4 py-3 text-slate-300">{{ $doc['title'] }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full bg-white/5 px-2.5 py-1 text-[11px] font-semibold text-slate-200">{{ $doc['discipline'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusColours[$doc['status']] ?? 'bg-slate-400/10 text-slate-200' }}">{{ $doc['status'] }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right text-slate-400">{{ $doc['updated'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-500/20 via-indigo-500/10 to-slate-900/70 p-7 shadow-lg shadow-indigo-950/30">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Projektpipeline</h2>
                                <p class="mt-1 text-sm text-slate-300">Snapshot af aktive projekter og gate-status.</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold text-indigo-100">Data fra RFC-001 §7</span>
                        </div>
                        <div class="mt-6 grid gap-4 md:grid-cols-3">
                            @foreach ($projects as $project)
                                <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wide text-slate-300">
                                        <span>{{ $project['code'] }}</span>
                                        <span class="text-indigo-200">{{ $project['status'] }}</span>
                                    </div>
                                    <h3 class="mt-2 text-sm font-semibold text-white">{{ $project['name'] }}</h3>
                                    <p class="text-xs text-slate-400">{{ $project['phase'] }}</p>
                                    <p class="mt-2 text-[11px] text-slate-400">Ansvarlig: <span class="text-slate-200">{{ $project['owner'] }}</span></p>
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                                            <span>Fremdrift</span>
                                            <span>{{ $project['progress'] }}%</span>
                                        </div>
                                        <div class="mt-2 h-2 rounded-full bg-white/10">
                                            <span class="block h-full rounded-full bg-gradient-to-r from-indigo-400 via-sky-400 to-indigo-500" style="width: {{ $project['progress'] }}%"></span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-lg shadow-indigo-950/30">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Aktiviteter</h2>
                                <p class="mt-1 text-sm text-slate-300">Audit trail inspireret af RFC-001 §5 og §24.</p>
                            </div>
                            <button class="text-xs font-semibold text-indigo-200 hover:text-indigo-100">Eksportér</button>
                        </div>
                        <ol class="mt-5 space-y-4 text-sm">
                            @foreach ($activityFeed as $event)
                                @php
                                    $chips = [
                                        'success' => 'bg-emerald-400/10 text-emerald-200',
                                        'warning' => 'bg-amber-400/10 text-amber-200',
                                        'info' => 'bg-sky-400/10 text-sky-200',
                                        'default' => 'bg-slate-400/10 text-slate-200',
                                    ];
                                @endphp
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-2.5 w-2.5 rounded-full @class([
                                        'bg-emerald-300' => $event['type'] === 'success',
                                        'bg-amber-300' => $event['type'] === 'warning',
                                        'bg-sky-300' => $event['type'] === 'info',
                                        'bg-slate-400' => $event['type'] === 'default',
                                    ])"></span>
                                    <div class="flex-1">
                                        <p class="text-slate-200"><span class="font-semibold text-white">{{ $event['actor'] }}</span> {{ $event['action'] }}</p>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                            <span>{{ $event['time'] }}</span>
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $chips[$event['type']] }}">
                                                <span>Event</span>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-500/30 via-indigo-500/15 to-slate-950 p-6 shadow-lg shadow-indigo-950/40">
                        <h2 class="text-lg font-semibold text-white">Næste skridt</h2>
                        <p class="mt-2 text-sm text-slate-300">Plan for at føre prototypen videre til produktionsklar oplevelse.</p>
                        <ul class="mt-4 space-y-3 text-sm text-slate-200">
                            <li class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                                <span>Tilslut Inertia + API v1 for live-data og rettighedsstyring.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                                <span>Udbyg dokumentliste med filtrering, søgning og inline diff.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                                <span>Implementér transmittal-flow, events og audit dashboards.</span>
                            </li>
                        </ul>
                        <button class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-white/30 hover:bg-white/10">
                            Se roadmap
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M3 9a1 1 0 0 1 1-1h9.586l-3.293-3.293a1 1 0 0 1 1.414-1.414l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 1 1-1.414-1.414L13.586 11H4a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </aside>
            </section>
        </main>
    </div>
</body>
</html>
