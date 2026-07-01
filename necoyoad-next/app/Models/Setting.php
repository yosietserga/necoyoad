<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'group', 'key', 'value'];
    public function get(string $key, ?int $storeId = null, mixed $default = null): mixed
    {
        $storeId ??= 0;
        $setting = static::where('store_id', $storeId)->where('key', $key)->first();
        return $setting?->value ?? $default;
    }
}
