<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    // otomatis ikut dikirim ke view/JSON
    protected $appends = ['image_url'];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];

    /** Relasi ke kategori (pakai model Categories) */
    public function category(): BelongsTo
    {
        // pastikan model Categories ada di App\Models\Categories
        return $this->belongsTo(Categories::class, 'category_id');
    }

    /**
     * Accessor URL gambar publik.
     * - Jika kolom image kosong → pakai placeholder.
     * - Jika sudah URL penuh (http/https) → kembalikan apa adanya.
     * - Jika path storage/public → generasikan URL /storage/...
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/placeholder.png'); // siapkan file ini di public/images
        }

        // Jika sudah berupa URL eksternal
        if (preg_match('/^https?:\/\//i', $this->image)) {
            return $this->image;
        }

        // Normalisasi path agar tidak dobel "storage/"
        $path = ltrim($this->image, '/');
        $path = preg_replace('#^storage/#', '', $path);

        // File disimpan di disk 'public' (storage/app/public/...)
        return Storage::disk('public')->url($path); // menghasilkan /storage/...
    }
}