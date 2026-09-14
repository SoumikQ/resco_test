<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting by key with a fallback
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/update a setting by key
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Return all settings as a key => value array
     */
    public static function getAllSettings(): array
    {
        $defaults = [
            'restaurant_name' => 'The Grand Royal Restaurant',
            'tagline' => 'Finest Culinary & Dining Experience',
            'currency' => '₹',
            'phone' => '+91 98765 43210',
            'email' => 'info@grandroyal.in',
            'address' => '12 Park Street, Kolkata, West Bengal - 700016',
            'gstin' => '',
            'invoice_footer' => 'Thank you for dining with us! Please visit again.',
            'bg_pattern' => 'red',
            'bg_pattern_opacity' => '0.06',
            'printer_paper_size' => '58mm',
            'direct_print_enabled' => '1',
            'direct_printer_name' => '',
        ];

        try {
            $settings = static::pluck('value', 'key')->toArray();
            return array_merge($defaults, $settings);
        } catch (\Throwable $e) {
            return $defaults;
        }
    }
}
