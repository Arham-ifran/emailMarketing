<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Package extends Model
{
    protected $fillable = [
        'title',
        'sub_title',
        'icon',
        'type',
        'monthly_price',
        'yearly_price',
        'description',
        'status',
        'recommended'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $path = 'storage/packages/' . $model->icon;
            if (\File::exists(public_path() . '/' . $path)) {
                \File::delete($path);
            }
        });
    }

    public function linkedFeatures()
    {
        return $this->hasMany('App\Models\Admin\PackageLinkFeature', 'package_id');
    }

    public function totalUsers()
    {
        $package_id = $this->id;
        $records = User::select('*')
            ->join('package_subscriptions', function ($join) use ($package_id) {
                $join->on('package_subscriptions.id', '=', 'users.package_subscription_id')
                    ->where('package_subscriptions.package_id', '=', $package_id);
            })
            ->get();

        return count($records);
    }
}
