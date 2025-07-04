<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('message.1', function ($user, $id) {
    return true;
});
