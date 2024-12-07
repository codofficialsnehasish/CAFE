<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Category;

class CategoeyAPI extends Controller
{
    public function index(string $id = null){
        if($id != null){
            $categories = Category::where('is_visible',1)->where('parent_id',$id)->with('media')->get();
        }else{
            $categories = Category::where('is_visible',1)->where('parent_id',null)->with('media')->get();
        }

        $categories->each(function($categorie) {
            $categorie->image_url = $categorie->getFirstMediaUrl('category');
        });

        return response()->json([
            'status' => 'true',
            'data' =>  $categories,
        ]);
    }
}