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

        // SEARCH KET QUA THEO ??I???U KI???N OR
//        $params = [
//            "from" => 0, // B???t ?????u t??? 0
//            'size' => 2,
//            'index' => 'user_index,post_index',
//            'body'  => [
//                'query' => [
//                    'bool' => [
//                        'should' => [ // must => AND, should => OR
//                            ['match' => ['name' => 'user1'] ],
//                            ['match' => ['content' => 'post by user2 content 2']] // match 1 trong c??c t???
//                        ],
//                    ]
//                ]
//            ]
//        ];

        $params = [
            "from" => 0, // B???t ?????u t??? 0
            'size' => 2,
            'index' => 'user_index,post_index',
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => [ // must => AND, should => OR
                            ['match' => ['name' => 'user1'] ],
                            ['match_phrase' => ['content' => 'post by user2 content 2']] // match 1 to??n b??? c???m t???
                        ],
                    ]
                ]
            ]
        ];


        // SEARCH TRONG CHU???I C?? CH??NH X??C M???T T???, match kh??ng d??ng ???????c v???i d???u *
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

//        // SEARCH K???T QU??? C?? CH???A
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

        //  SEARCH CH??NH X??C THEO NH???U ??I???U KI???N
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

    public function groupByState(){
        $params = [
            'index' => 'bank',
            'body'  => [
                'size' => 0,
                'aggs' => [
                    'group_by_state' => [
                        'terms' => [ // must => AND, should => OR
                            'field' => "state.keyword",
                        ],
                    ]
                ]
            ]
        ];

        $client = ClientBuilder::create()->build();
        $response = $client->search($params);
        return response()->json($response);

    }

    public function groupByStateAverage(){
        $params = [
            'index' => 'bank',
            'body'  => [
                'size' => 0,
                'aggs' => [
                    'group_by_state' => [
                        'terms' => [ // must => AND, should => OR
                            'field' => "state.keyword",
                        ],
                        'aggs' => [
                            'average_balanceee' =>[
                                'avg' => [
                                    'field' => 'balance'
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ];

        $client = ClientBuilder::create()->build();
        $response = $client->search($params);
        return response()->json($response);
    }
}
