<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Elasticsearch;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function($model){
            self::insertElasticsearchDocument($model);
        });

        self::updated(function($model){
            self::updateElasticsearchDocument($model);
        });

        self::deleted(function($model){
            self::deleteElasticsearchDocument($model);
        });
    }

    public static function insertElasticsearchDocument($model){
        $data = [
            'body' => [
                'name' => $model->name,
                'email' => $model->email
            ],
            'index' => 'user_index',
            'type' => 'my_type',
            'id' => $model->id,
        ];
        Elasticsearch::index($data);
    }

    public static function updateElasticsearchDocument($model){
        $data = [
            'body' => [
                'name' => $model->name,
                'email' => $model->email
            ],
            'index' => 'user_index',
            'type' => 'my_type',
            'id' => $model->id,
        ];
        Elasticsearch::update($data);
    }
    public static function deleteElasticsearchDocument($model){
        $params = [
            'index' => 'user_index',
            'id'    => $model->id
        ];
        Elasticsearch::delete($params);
    }
}
