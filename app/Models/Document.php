<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'document_date' => 'date',
        'received_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->reference_number)) {
                $now = now();
                $year = $now->year;
                $month = $now->month;
                $romawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
                
                $prefix = $document->type === 'incoming' ? 'SM' : 'SK';
                
                // Cari dokumen terakhir di tahun ini dan tipe yang sama, yang referensinya mirip format baru
                $latestDoc = self::where('type', $document->type)
                    ->whereYear('created_at', $year)
                    ->where('reference_number', 'LIKE', "ARSIP/{$prefix}/%")
                    ->withTrashed()
                    ->orderBy('id', 'desc')
                    ->first();
                
                $nextNumber = 1;
                if ($latestDoc && $latestDoc->reference_number) {
                    $parts = explode('/', $latestDoc->reference_number);
                    $lastNum = (int) end($parts);
                    if ($lastNum > 0) {
                        $nextNumber = $lastNum + 1;
                    }
                }
                
                $document->reference_number = 'ARSIP/' . $prefix . '/' . $romawi[$month] . '/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
