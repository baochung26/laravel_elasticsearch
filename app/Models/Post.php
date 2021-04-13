<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Elasticsearch;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'user_id',
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
                'content' => $model->content,
                'user_id' => $model->user_id,
            ],
            'index' => 'post_index',
            'type' => 'my_type',
            'id' => $model->id,
        ];
        Elasticsearch::index($data);
    }

    public static function updateElasticsearchDocument($model){
        $data = [
            'body' => [
                'name' => $model->name,
                'content' => $model->content,
                'user_id' => $model->user_id,
            ],
            'index' => 'post_index',
            'type' => 'my_type',
            'id' => $model->id,
        ];
        Elasticsearch::update($data);
    }
    public static function deleteElasticsearchDocument($model){
        $params = [
            'index' => 'post_index',
            'id'    => $model->id
        ];
        Elasticsearch::delete($params);
    }
}
