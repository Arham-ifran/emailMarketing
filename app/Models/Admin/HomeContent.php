<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    protected $fillable = [
        'name', 'description', 'image', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($model) 
        {
            $path = 'storage/home-contents/'.$model->image;
            if (\File::exists(public_path() . '/' . $path)) 
            {
                \File::delete($path);
            }
        });
    }

    public function homeContentLabels()
    {
        return $this->hasMany('App\Models\Admin\HomeContentLabel', 'home_content_id');
    }

    // ************************** //
    //  Append Extra Attributes   //
    // ************************** //

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        return $this->attributes['image_path'] = checkImage(asset('storage/home-contents/' . $this->image),'placeholder.png');
    }
}
