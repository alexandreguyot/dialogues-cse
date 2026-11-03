<?php

return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),

    // Les formulaires User / Role / Permission utilisent wire:model="user.name" sur des modèles Eloquent.
    'legacy_model_binding' => true,

    'pagination_theme' => 'tailwind',
];
