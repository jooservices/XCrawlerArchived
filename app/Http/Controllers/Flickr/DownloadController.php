<?php

namespace App\Http\Controllers\Flickr;

use App\Flickr\Jobs\DownloadJob;
use App\Http\Requests\FlickrDownloadAlbumRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class DownloadController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function download(FlickrDownloadAlbumRequest $request)
    {
        DownloadJob::dispatch($request->input('url'), $request->input('type'), (bool)$request->input('to_wordpress'));
    }
}
