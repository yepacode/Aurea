<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransferSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankTransferAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.bank-transfer.index', [
            'bankName'              => BankTransferSetting::get('bank_name', ''),
            'accountHolder'         => BankTransferSetting::get('account_holder', ''),
            'nit'                   => BankTransferSetting::get('nit', ''),
            'accountType'           => BankTransferSetting::get('account_type', ''),
            'accountNumber'         => BankTransferSetting::get('account_number', ''),
            'documentType'          => BankTransferSetting::get('document_type', ''),
            'documentNumber'        => BankTransferSetting::get('document_number', ''),
            'referenceInstructions' => BankTransferSetting::get('reference_instructions', 'Usa tu número de pedido como referencia'),
            'additionalNotes'       => BankTransferSetting::get('additional_notes', ''),
            // Medios digitales colombianos (Nequi / Daviplata / Bancolombia a la mano)
            'nequiEnabled'          => (bool) BankTransferSetting::get('nequi_enabled', ''),
            'nequiNumber'           => BankTransferSetting::get('nequi_number', ''),
            'nequiInstructions'     => BankTransferSetting::get('nequi_instructions', ''),
            'daviplataEnabled'      => (bool) BankTransferSetting::get('daviplata_enabled', ''),
            'daviplataNumber'       => BankTransferSetting::get('daviplata_number', ''),
            'daviplataInstructions' => BankTransferSetting::get('daviplata_instructions', ''),
            'bancolombiaAmanoEnabled' => (bool) BankTransferSetting::get('bancolombia_amano_enabled', ''),
            'bancolombiaAmanoNumber'  => BankTransferSetting::get('bancolombia_amano_number', ''),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name'              => 'required|string|max:255',
            'account_holder'         => 'required|string|max:255',
            'nit'                    => 'nullable|string|max:30',
            'account_type'           => 'nullable|in:Ahorros,Corriente',
            'account_number'         => 'nullable|string|max:30',
            'document_type'          => 'nullable|in:CC,NIT,CE',
            'document_number'        => 'nullable|string|max:30',
            'reference_instructions' => 'nullable|string|max:500',
            'additional_notes'       => 'nullable|string|max:1000',
            // Medios digitales colombianos
            'nequi_enabled'                => 'nullable|boolean',
            'nequi_number'                 => 'nullable|string|max:30',
            'nequi_instructions'           => 'nullable|string|max:1000',
            'daviplata_enabled'            => 'nullable|boolean',
            'daviplata_number'             => 'nullable|string|max:30',
            'daviplata_instructions'       => 'nullable|string|max:1000',
            'bancolombia_amano_enabled'    => 'nullable|boolean',
            'bancolombia_amano_number'     => 'nullable|string|max:30',
        ]);

        BankTransferSetting::set('bank_name', $request->input('bank_name'));
        BankTransferSetting::set('account_holder', $request->input('account_holder'));
        BankTransferSetting::set('nit', $request->input('nit', ''));
        BankTransferSetting::set('account_type', $request->input('account_type', ''));
        BankTransferSetting::set('account_number', $request->input('account_number', ''));
        BankTransferSetting::set('document_type', $request->input('document_type', ''));
        BankTransferSetting::set('document_number', $request->input('document_number', ''));
        BankTransferSetting::set('reference_instructions', $request->input('reference_instructions', ''));
        BankTransferSetting::set('additional_notes', $request->input('additional_notes', ''));

        // Medios digitales — el checkbox llega como '1' o ausente. Guardamos
        // '1'/'' para que (bool) lo interprete correctamente al leer.
        BankTransferSetting::set('nequi_enabled', $request->boolean('nequi_enabled') ? '1' : '');
        BankTransferSetting::set('nequi_number', $request->input('nequi_number', ''));
        BankTransferSetting::set('nequi_instructions', $request->input('nequi_instructions', ''));
        BankTransferSetting::set('daviplata_enabled', $request->boolean('daviplata_enabled') ? '1' : '');
        BankTransferSetting::set('daviplata_number', $request->input('daviplata_number', ''));
        BankTransferSetting::set('daviplata_instructions', $request->input('daviplata_instructions', ''));
        BankTransferSetting::set('bancolombia_amano_enabled', $request->boolean('bancolombia_amano_enabled') ? '1' : '');
        BankTransferSetting::set('bancolombia_amano_number', $request->input('bancolombia_amano_number', ''));

        return redirect()->route('admin.bank-transfer.index')
            ->with('success', 'Datos bancarios y medios digitales actualizados correctamente.');
    }
}
