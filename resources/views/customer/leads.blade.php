<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">Leads</h2>
            <p class="text-sm text-gray-400 mt-1">Gérez les demandes de contact de vos produits</p>
        </div>
    </x-slot>

    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
        @if($leads->isEmpty())
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">Aucun lead pour le moment</h3>
                <p class="text-gray-500">Les demandes de contact des visiteurs apparaîtront ici</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#0a1628] border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Téléphone</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Langue</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $lead->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        @if($lead->product && $lead->product->first_image)
                                            <img src="{{ $lead->product->first_image }}" alt="{{ $lead->product->name }}" class="w-10 h-10 rounded object-cover">
                                        @endif
                                        <span class="text-white font-medium">{{ $lead->product->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-white font-medium">{{ $lead->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="tel:{{ $lead->phone }}" class="flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $lead->phone }}
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="text-xs text-green-400 hover:text-green-300 transition mt-1 block">
                                        WhatsApp
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($lead->language === 'fr') bg-blue-500/20 text-blue-300
                                        @elseif($lead->language === 'en') bg-purple-500/20 text-purple-300
                                        @else bg-green-500/20 text-green-300
                                        @endif">
                                        {{ strtoupper($lead->language) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300 max-w-xs">
                                    @if($lead->note)
                                        <div class="truncate" title="{{ $lead->note }}">
                                            {{ $lead->note }}
                                        </div>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleLeadDetails({{ $lead->id }})" class="p-2 text-gray-400 hover:text-cyan-400 transition rounded-lg hover:bg-white/10" title="Voir les détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <!-- Expandable Details Row -->
                            <tr id="lead-details-{{ $lead->id }}" class="hidden bg-[#1a2d42]/50">
                                <td colspan="7" class="px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Customer Info -->
                                        <div class="bg-[#0f1c2e] rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-cyan-400 mb-3 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                Informations Client
                                            </h4>
                                            <dl class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Nom:</dt>
                                                    <dd class="text-white font-medium">{{ $lead->name ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Téléphone:</dt>
                                                    <dd class="text-white">{{ $lead->phone ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Adresse:</dt>
                                                    <dd class="text-white">{{ $lead->address ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Ville:</dt>
                                                    <dd class="text-white">{{ $lead->city ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Langue:</dt>
                                                    <dd class="text-white">{{ strtoupper($lead->language ?? 'N/A') }}</dd>
                                                </div>
                                                @if($lead->note)
                                                <div>
                                                    <dt class="text-gray-400 mb-1">Note:</dt>
                                                    <dd class="text-white bg-[#1a2d42] p-2 rounded text-xs">{{ $lead->note }}</dd>
                                                </div>
                                                @endif
                                                @if($lead->custom_fields && is_array($lead->custom_fields))
                                                @foreach($lead->custom_fields as $key => $value)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">{{ ucfirst($key) }}:</dt>
                                                    <dd class="text-white">{{ $value }}</dd>
                                                </div>
                                                @endforeach
                                                @endif
                                            </dl>
                                        </div>
                                        
                                        <!-- Product Info -->
                                        <div class="bg-[#0f1c2e] rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-cyan-400 mb-3 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Informations Produit
                                            </h4>
                                            <dl class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Produit:</dt>
                                                    <dd class="text-white font-medium">{{ $lead->product->name ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Prix sélectionné:</dt>
                                                    <dd class="text-green-400 font-semibold">{{ $lead->selected_price ? number_format($lead->selected_price, 2) . ' DHS' : 'N/A' }}</dd>
                                                </div>
                                                @if($lead->promotion)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Promotion:</dt>
                                                    <dd class="text-yellow-400">{{ $lead->promotion->label ?? $lead->promotion->quantity_range ?? 'N/A' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Prix promotion:</dt>
                                                    <dd class="text-yellow-400">{{ $lead->promotion->price ? number_format($lead->promotion->price, 2) . ' DHS' : 'N/A' }}</dd>
                                                </div>
                                                @endif
                                                @if($lead->variation)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Variante:</dt>
                                                    <dd class="text-blue-400">
                                                        @php
                                                            $variantDisplay = 'Variante';
                                                            if (!empty($lead->variation->attributes) && is_array($lead->variation->attributes)) {
                                                                $parts = [];
                                                                foreach ($lead->variation->attributes as $key => $value) {
                                                                    $parts[] = ucfirst($key) . ': ' . $value;
                                                                }
                                                                $variantDisplay = implode(', ', $parts);
                                                            }
                                                        @endphp
                                                        {{ $variantDisplay }}
                                                    </dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Prix variante:</dt>
                                                    <dd class="text-blue-400">{{ $lead->variation->price ? number_format($lead->variation->price, 2) . ' DHS' : 'N/A' }}</dd>
                                                </div>
                                                @endif
                                            </dl>
                                        </div>
                                        
                                        <!-- Order Info -->
                                        <div class="bg-[#0f1c2e] rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-cyan-400 mb-3 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Informations Commande
                                            </h4>
                                            <dl class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">ID Lead:</dt>
                                                    <dd class="text-white font-mono">#{{ $lead->id }}</dd>
                                                </div>
                                                @if($lead->status)
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-500/20 text-yellow-400',
                                                        'confirmed' => 'bg-blue-500/20 text-blue-400',
                                                        'shipped' => 'bg-purple-500/20 text-purple-400',
                                                        'delivered' => 'bg-green-500/20 text-green-400',
                                                        'cancelled' => 'bg-red-500/20 text-red-400',
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'En attente',
                                                        'confirmed' => 'Confirmé',
                                                        'shipped' => 'Expédié',
                                                        'delivered' => 'Livré',
                                                        'cancelled' => 'Annulé',
                                                    ];
                                                    $status = $lead->status;
                                                @endphp
                                                <div class="flex justify-between items-center">
                                                    <dt class="text-gray-400">Statut:</dt>
                                                    <dd>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-500/20 text-gray-400' }}">
                                                            {{ $statusLabels[$status] ?? $status }}
                                                        </span>
                                                    </dd>
                                                </div>
                                                @endif
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Créé le:</dt>
                                                    <dd class="text-white">{{ $lead->created_at->format('d/m/Y à H:i') }}</dd>
                                                </div>
                                                @if($lead->ip_address)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-400">Adresse IP:</dt>
                                                    <dd class="text-white font-mono text-xs">{{ $lead->ip_address }}</dd>
                                                </div>
                                                @endif
                                                @if($lead->user_agent)
                                                <div>
                                                    <dt class="text-gray-400 mb-1">Navigateur:</dt>
                                                    <dd class="text-white text-xs bg-[#1a2d42] p-2 rounded break-all">{{ Str::limit($lead->user_agent, 80) }}</dd>
                                                </div>
                                                @endif
                                            </dl>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="px-6 py-4 bg-[#0a1628] border-t border-white/10">
                    {{ $leads->links() }}
                </div>
            @endif
        @endif
    </div>
    
    <script>
        function toggleLeadDetails(leadId) {
            const detailsRow = document.getElementById('lead-details-' + leadId);
            if (detailsRow) {
                detailsRow.classList.toggle('hidden');
            }
        }
    </script>
</x-customer-layout>
