<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Añade UNIQUE(email) a la tabla `customers` para cerrar el TOCTOU de registro:
 * antes de esto, dos peticiones simultáneas podían crear dos cuentas con el
 * mismo correo entre el `first()` de validación y el `create()`.
 *
 * Antes de crear el índice, verifica que no existan duplicados y aborta la
 * migración con un mensaje claro si los detecta, para que el operador limpie
 * los datos a mano y no perdamos registros por accidente.
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('customers')
            ->select('email', DB::raw('COUNT(*) as total'))
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $list = $duplicates->map(fn ($row) => "{$row->email} (x{$row->total})")->implode(', ');
            throw new \RuntimeException(
                'No se puede aplicar UNIQUE(email) en customers: hay correos duplicados. '.
                'Limpia manualmente y vuelve a correr la migración. Duplicados: '.$list
            );
        }

        Schema::table('customers', function (Blueprint $t) {
            $t->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $t) {
            $t->dropUnique(['email']);
        });
    }
};
