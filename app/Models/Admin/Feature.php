<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'name', 'description', 'image', 'image_position', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $path = 'storage/services/' . $model->image;
            if (\File::exists(public_path() . '/' . $path)) {
                \File::delete($path);
            }
        });
    }

    // ************************** //
    //  Append Extra Attributes   //
    // ************************** //

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        return $this->attributes['image_path'] = checkImage(asset('storage/services/' . $this->image), 'line-2-img.png');
    }

    public function FeatureLabels()
    {
        return $this->hasMany('App\Models\Admin\FeatureLabel', 'feature_id');
    }
}
