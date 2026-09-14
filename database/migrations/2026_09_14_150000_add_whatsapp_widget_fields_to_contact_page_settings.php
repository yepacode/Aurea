<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_page_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_page_settings', 'whatsapp_widget_enabled')) {
                $table->boolean('whatsapp_widget_enabled')->default(true)->after('whatsapp_message');
            }
            if (! Schema::hasColumn('contact_page_settings', 'whatsapp_hours_json')) {
                $table->json('whatsapp_hours_json')->nullable()->after('whatsapp_widget_enabled');
            }
            if (! Schema::hasColumn('contact_page_settings', 'whatsapp_welcome_online')) {
                $table->text('whatsapp_welcome_online')->nullable()->after('whatsapp_hours_json');
            }
            if (! Schema::hasColumn('contact_page_settings', 'whatsapp_welcome_offline')) {
                $table->text('whatsapp_welcome_offline')->nullable()->after('whatsapp_welcome_online');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_page_settings', function (Blueprint $table) {
            foreach (['whatsapp_widget_enabled', 'whatsapp_hours_json', 'whatsapp_welcome_online', 'whatsapp_welcome_offline'] as $col) {
                if (Schema::hasColumn('contact_page_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
