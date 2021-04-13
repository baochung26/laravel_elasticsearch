<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Elasticsearch\ClientBuilder;
use Elasticsearch;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request){
        $params = [
            'index' => 'user_index',
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => 'user1'
                    ]
                ]
            ]
        ];
        $client = ClientBuilder::create()->build();
        $response = $client->search($params);
//        $response = Elasticsearch::search($params);
        return response()->json($response);
    }

    public function indexUser(Request $request){
        for ($i = 1; $i < 100; $i++){
            $user = new User();
            $user->name = 'user' . $i;
            $user->email = 'user' . $i . '@example.com';
            $user->phone = '0123456789';
            $user->password = Hash::make('123456');
            $user->save();
        }
        return 'oke';
    }

    public function indexPost(Request $request){
        for ($i = 1; $i < 100; $i++){
            $post = new Post();
            $post->name = 'post' . $i;
            $post->content = 'post content ' . $i;
            $post->user_id = 50;
            $post->save();
        }
        return 'oke';
    }

    public function deleteUserIndex(Request $request){
        $deleteParams = [
            'index' => 'user_index,post_index'
        ];
        $client = ClientBuilder::create()->build();
        $response = $client->indices()->delete($deleteParams);
        return response()->json($response);
    }

    public function searchMultiIndex(Request $request){
        $params = [
            'index' => 'user_index,post_index',
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['match' => ['name' => 'user1'] ],
                            ['match' => ['content' => 'post content 2'] ]
                        ],
                    ]
                ]
            ]
        ];
        $client = ClientBuilder::create()->build();
        $response = $client->search($params);
        return response()->json($response);
    }
}
