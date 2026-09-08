<?php

namespace App\Models;

use Database\Factories\NfcScanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nfc_point_id', 'news_id', 'scanned_at'])]
class NfcScan extends Model
{
    /** @use HasFactory<NfcScanFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<NfcPoint, $this>
     */
    public function nfcPoint(): BelongsTo
    {
        return $this->belongsTo(NfcPoint::class);
    }

    /**
     * @return BelongsTo<News, $this>
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
