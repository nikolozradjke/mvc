<?php

namespace app\controllers;

use app\models\Post;
use core\Controller;
use core\Request;
use app\Helper;

class SiteController extends Controller
{

    public function index(){
        $post = new Post();
        $posts_count =  $post::queryBuilder()->table('posts')->select('id')->count();
        $per_page = 3;
        $current_page = $_GET['page'] ?? 1;
        $num_of_pages = $posts_count / $per_page;
        $posts = $post::queryBuilder()->table('posts')->select()->page($current_page - 1, $per_page)->get();
        $params = ['name' => 'title', 'pages' => ceil($num_of_pages), 'posts' => $posts];
        return $this->render('/client/home', $params);
    }

    public function contact(){
        return $this->render('/client/contact');
    }

    public function handleContact(Request $request){
        $body = $request->getBody();

        return $body;
    }
}