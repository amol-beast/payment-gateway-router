<?php

namespace App\Filament\Resources\PGConnections\Schemas;

use App\Models\SupportedPaymentGateway;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Str;

class PGConnectionAttributeSchema
{
    /**
     * The "attributes" json column on supported_payment_gateways stores a
     * 'required' map of attribute-key => spec, where spec is "type" or
     * "type|extra" (extra being a default value for string/float, or a
     * comma-separated list of options for radio). e.g.:
     *   'fees_rate' => 'float'
     *   'paymentAction' => 'string|Sale'
     *   'mode' => 'radio|live,sandbox'
     *
     * @return array<string, array{type: string, extra: ?string}>
     */
    public static function requiredSpecFor(?string $pgClass): array
    {
        if (blank($pgClass)) {
            return [];
        }

        $gateway = SupportedPaymentGateway::query()
            ->where('pg_class', $pgClass)
            ->first();

        $required = $gateway?->attributes['required'] ?? [];

        $specs = [];

        foreach ($required as $key => $definition) {
            [$type, $extra] = array_pad(explode('|', (string) $definition, 2), 2, null);

            $specs[$key] = [
                'type' => $type,
                'extra' => $extra,
            ];
        }

        return $specs;
    }

    /**
     * Build the form fields for the given gateway's required attributes,
     * rendered using the field type appropriate for each attribute's datatype.
     *
     * @return array<Component>
     */
    public static function fields(?string $pgClass): array
    {
        $specs = static::requiredSpecFor($pgClass);

        if ($specs === []) {
            return [
                Placeholder::make('attributes_placeholder')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->content('Select a payment gateway above to configure its attributes.'),
            ];
        }

        return collect($specs)
            ->map(fn (array $spec, string $key): Component => static::fieldFor($key, $spec))
            ->values()
            ->all();
    }

    protected static function fieldFor(string $key, array $spec): Component
    {
        $label = static::labelFor($key);
        $statePath = "attributes.{$key}";

        return match ($spec['type']) {
            'boolean' => Toggle::make($statePath)
                ->label($label)
                ->default(false),

            'url' => TextInput::make($statePath)
                ->label($label)
                ->url()
                ->required(),

            'float' => TextInput::make($statePath)
                ->label($label)
                ->numeric()
                ->default($spec['extra'])
                ->required(),

            'radio' => Radio::make($statePath)
                ->label($label)
                ->options(static::radioOptions($spec['extra']))
                ->default($spec['extra'] ? Str::of($spec['extra'])->before(',')->toString() : null)
                ->required(),

            default => TextInput::make($statePath)
                ->label($label)
                ->default($spec['extra'])
                ->required(),
        };
    }

    /**
     * Validate a submitted attributes array against the required schema for
     * the given gateway. Returns a map of "attributes.key" => error message
     * for every attribute that is missing or does not match its datatype.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, string>
     */
    public static function validate(?string $pgClass, array $attributes): array
    {
        $errors = [];

        foreach (static::requiredSpecFor($pgClass) as $key => $spec) {
            if ($error = static::validateValue($key, $spec, $attributes[$key] ?? null)) {
                $errors["attributes.{$key}"] = $error;
            }
        }

        return $errors;
    }

    protected static function validateValue(string $key, array $spec, mixed $value): ?string
    {
        $label = static::labelFor($key);

        return match ($spec['type']) {
            'boolean' => is_bool($value) ? null : "{$label} must be true or false.",
            'url' => (is_string($value) && $value !== '' && Str::isUrl($value))
                ? null
                : "{$label} must be a valid URL.",
            'float' => is_numeric($value) ? null : "{$label} must be a number.",
            'radio' => (is_string($value) && array_key_exists($value, static::radioOptions($spec['extra'])))
                ? null
                : "{$label} must be one of: {$spec['extra']}.",
            default => (is_string($value) && $value !== '') ? null : "{$label} is required.",
        };
    }

    /**
     * @return array<string, string>
     */
    protected static function radioOptions(?string $extra): array
    {
        $options = collect(explode(',', (string) $extra))
            ->map(fn (string $option): string => trim($option))
            ->filter()
            ->all();

        return array_combine($options, $options);
    }

    protected static function labelFor(string $key): string
    {
        return (string) Str::of($key)->replace('_', ' ')->headline();
    }
}
