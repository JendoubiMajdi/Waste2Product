<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPoint extends Model
{
    protected $table = 'collection_points';

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'working_hours',
        'contact_phone',
        'status',
        'user_id',
        'image',
    ];
    
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }
    
    /**
     * Get formatted working hours for display
     * Converts "08:00-17:00" to "08:00 AM - 05:00 PM"
     */
    public function getFormattedWorkingHoursAttribute()
    {
        if (!$this->working_hours) {
            return 'Not specified';
        }
        
        $times = explode('-', $this->working_hours);
        if (count($times) !== 2) {
            return $this->working_hours;
        }
        
        try {
            $opening = \Carbon\Carbon::createFromFormat('H:i', trim($times[0]))->format('h:i A');
            $closing = \Carbon\Carbon::createFromFormat('H:i', trim($times[1]))->format('h:i A');
            return $opening . ' - ' . $closing;
        } catch (\Exception $e) {
            return $this->working_hours;
        }
    }
}
