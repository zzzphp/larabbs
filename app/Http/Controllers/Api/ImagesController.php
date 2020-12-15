<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Support\Str;

class ImagesController extends Controller
{
    // 上传图片
    public function store(ImageRequest $request, ImageUploadHandler $uploadHandler, Image $image)
    {
        $user = $request->user();
        $size = $request->type == 'avatar' ? 416 : 1024;
        $result = $uploadHandler->save($request->image, Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return new ImageResource($image);
    }
}
