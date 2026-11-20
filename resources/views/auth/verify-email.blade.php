<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wide text-[#7D5A50]">Verification email</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-slate-950">Confirmez votre adresse</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Un lien de verification a ete envoye. Validez-le pour finaliser les actions liees a votre email.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-md border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-4 py-3 text-sm font-semibold text-[#5a3f38]">
            Un nouveau lien de verification vient d'etre envoye.
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-sm hover:bg-[#6b4d44] focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2">
                Renvoyer le lien
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md px-2 py-2 text-sm font-bold text-slate-600 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2">
                Se deconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
