<?php

namespace App\Filament\Support;

use App\Support\FactoryOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class FieldBuilder
{
    /**
     * @param  list<array<string, mixed>>  $fields
     * @return list<\Filament\Forms\Components\Component>
     */
    public static function form(array $fields): array
    {
        return array_map(fn (array $field) => self::input($field), $fields);
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @return list<TextColumn|IconColumn|ImageColumn>
     */
    public static function columns(array $fields): array
    {
        $columns = [];

        foreach ($fields as $field) {
            if (($field['column'] ?? true) === false) {
                continue;
            }

            $name = $field['column_name'] ?? ($field['relation'] ?? null ? $field['relation'].'.'.$field['title'] : $field['name']);
            $label = $field['label'] ?? str($field['name'])->headline()->toString();

            if (($field['type'] ?? '') === 'toggle') {
                $columns[] = IconColumn::make($field['name'])->label($label)->boolean();
                continue;
            }

            if (($field['type'] ?? '') === 'file') {
                $columns[] = ImageColumn::make($field['name'])->label($label);
                continue;
            }

            $column = TextColumn::make($name)->label($label)->searchable($field['search'] ?? in_array($field['type'] ?? '', ['text', 'textarea'], true))->toggleable();

            if (in_array($field['type'] ?? '', ['date', 'datetime'], true)) {
                $column->date(($field['type'] ?? '') === 'datetime' ? 'd M Y H:i' : 'd M Y');
            }

            if (($field['badge'] ?? false) === true) {
                $column->badge();
            }

            if (($field['limit'] ?? null) !== null) {
                $column->limit((int) $field['limit']);
            }

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @return list<SelectFilter|TernaryFilter>
     */
    public static function filters(array $fields): array
    {
        $filters = [];

        foreach ($fields as $field) {
            if (($field['filter'] ?? false) !== true) {
                continue;
            }

            $label = $field['label'] ?? str($field['name'])->headline()->toString();

            if (($field['type'] ?? '') === 'toggle') {
                $filters[] = TernaryFilter::make($field['name'])->label($label);
                continue;
            }

            if (($field['type'] ?? '') === 'relation') {
                $filters[] = SelectFilter::make($field['name'])->label($label)->relationship($field['relation'], $field['title']);
                continue;
            }

            if (($field['type'] ?? '') === 'select') {
                $filters[] = SelectFilter::make($field['name'])->label($label)->options(self::options($field));
            }
        }

        return $filters;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected static function input(array $field): mixed
    {
        $name = $field['name'];
        $label = $field['label'] ?? str($name)->headline()->toString();
        $type = $field['type'] ?? 'text';

        $component = match ($type) {
            'textarea' => Textarea::make($name)->rows(4)->columnSpanFull(),
            'number' => TextInput::make($name)->numeric(),
            'decimal' => TextInput::make($name)->numeric(),
            'date' => DatePicker::make($name),
            'datetime' => DateTimePicker::make($name),
            'time' => TimePicker::make($name)->seconds(false),
            'toggle' => Toggle::make($name),
            'file' => FileUpload::make($name)->image()->directory('factory')->columnSpanFull(),
            'keyvalue' => KeyValue::make($name)->columnSpanFull(),
            'relation' => Select::make($name)->relationship($field['relation'], $field['title'])->searchable()->preload(),
            'select' => Select::make($name)->options(self::options($field))->searchable(),
            default => TextInput::make($name),
        };

        $component = $component->label($label);

        if ($field['required'] ?? false) {
            $component = $component->required();
        }

        if (isset($field['default'])) {
            $component = $component->default($field['default']);
        }

        if (isset($field['helper'])) {
            $component = $component->helperText($field['helper']);
        }

        return $component;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, string>
     */
    protected static function options(array $field): array
    {
        $options = $field['options'] ?? [];

        if (is_string($options)) {
            return FactoryOptions::map($options);
        }

        return $options;
    }
}
