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
        $params = [
            'index' => 'user_index',
            'body'  => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];
        $client = ClientBuilder::create()->build();
        $client->indices()->create($params);
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
        $params = [
            'index' => 'post_index',
            'body'  => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];
        $client = ClientBuilder::create()->build();
        $client->indices()->create($params);
        for ($i = 1; $i < 100; $i++){
            $post = new Post();
            $post->name = 'post' . $i;
            $post->content = 'post by user' . $i . ' content ' . $i;
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

        // SEARCH KET QUA THEO ĐIỀU KIỆN OR
        $params = [
            "from" => 0, // Bắt đầu từ 0
            'size' => 2,
            'index' => 'user_index,post_index',
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => [ // must => AND, should => OR
                            ['match' => ['name' => 'user1'] ],
                            ['match' => ['content' => 'post by user2 content 2']]
                        ],
                    ]
                ]
            ]
        ];


        // SEARCH TRONG CHUỖI CÓ CHÍNH XÁC MỘT TỪ, match không dùng được với dấu *
//        $params = [
//            'index' => 'user_index,post_index',
//            'body'  => [
//                'query' => [
//                    'multi_match' => [
//                        'query' => 'user11',
//                        'fields' => ['name', 'content']
//                    ]
//                ]
//            ]
//        ];

//        // SEARCH KẾT QUẢ CÓ CHỨA
//        $params = [
//            'index' => 'user_index,post_index',
//            'body'  => [
//                'query' => [
//                    'bool' => [
//                        'should' => [ // must => AND, should => OR
//                            'query_string' => [
//                                'query' => '*user1*',
//                                'fields' => ['name', 'content']
//                            ]
//                        ],
//                    ]
//                ]
//            ]
//        ];

        //  SEARCH CHÍNH XÁC THEO NHỀU ĐIỀU KIỆN
//        $params = [
//            'index' => 'user_index,post_index',
//            'body'  => [
//                'query' => [
//                    'bool' => [
//                        'should' => [ // must => AND, should => OR
//                            [
//                                'query_string' => [
//                                    'query' => 'user1',
//                                    'fields' => ['name', 'content']
//                                ]
//                            ],
//                            [
//                                'query_string' => [
//                                    'query' => 'user15',
//                                    'fields' => ['name', 'content']
//                                ]
//                            ],
//
//                        ],
//                    ]
//                ]
//            ]
//        ];

        $client = ClientBuilder::create()->build();
        $response = $client->search($params);
        return response()->json($response);
    }
}
