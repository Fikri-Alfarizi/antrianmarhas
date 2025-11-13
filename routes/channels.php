<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The required channels may be registered here
| and others may be registered in your service providers.
|
*/

// Public channels
Broadcast::channel('antrian-channel', function ($user) {
    return true; // Public channel
});

Broadcast::channel('antrian-tracking', function ($user) {
    return true; // Public channel
});

// Private channels for messages
Broadcast::channel('loket-{id}', function ($user, $id) {
    // Allow if user is assigned to this loket or is admin
    return $user->role === 'admin' || $user->loket_id == $id;
});

Broadcast::channel('admin-messages.{id}', function ($user, $id) {
    // Allow if the authenticated user is this admin
    return $user->id == $id && $user->role === 'admin';
});

Broadcast::channel('petugas-messages.{id}', function ($user, $id) {
    // Allow if the authenticated user is this petugas
    return $user->id == $id && $user->role === 'petugas';
});
