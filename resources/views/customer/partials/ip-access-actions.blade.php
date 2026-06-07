@props(['ipAddress', 'blockedIpAddresses' => []])

@if($ipAddress)
    @if(in_array($ipAddress, $blockedIpAddresses, true))
        <span class="text-xs px-2 py-1 bg-red-600/20 text-red-400 rounded">Bloqué</span>
        <form method="POST" action="{{ route('app.blocked-ips.reactivate') }}" class="inline"
            onsubmit="return confirm('Réactiver l\'accès pour cette adresse IP ?')">
            @csrf
            <input type="hidden" name="ip_address" value="{{ $ipAddress }}">
            <button type="submit" class="text-xs px-2 py-1 bg-green-600/20 hover:bg-green-600/30 text-green-400 rounded transition">
                Réactiver l'accès
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('app.blocked-ips.store') }}" class="inline"
            onsubmit="return confirm('Bloquer cette adresse IP ?')">
            @csrf
            <input type="hidden" name="ip_address" value="{{ $ipAddress }}">
            <input type="hidden" name="reason" value="Blocked from order">
            <button type="submit" class="text-xs px-2 py-1 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded transition">
                Bloquer
            </button>
        </form>
    @endif
@endif
