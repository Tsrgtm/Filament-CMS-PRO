<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Concerns;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Nepal360\FilamentCmsPro\Models\CmsSetting;

trait HasDynamicCustomFields
{
    /**
     * Build and return custom form fields based on configuration settings.
     */
    public static function getCustomFieldsSchema(string $resourceType, bool $translatableOnly = false): array
    {
        $fieldsConfig = CmsSetting::get("{$resourceType}_custom_fields", []);
        $components = [];

        foreach ($fieldsConfig as $config) {
            $isTranslatable = (bool) ($config['is_translatable'] ?? false);

            if ($translatableOnly && !$isTranslatable) {
                continue;
            }
            if (!$translatableOnly && $isTranslatable) {
                continue;
            }

            $key = 'custom_fields.' . $config['name'];
            $label = $config['label'];
            $type = $config['type'] ?? 'text';
            $required = (bool) ($config['required'] ?? false);

            $field = match ($type) {
                'textarea' => Textarea::make($key)->label($label)->rows(3),
                'number' => TextInput::make($key)->label($label)->numeric(),
                'toggle' => Toggle::make($key)->label($label),
                'select' => Select::make($key)
                    ->label($label)
                    ->options(static::parseSelectOptions($config['options'] ?? '')),
                default => TextInput::make($key)->label($label),
            };

            if ($required) {
                $field->required();
            }

            $components[] = $field;
        }

        return $components;
    }

    /**
     * Filter the list of table columns based on customized visibility settings.
     */
    public static function getVisibleTableColumns(string $resourceType, array $allColumns): array
    {
        $default = match ($resourceType) {
            'posts' => ['id', 'featured_image', 'title', 'status', 'published_at'],
            'categories' => ['id', 'name', 'parent', 'order'],
            'tags' => ['id', 'name'],
            'comments' => ['id', 'post', 'author', 'content', 'status', 'created_at'],
            default => array_keys($allColumns),
        };

        $visibleKeys = CmsSetting::get("{$resourceType}_columns", $default);
        $visibleColumns = [];

        foreach ($visibleKeys as $key) {
            if (isset($allColumns[$key])) {
                $visibleColumns[] = $allColumns[$key];
            }
        }

        return $visibleColumns;
    }

    /**
     * Filter active filters based on user configuration settings.
     */
    public static function getVisibleTableFilters(string $resourceType, array $allFilters): array
    {
        $default = match ($resourceType) {
            'posts' => ['status', 'published_at'],
            default => [],
        };

        $activeKeys = CmsSetting::get("{$resourceType}_filters", $default);
        $activeFilters = [];

        foreach ($activeKeys as $key) {
            if (isset($allFilters[$key])) {
                $activeFilters[] = $allFilters[$key];
            }
        }

        return $activeFilters;
    }

    /**
     * Parse comma-separated options into key-value pairs.
     */
    protected static function parseSelectOptions(string $optionsString): array
    {
        if (blank($optionsString)) {
            return [];
        }

        $items = explode(',', $optionsString);
        $options = [];

        foreach ($items as $item) {
            $item = trim($item);
            $options[$item] = $item;
        }

        return $options;
    }
}
