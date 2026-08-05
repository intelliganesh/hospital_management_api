<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MaskId extends Model
{
    public function toArray()
    {
        $array = parent::toArray();
        if (array_key_exists('id', $array)) {
            $array['id'] = $this->masked_id;
        }
        return $array;
    }

    public function getMaskedIdAttribute(): string
    {
        return rtrim(strtr(base64_encode($this->getKey()), '+/', '-_'), '=');
    }

    public static function decodeMaskedId($maskedId): ?int
    {
        $decoded = base64_decode(strtr($maskedId, '-_', '+/'));
        return is_numeric($decoded) ? (int) $decoded : null;
    }

    public static function findByMaskedId($maskedId)
    {
        $id = self::decodeMaskedId($maskedId);
        return $id ? self::find($id) : null;
    }

    public static function findByMaskedIdOrFail($maskedId)
    {
        $id = self::decodeMaskedId($maskedId);
        if (!$id) {
            throw (new ModelNotFoundException)->setModel(static::class);
        }
        return self::findOrFail($id);
    }

    public function scopeFindByMaskedId($query, $maskedId)
    {
        $id = self::decodeMaskedId($maskedId);
        if (!$id) {
            return $query->whereNull('id'); // empty result
        }
        return $query->where('id', $id);
    }
}