<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;


class PostHandler {
    public static function addPost($idUser,$type,$body){
            if (!empty($idUser) && !empty($body)){
                $body = trim($body);
                Post::insert([
                        'id_user'=>$idUser,
                    'type'=>$type,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'body'=>$body
                ])->execute();
            }
    }
    public static function getHomeFeed($idUser){
        //1 - lista de usuarios q sigo
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach ($userList as $userItem){
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        //2 - pegar post da galera por data
        $postList = Post::select()
            ->where('id_user','in',$users)
            ->orderBy('created_at','desc')
            ->get();


        //3 - transformar obj em post
        $posts = [];
        foreach ($postList as $postItem){
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            //4 - preencher as inf adicionais no POst
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];
            //4.1  preencher info likes
            //4.2  preencher info coments

            $posts = $newPost;
        }


        //5 - retornar resultado
        //var_dump($posts);
        return $posts;
    }

}
