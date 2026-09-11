<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockNotificationAdminController extends Controller
{
    public function index(): View
    {
        $notifications = StockNotification::with('product')
            ->latest()
            ->paginate(30);

        return view('admin.stock-notifications.index', compact('notifications'));
    }

    public function destroy(StockNotification $stockNotification): RedirectResponse
    {
        $stockNotification->delete();

        return back()->with('success', 'Aviso de stock eliminado.');
    }
}
