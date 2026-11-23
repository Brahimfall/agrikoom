<x-app-layout>
    {{-- ══════════════════════════════════════════════════════════════
         SUPER ADMIN DASHBOARD DYNAMIQUE — AgriKoom
         Composant interactif Alpine.js + Chart.js + API temps réel
    ══════════════════════════════════════════════════════════════ --}}

    <div x-data="adminDashboard({
            initialStats: {{ Js::from($initialStats) }},
            statsUrl: '{{ route('admin.dashboard.stats') }}'
         })"
         x-init="init()"
         class="bg-slate-50 min-h-screen py-8">

        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">

            {{-- ══════════════════ EN-TÊTE DU DASHBOARD ══════════════════ --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#7D5A50]/15 px-3 py-0.5 text-xs font-black uppercase tracking-wider text-[#7D5A50]">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#7D5A50] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#7D5A50]"></span>
                            </span>
                            Super Admin
                        </span>
                        <span class="text-xs text-slate-400 font-semibold">Tableau de bord dynamique</span>
                        <span class="hidden sm:inline-block rounded-md bg-white border border-slate-200 px-2 py-0.5 text-[11px] font-mono text-slate-500 shadow-2xs">
                            Actualisé à <span x-text="stats.updated_at">{{ $initialStats['updated_at'] }}</span>
                        </span>
                    </div>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                        Observatoire AgriKoom Sénégal
                    </h1>
                    <p class="mt-1 text-xs text-slate-500">
                        Mesure en temps réel des utilisateurs, intentions d'achat et clics vers les vendeurs.
                    </p>
                </div>

                {{-- Contrôles interactifs : Périodes & Rafraîchissement --}}
                <div class="flex flex-wrap items-center gap-2 self-start lg:self-center">
                    {{-- Bouton Rafraîchir --}}
                    <button type="button"
                            @click="refresh()"
                            :disabled="loading"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition active:scale-95 disabled:opacity-50">
                        <svg class="h-3.5 w-3.5 text-[#7D5A50]" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-text="loading ? 'Actualisation...' : 'Rafraîchir'">Rafraîchir</span>
                    </button>

                    {{-- Toggle Auto-refresh --}}
                    <button type="button"
                            @click="toggleAutoRefresh()"
                            :class="autoRefresh ? 'bg-emerald-50 text-emerald-800 border-emerald-300 ring-1 ring-emerald-300' : 'bg-white text-slate-600 border-slate-300'"
                            class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-bold shadow-xs transition">
                        <span class="h-2 w-2 rounded-full" :class="autoRefresh ? 'bg-emerald-500 animate-ping' : 'bg-slate-300'"></span>
                        <span x-text="autoRefresh ? 'En direct (15s)' : 'Auto-refresh off'">En direct</span>
                    </button>

                    {{-- Raccourcis Admin --}}
                    <a href="{{ route('listings.mine') }}" class="rounded-xl border border-[#7D5A50] bg-[#7D5A50] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#6b4d44] transition shadow-xs flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Annonces ({{ $metrics['listings_active'] }})</span>
                    </a>
                    <a href="{{ route('listings.create') }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        + Publier
                    </a>
                    <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : url('/admin/categories') }}" class="rounded-xl border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-3 py-2 text-xs font-bold text-[#5a3f38] hover:bg-[#7D5A50]/20 transition">
                        Catégories ({{ $metrics['categories_total'] }})
                    </a>
                    <a href="{{ route('admin.mercuriale.index') }}" class="rounded-xl border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-3 py-2 text-xs font-bold text-[#5a3f38] hover:bg-[#7D5A50]/20 transition">
                        Prix du marché ({{ $metrics['market_prices_active'] }})
                    </a>
                    <a href="{{ route('admin.articles.index') }}" class="rounded-xl border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-3 py-2 text-xs font-bold text-[#5a3f38] hover:bg-[#7D5A50]/20 transition">
                        Blog ({{ $metrics['articles_published'] }})
                    </a>
                </div>
            </div>

            {{-- ══════════════════ SÉLECTEUR DE PÉRIODE DYNAMIQUE ══════════════════ --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Période d'analyse :</span>
                        <span class="rounded bg-[#7D5A50]/10 px-2 py-0.5 text-[11px] font-black text-[#7D5A50]" x-text="periodLabel">Ce mois-ci</span>
                    </div>

                    {{-- Onglets interactifs : Changement instantané sans recharger la page --}}
                    <div class="flex flex-wrap items-center gap-1 rounded-xl bg-slate-100 p-1">
                        <button type="button"
                                @click="setPeriod('today')"
                                :class="period === 'today' ? 'bg-[#7D5A50] text-white shadow font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition duration-150">
                            ⚡ Aujourd'hui
                        </button>
                        <button type="button"
                                @click="setPeriod('week')"
                                :class="period === 'week' ? 'bg-[#7D5A50] text-white shadow font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition duration-150">
                            📅 7 derniers jours
                        </button>
                        <button type="button"
                                @click="setPeriod('month')"
                                :class="period === 'month' ? 'bg-[#7D5A50] text-white shadow font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition duration-150">
                            📆 Ce mois-ci
                        </button>
                        <button type="button"
                                @click="setPeriod('year')"
                                :class="period === 'year' ? 'bg-[#7D5A50] text-white shadow font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition duration-150">
                            📈 Cette année
                        </button>
                        <button type="button"
                                @click="setPeriod('all')"
                                :class="period === 'all' ? 'bg-[#7D5A50] text-white shadow font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition duration-150">
                            🏆 Tout l'historique
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══════════════════ BLOC 1 : CARTES KPIS DYNAMIQUES ══════════════════ --}}
            <div class="grid gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">

                {{-- KPI 1 : Clics & Intentions de Contact --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Clics vers vendeurs</span>
                        <span class="rounded-full bg-emerald-100 p-2 text-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-slate-950 tracking-tight"
                              x-text="formatNumber(stats.summary.contacts_total)">
                            {{ number_format($initialStats['summary']['contacts_total'], 0, ',', ' ') }}
                        </span>
                        <span class="text-xs text-emerald-700 font-bold">clics</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        <span class="font-bold text-slate-800" x-text="formatNumber(stats.summary.unique_people)">{{ $initialStats['summary']['unique_people'] }}</span>
                        personnes distinctes sur la période
                    </p>
                    <div class="mt-3 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-500" style="width: 85%"></div>
                    </div>
                </div>

                {{-- KPI 2 : WhatsApp Spécifique --}}
                <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50/50 to-white p-5 shadow-xs relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Canal WhatsApp</span>
                        <span class="rounded-full bg-[#25D366]/20 p-2 text-emerald-800">
                            <svg class="h-4 w-4 fill-emerald-700" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-slate-950 tracking-tight"
                              x-text="formatNumber(stats.channels.whatsapp.count)">
                            {{ number_format($initialStats['channels']['whatsapp']['count'], 0, ',', ' ') }}
                        </span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800"
                              x-text="stats.channels.whatsapp.percent + '%'">
                            {{ $initialStats['channels']['whatsapp']['percent'] }}%
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        Canal préféré pour négocier en direct
                    </p>
                    <div class="mt-3 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#25D366] transition-all duration-500" :style="'width: ' + stats.channels.whatsapp.percent + '%'"></div>
                    </div>
                </div>

                {{-- KPI 3 : Inscriptions Utilisateurs --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Utilisateurs inscrits</span>
                        <span class="rounded-full bg-sky-100 p-2 text-sky-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-slate-950 tracking-tight"
                              x-text="formatNumber(stats.summary.users_period)">
                            {{ number_format($initialStats['summary']['users_period'], 0, ',', ' ') }}
                        </span>
                        <span class="text-xs text-slate-400">/ <span x-text="formatNumber(stats.summary.users_total)">{{ $initialStats['summary']['users_total'] }}</span> tot.</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        <span class="font-bold text-slate-800" x-text="formatNumber(stats.summary.users_verified)">{{ $initialStats['summary']['users_verified'] }}</span>
                        vérifiés par téléphone sur la période
                    </p>
                    <div class="mt-3 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sky-500 transition-all duration-500" style="width: 70%"></div>
                    </div>
                </div>

                {{-- KPI 4 : Taux d'Intérêt / Conversion --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Ratio Clics / Annonce</span>
                        <span class="rounded-full bg-amber-100 p-2 text-amber-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-[#7D5A50] tracking-tight"
                              x-text="stats.summary.conversion_rate">
                            {{ $initialStats['summary']['conversion_rate'] }}
                        </span>
                        <span class="text-xs font-bold text-slate-500">contacts / fiche</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        Moyenne sur <span class="font-bold text-slate-700">{{ $metrics['listings_active'] }}</span> annonces en ligne
                    </p>
                    <div class="mt-3 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#7D5A50] transition-all duration-500" style="width: 60%"></div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════ BLOC 2 : GRAPHIQUE INTERACTIF + RÉPARTITION CANAUX ══════════════════ --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Grand Graphique Interactif (Timeline) --}}
                <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base font-black text-slate-950">Courbe dynamique de fréquentation</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Évolution temporelle des contacts et des nouveaux utilisateurs.</p>
                        </div>

                        {{-- Sélecteur de série à afficher sur le graphique --}}
                        <div class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 text-xs">
                            <button type="button"
                                    @click="setChartSeries('contacts')"
                                    :class="chartSeries === 'contacts' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600'"
                                    class="rounded-md px-2.5 py-1 transition">
                                Tous contacts
                            </button>
                            <button type="button"
                                    @click="setChartSeries('whatsapp')"
                                    :class="chartSeries === 'whatsapp' ? 'bg-[#25D366] text-white shadow-2xs font-bold' : 'text-slate-600'"
                                    class="rounded-md px-2.5 py-1 transition">
                                WhatsApp
                            </button>
                            <button type="button"
                                    @click="setChartSeries('users')"
                                    :class="chartSeries === 'users' ? 'bg-sky-600 text-white shadow-2xs font-bold' : 'text-slate-600'"
                                    class="rounded-md px-2.5 py-1 transition">
                                Inscriptions
                            </button>
                        </div>
                    </div>

                    {{-- Canvas Chart.js --}}
                    <div class="mt-5 relative" style="height: 280px;">
                        <canvas id="timelineChart"></canvas>
                    </div>

                    {{-- Légende dynamique bas de graphique --}}
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#7D5A50]"></span>
                                Contacts totaux : <strong class="text-slate-800" x-text="stats.summary.contacts_total"></strong>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#25D366]"></span>
                                WhatsApp : <strong class="text-slate-800" x-text="stats.channels.whatsapp.count"></strong>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>
                                Utilisateurs : <strong class="text-slate-800" x-text="stats.summary.users_period"></strong>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400">Survolez un point pour voir les détails</span>
                    </div>
                </div>

                {{-- Camembert / Donut Interactif des Canaux + Taux --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-base font-black text-slate-950">Répartition des Canaux</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Par quel moyen les acheteurs contactent les vendeurs ?</p>
                        </div>

                        {{-- Donut Chart --}}
                        <div class="mt-4 relative flex items-center justify-center" style="height: 190px;">
                            <canvas id="channelsChart"></canvas>
                        </div>

                        {{-- Détails cliquables --}}
                        <div class="mt-4 space-y-2">
                            {{-- WhatsApp --}}
                            <div class="flex items-center justify-between rounded-xl bg-emerald-50/70 p-2.5 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-[#25D366]"></span>
                                    <span class="font-bold text-slate-800">WhatsApp</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-950" x-text="formatNumber(stats.channels.whatsapp.count)"></span>
                                    <span class="rounded bg-emerald-200 px-1.5 py-0.2 text-[10px] font-black text-emerald-900" x-text="stats.channels.whatsapp.percent + '%'"></span>
                                </div>
                            </div>

                            {{-- Appels --}}
                            <div class="flex items-center justify-between rounded-xl bg-sky-50/70 p-2.5 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-sky-500"></span>
                                    <span class="font-bold text-slate-800">Appels directs</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-950" x-text="formatNumber(stats.channels.phone.count)"></span>
                                    <span class="rounded bg-sky-200 px-1.5 py-0.2 text-[10px] font-black text-sky-900" x-text="stats.channels.phone.percent + '%'"></span>
                                </div>
                            </div>

                            {{-- Formulaires --}}
                            <div class="flex items-center justify-between rounded-xl bg-indigo-50/70 p-2.5 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-indigo-500"></span>
                                    <span class="font-bold text-slate-800">Formulaires Web</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-950" x-text="formatNumber(stats.channels.form.count)"></span>
                                    <span class="rounded bg-indigo-200 px-1.5 py-0.2 text-[10px] font-black text-indigo-900" x-text="stats.channels.form.percent + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════ BLOC 3 : FLUX EN DIRECT + TOP ANNONCES & VENDEURS ══════════════════ --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Flux d'activité en direct (Live Activity Feed) --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <h2 class="text-base font-black text-slate-950">Activité en direct</h2>
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-400">Temps réel</span>
                    </div>

                    <div class="mt-4 space-y-3 max-h-[360px] overflow-y-auto pr-1">
                        <template x-for="(act, idx) in stats.recent_activities" :key="idx">
                            <div class="flex items-start gap-3 rounded-xl border border-slate-100 p-2.5 transition hover:bg-slate-50">
                                <div class="shrink-0 mt-0.5">
                                    <template x-if="act.channel === 'whatsapp'">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#25D366]/15 text-[#25D366]">
                                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        </span>
                                    </template>
                                    <template x-if="act.channel === 'phone'">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </span>
                                    </template>
                                    <template x-if="act.type === 'user'">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        </span>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-xs font-bold text-slate-900 truncate" x-text="act.title"></p>
                                        <span class="text-[10px] text-slate-400 shrink-0" x-text="act.time"></span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-[#7D5A50] truncate" x-text="act.detail"></p>
                                    <p class="text-[10px] text-slate-400 truncate" x-text="act.meta"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Annonces les plus contactées --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-black text-slate-950">Top Produits demandés</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Annonces ayant généré le plus de clics.</p>
                        </div>
                        <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">Populaires</span>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        @foreach ($topContactedListings as $listing)
                            <a href="{{ route('listings.show', $listing) }}"
                               class="group flex items-center justify-between rounded-xl border border-slate-100 p-2.5 transition hover:border-[#7D5A50]/40 hover:bg-slate-50">
                                <div class="min-w-0 flex-1 pr-2">
                                    <p class="text-xs font-bold text-slate-900 group-hover:text-[#7D5A50] truncate">{{ $listing->title }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $listing->category?->name }} · {{ $listing->location }}</p>
                                </div>
                                <span class="rounded-lg bg-[#7D5A50] px-2 py-1 text-xs font-black text-white shrink-0">
                                    {{ $listing->contact_events_count }} clics
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Vendeurs les plus sollicités & à certifier --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-black text-slate-950">Vendeurs à vérifier</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Priorité aux vendeurs portant des annonces.</p>
                        </div>
                        <span class="rounded bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">Action</span>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        @forelse ($sellersToVerify as $seller)
                            <div class="rounded-xl border border-slate-100 p-2.5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-900">{{ $seller->name }}</p>
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700">
                                        {{ $seller->listings_count }} annonces
                                    </span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-[11px] text-slate-500">
                                    <span>{{ $seller->phone ?? 'Sans téléphone' }}</span>
                                    <span class="font-bold text-[#7D5A50]">{{ $seller->received_contact_events_count }} clics reçus</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 p-4">Tous les vendeurs sont vérifiés.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- ══════════════════ BLOC 4 : GESTION CATALOGUE, MERCURIALE & BLOG ══════════════════ --}}
            <div class="grid gap-6 sm:grid-cols-3">
                {{-- Catégories --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-black text-slate-950">Catégories &amp; Filières</h2>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800">{{ $metrics['categories_active'] }} actives</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Ajoutez, modifiez ou supprimez les filières agricoles et leurs visuels d'accueil.</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-xs font-semibold text-slate-700">{{ $metrics['categories_total'] }} catégories au total</span>
                        <div class="flex gap-2">
                            <a href="{{ Route::has('admin.categories.create') ? route('admin.categories.create') : url('/admin/categories/creer') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">+ Ajouter</a>
                            <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : url('/admin/categories') }}" class="rounded-lg bg-[#7D5A50] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#6b4d44]">Gérer</a>
                        </div>
                    </div>
                </div>

                {{-- Mercuriale --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-black text-slate-950">Prix du marché &amp; Cotations</h2>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800">{{ $metrics['market_prices_active'] }} actifs</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Mettez à jour les cours du riz, de l'oignon, du labour et du bétail pour les visiteurs.</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-xs font-semibold text-slate-700">{{ $metrics['market_prices_total'] }} cours enregistrés</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.mercuriale.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">+ Ajouter</a>
                            <a href="{{ route('admin.mercuriale.index') }}" class="rounded-lg bg-[#7D5A50] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#6b4d44]">Gérer les cours</a>
                        </div>
                    </div>
                </div>

                {{-- Blog --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-black text-slate-950">Blog &amp; Conseils agricoles</h2>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800">{{ $metrics['articles_published'] }} publiés</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Rédigez des guides pour informer et fidéliser votre communauté d'agriculteurs.</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-xs font-semibold text-slate-700">{{ $metrics['articles_total'] }} articles au total</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.articles.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">+ Rédiger</a>
                            <a href="{{ route('admin.articles.index') }}" class="rounded-lg bg-[#7D5A50] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#6b4d44]">Gérer le blog</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════ COMPOSANT SCRIPT DYNAMIQUE ALPINE.JS + CHART.JS ══════════════════ --}}
    <script>
        function adminDashboard(config) {
            return {
                period: 'month',
                periodLabels: {
                    'today': "Aujourd'hui",
                    'week': "7 derniers jours",
                    'month': "Ce mois-ci (30 jours)",
                    'year': "Cette année (" + new Date().getFullYear() + ")",
                    'all': "Tout l'historique"
                },
                get periodLabel() {
                    return this.periodLabels[this.period] || 'Ce mois-ci';
                },
                stats: config.initialStats,
                statsUrl: config.statsUrl,
                loading: false,
                autoRefresh: false,
                refreshTimer: null,
                chartSeries: 'contacts', // contacts, whatsapp, users
                timelineChart: null,
                channelsChart: null,

                init() {
                    this.$nextTick(() => {
                        this.initTimelineChart();
                        this.initChannelsChart();
                    });
                },

                setPeriod(p) {
                    if (this.period === p) return;
                    this.period = p;
                    this.fetchStats();
                },

                setChartSeries(s) {
                    this.chartSeries = s;
                    this.updateTimelineChart();
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.refreshTimer = setInterval(() => {
                            this.fetchStats(true);
                        }, 15000);
                    } else if (this.refreshTimer) {
                        clearInterval(this.refreshTimer);
                        this.refreshTimer = null;
                    }
                },

                refresh() {
                    this.fetchStats();
                },

                async fetchStats(silent = false) {
                    if (!silent) this.loading = true;
                    try {
                        const res = await fetch(this.statsUrl + '?period=' + encodeURIComponent(this.period), {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.stats = data;
                            this.updateTimelineChart();
                            this.updateChannelsChart();
                        }
                    } catch (e) {
                        console.error('Erreur chargement stats dynamiques', e);
                    } finally {
                        if (!silent) this.loading = false;
                    }
                },

                formatNumber(val) {
                    return new Intl.NumberFormat('fr-FR').format(val || 0);
                },

                initTimelineChart() {
                    const ctx = document.getElementById('timelineChart');
                    if (!ctx || !window.Chart) return;

                    const labels = this.stats.timeline.map(item => item.label);
                    const dataset = this.getTimelineDataset();

                    this.timelineChart = new window.Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [dataset]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 12 },
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        title: (items) => {
                                            const idx = items[0].dataIndex;
                                            return this.stats.timeline[idx]?.full_date || items[0].label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 10 },
                                        color: '#94a3b8',
                                        maxRotation: 0
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        font: { size: 10 },
                                        color: '#94a3b8',
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                },

                getTimelineDataset() {
                    if (this.chartSeries === 'whatsapp') {
                        return {
                            label: 'Clics WhatsApp',
                            data: this.stats.timeline.map(item => item.whatsapp),
                            borderColor: '#25D366',
                            backgroundColor: 'rgba(37, 211, 102, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#25D366'
                        };
                    } else if (this.chartSeries === 'users') {
                        return {
                            label: 'Inscriptions',
                            data: this.stats.timeline.map(item => item.users),
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#0284c7'
                        };
                    }
                    return {
                        label: 'Tous les contacts',
                        data: this.stats.timeline.map(item => item.contacts),
                        borderColor: '#7D5A50',
                        backgroundColor: 'rgba(125, 90, 80, 0.12)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#7D5A50'
                    };
                },

                updateTimelineChart() {
                    if (!this.timelineChart) return;
                    this.timelineChart.data.labels = this.stats.timeline.map(item => item.label);
                    this.timelineChart.data.datasets = [this.getTimelineDataset()];
                    this.timelineChart.update('active');
                },

                initChannelsChart() {
                    const ctx = document.getElementById('channelsChart');
                    if (!ctx || !window.Chart) return;

                    const ch = this.stats.channels;
                    this.channelsChart = new window.Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['WhatsApp', 'Appels', 'Formulaire'],
                            datasets: [{
                                data: [ch.whatsapp.count, ch.phone.count, ch.form.count],
                                backgroundColor: ['#25D366', '#0284c7', '#6366f1'],
                                borderWidth: 3,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (item) => {
                                            const total = item.dataset.data.reduce((a, b) => a + b, 0);
                                            const val = item.raw || 0;
                                            const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                            return ` ${item.label} : ${val} (${pct}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                updateChannelsChart() {
                    if (!this.channelsChart) return;
                    const ch = this.stats.channels;
                    this.channelsChart.data.datasets[0].data = [ch.whatsapp.count, ch.phone.count, ch.form.count];
                    this.channelsChart.update('active');
                }
            };
        }
    </script>
</x-app-layout>
