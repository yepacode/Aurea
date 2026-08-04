<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizPageSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'recommendation_rules' => 'array',
        'questions' => 'array',
    ];

    public static function getCurrent(): static
    {
        return static::where('is_active', true)->latest()->first()
            ?? static::create([]);
    }

    /**
     * Get configured questions or default ones if none are set.
     */
    public function getQuestionsOrDefault(): array
    {
        return !empty($this->questions) ? $this->questions : self::defaultQuestions();
    }

    /**
     * Default quiz questions (used when admin hasn't configured any).
     */
    public static function defaultQuestions(): array
    {
        return [
            [
                'key' => 'skin_type',
                'label' => '¿Cómo es tu piel?',
                'subtitle' => 'Elige la opción con la que más te identifiques.',
                'options' => [
                    ['value' => 'grasa', 'label' => 'Grasa', 'desc' => 'Brilla en la zona T, poros visibles'],
                    ['value' => 'seca', 'label' => 'Seca', 'desc' => 'Tirante, se descama, poca grasa'],
                    ['value' => 'mixta', 'label' => 'Mixta', 'desc' => 'Zona T grasa, mejillas normales'],
                    ['value' => 'sensible', 'label' => 'Sensible / Normal', 'desc' => 'Se irrita fácil o está equilibrada'],
                ],
            ],
            [
                'key' => 'concern',
                'label' => '¿Qué te preocupa más?',
                'subtitle' => 'Tu objetivo principal ahora mismo.',
                'options' => [
                    ['value' => 'brillo', 'label' => 'Brillo y poros', 'desc' => 'Controlar la grasa'],
                    ['value' => 'resequedad', 'label' => 'Resequedad', 'desc' => 'Hidratar y calmar'],
                    ['value' => 'manchas', 'label' => 'Manchas y tono', 'desc' => 'Unificar y dar luz'],
                    ['value' => 'lineas', 'label' => 'Líneas y firmeza', 'desc' => 'Nutrir y prevenir'],
                ],
            ],
            [
                'key' => 'routine',
                'label' => '¿Cómo es tu rutina hoy?',
                'subtitle' => 'Para recomendarte algo realista.',
                'options' => [
                    ['value' => 'ninguna', 'label' => 'Casi nada', 'desc' => 'Agua y a veces crema'],
                    ['value' => 'basica', 'label' => 'Básica', 'desc' => 'Limpio e hidrato'],
                    ['value' => 'completa', 'label' => 'Completa', 'desc' => 'Limpieza, sérum, SPF y más'],
                ],
            ],
            [
                'key' => 'interest',
                'label' => '¿Qué te interesa más ahora?',
                'subtitle' => 'Para afinar tu recomendación.',
                'options' => [
                    ['value' => 'skincare', 'label' => 'Cuidado de piel', 'desc' => 'Skincare y rituales'],
                    ['value' => 'unas', 'label' => 'Uñas / Nail art', 'desc' => 'Esmaltes y decoración'],
                    ['value' => 'maquillaje', 'label' => 'Maquillaje', 'desc' => 'Bases, labiales, sombras'],
                    ['value' => 'cabello', 'label' => 'Cabello', 'desc' => 'Peluquería y estilizado'],
                ],
            ],
        ];
    }
}
