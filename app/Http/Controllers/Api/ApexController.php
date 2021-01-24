<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Log;
use App\Models\User;
use App\Models\Article;
use DB;

class ApexController extends Controller
{

    public function get_articles(){
        $articles = Article::orderBy('created_at', 'desc')->get();
        return [
            "articles" => $articles,
        ];
    }

    public function post(Request $request){
        Log::debug($request);
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                // 'player_id' => 'integer',
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
            $user_name = $request->player_name;
            $platform = $request->platform;
            $comment = $request->comment;
            $tag = $request->tag;

            Log::debug($reply_id);
            $article_model = new Article;
            $article_model->fill([
                'reply_id' => $reply_id,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'platform' => $platform,
                'comment' => $comment,
                'report' => '',
            ]);
            $article_model->save();
            DB::commit();

            //全記事取得
            $articles = Article::orderBy('created_at', 'desc')->get();
            return [
                "articles" => $articles,
            ];
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }
}
