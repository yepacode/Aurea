<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactPageSetting;
use Illuminate\Http\Request;

class AdminContactPageController extends Controller
{
    public function edit()
    {
        $page = ContactPageSetting::getCurrent();

        return view('admin.pages.contact.edit', compact('page'));
    }

    public function update(Request $request)
    {
        $page = ContactPageSetting::getCurrent();

        $data = $request->except(['_token', '_method', 'whatsapp_hours']);

        // Normaliza el flag on/off del widget WhatsApp (checkbox + hidden 0)
        if (array_key_exists('whatsapp_widget_enabled', $data)) {
            $data['whatsapp_widget_enabled'] = (bool) $data['whatsapp_widget_enabled'];
        }

        // Normaliza el horario semanal: {mon: {enabled, open, close}, ...}
        $rawHours = $request->input('whatsapp_hours');
        if (is_array($rawHours)) {
            $normalized = [];
            foreach (ContactPageSetting::DAY_KEYS as $day) {
                $slot = $rawHours[$day] ?? [];
                $normalized[$day] = [
                    'enabled' => (bool) ($slot['enabled'] ?? false),
                    'open'    => $this->normalizeTime($slot['open']  ?? '08:00'),
                    'close'   => $this->normalizeTime($slot['close'] ?? '18:00'),
                ];
            }
            // Al ser cast 'array' en el modelo, Eloquent lo serializa a JSON automáticamente.
            $data['whatsapp_hours_json'] = $normalized;
        }

        $page->update($data);

        return redirect()->route('admin.pages.contact.edit')
            ->with('success', 'Página de contacto actualizada correctamente.');
    }

    private function normalizeTime(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '' || !preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return '08:00';
        }
        // Recorta segundos si vienen y aplica padding a la hora
        [$h, $m] = explode(':', $value);
        $h = str_pad((string) max(0, min(23, (int) $h)), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) max(0, min(59, (int) $m)), 2, '0', STR_PAD_LEFT);
        return "{$h}:{$m}";
    }
}
