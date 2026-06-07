<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\Store;
use Illuminate\Http\Request;

class BlockedIpController extends Controller
{
    public function index()
    {
        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return redirect()->route('stores.dashboard')->with('error', 'Please select a store first.');
        }

        $activeStore = Store::findOrFail($activeStoreId);
        $blockedIps = BlockedIp::where('store_id', $activeStoreId)
            ->with('blockedByUser')
            ->latest()
            ->get();

        return view('customer.blocked-ips', compact('activeStore', 'blockedIps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255',
        ]);

        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return back()->with('error', 'No active store selected.');
        }

        BlockedIp::firstOrCreate(
            [
                'store_id' => $activeStoreId,
                'ip_address' => $request->ip_address,
            ],
            [
                'reason' => $request->reason,
                'blocked_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Adresse IP bloquée avec succès.');
    }

    public function reactivate(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return back()->with('error', 'Aucune boutique active sélectionnée.');
        }

        $deleted = BlockedIp::where('store_id', $activeStoreId)
            ->where('ip_address', $request->ip_address)
            ->delete();

        if ($deleted === 0) {
            return back()->with('error', 'Cette adresse IP n\'est pas bloquée.');
        }

        return back()->with('success', 'Accès réactivé pour l\'adresse IP ' . $request->ip_address . '.');
    }

    public function destroy(BlockedIp $blockedIp)
    {
        $activeStoreId = session('active_store_id');

        if (!$activeStoreId || $blockedIp->store_id != $activeStoreId) {
            abort(403);
        }

        $ip = $blockedIp->ip_address;
        $blockedIp->delete();

        return back()->with('success', 'Accès réactivé pour l\'adresse IP ' . $ip . '.');
    }
}
