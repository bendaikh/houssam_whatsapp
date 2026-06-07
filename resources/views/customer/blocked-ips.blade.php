@extends('layouts.customer')

@section('title', 'Blocked IPs')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Adresses IP bloquées</h1>
        <p class="text-gray-400">Bloquez ou réactivez l'accès des visiteurs à votre boutique.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 rounded-lg px-4 py-3 text-green-400">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Bloquer une adresse IP</h2>
        <form method="POST" action="{{ route('app.blocked-ips.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Adresse IP *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" required placeholder="192.168.1.1"
                    class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                @error('ip_address')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Motif (optionnel)</label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Spam, fraude, etc."
                    class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                Bloquer l'accès
            </button>
        </form>
    </div>

    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Liste des IP bloquées — {{ $activeStore->name }}</h2>
            <span class="text-sm text-gray-400">{{ $blockedIps->count() }} bloquée(s)</span>
        </div>

        @if($blockedIps->isEmpty())
        <div class="p-10 text-center text-gray-400">
            Aucune adresse IP bloquée pour le moment.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#1a2d42]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Adresse IP</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Bloqué par</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($blockedIps as $blockedIp)
                    <tr class="hover:bg-white/5">
                        <td class="px-6 py-4 font-mono text-white">{{ $blockedIp->ip_address }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $blockedIp->reason ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $blockedIp->blockedByUser->name ?? 'Système' }}</td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $blockedIp->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400">Bloqué</span>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('app.blocked-ips.destroy', $blockedIp) }}"
                                onsubmit="return confirm('Réactiver l\'accès pour {{ $blockedIp->ip_address }} ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                                    Réactiver l'accès
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
