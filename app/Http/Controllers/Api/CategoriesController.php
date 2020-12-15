<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;
use App\Models\Category;

class CategoriesController extends Controller
{
    //
    public function index()
    {
        CategoriesResource::wrap('data');
        return CategoriesResource::collection(Category::all());
    }
}
