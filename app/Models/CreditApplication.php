<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CreditApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'nik',
        'vehicle_price',
        'ktp_image_base64',
        'status',
        'vehicle_type',
        'notes',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'vehicle_price' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function getFormattedVehiclePriceAttribute()
    {
        return 'Rp ' . number_format($this->vehicle_price, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Approved' => 'bg-green-500',
            'Rejected' => 'bg-red-500',
            default => 'bg-yellow-500'
        };
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}