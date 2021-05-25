<?php

namespace App\Observer;

use App\Mail\WordPressIdolPost;
use App\Models\Idol;
use App\Models\WordPressPost;
use Illuminate\Support\Facades\Mail;

class IdolObserve
{
    public function created(Idol $idol)
    {
        // Send idol post
        if (WordPressPost::where(['title' => $idol->name])->exists()) {
            return;
        }
        Mail::send(new WordPressIdolPost($idol));
        WordPressPost::create(['title' => $idol->name]);
    }
}
