<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Article;
use App\Models\Apex_player;
use App\Models\Guest_user;
use DB;
use Carbon\Carbon;

class ApexController extends Controller
{

    public function get_articles(Request $request){
        $articles = Article::orderBy('created_at', 'desc')->get();
        $apex_player = Apex_player::where('user_id', $request->user_id)->first();
        return [
            "articles" => $articles,
            "apex_player" => $apex_player,
        ];
    }

    public function post(Request $request){
        Log::debug($request);
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'player_name' => 'required',
                'comment' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }

            $reply_id = $request->reply_id;
            $user_id = $request->player_id;
            if(is_null($user_id)){
                //ゲストユーザーを登録
                $guest_user_model = new Guest_user;
                $guest_user_model->fill([
                    'game_name' => 'apex',
                    'api_token' => Str::random(60),
                ]);
                $guest_user_model->save();
                $user_id = $guest_user_model->id;
            }
            $user_name = $request->player_name;
            $comment = $request->comment;
            $platform = array_filter(explode(',', $request->platform));
            if(!$platform){
                $platform[] = 'all';
            }
            $tag = array_filter(explode(',', $request->tag));

            //返信数の加算
            Article::where('id', $reply_id)->increment('reply_count');

            $article_model = new Article;
            $article_model->fill([
                'reply_id' => $reply_id,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'comment' => $comment,
                'report' => '',
            ]);
            //プラットフォーム
            foreach($platform as $platform_name){
                $article_model->fill([
                    'platform_'.$platform_name => 1,
                ]);
            }
            //タグ
            foreach($tag as $tag_name){
                $article_model->fill([
                    $tag_name => 1,
                ]);
            }
            $article_model->save();
            DB::commit();

            //全記事取得
            $articles = Article::orderBy('created_at', 'desc')->get();
            if(isset($guest_user_model)){
                return [
                    "articles" => $articles,
                    "guest_user" => $guest_user_model
                ];
            } else {
                return ["articles" => $articles];
            }
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }

    //記事削除
    public function delete_article(Request $request){
        Log::debug($request);
        DB::beginTransaction();
        try{
            $article_id = $request->article_id;
            $user_id = $request->user_id;
            $article_model = Article::where('id', $article_id)->where('user_id', $user_id)->first();
            $article_model->delete();
            DB::commit();
            $articles = Article::orderBy('created_at', 'desc')->get();
            return ["articles" => $articles];
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }

    public function get_notice(Request $request){
        return [
            'count' => 2,
        ];
    }

    public function clear_notice(){
        return [
            'count' => 0,
        ];
    }
}
