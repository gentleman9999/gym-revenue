<?php

namespace App\StorableEvents;

use App\Models\User;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class CustomStoredEvent extends EloquentStoredEvent
{
    public static function boot()
    {
        parent::boot();

        static::creating(function (CustomStoredEvent $storedEvent) {
            $user_id = null;
            if (session('user_id')) {
                $user_id = session('user_id');
            }
            $access_token = request()->bearerToken() ?? null;
            $api_user_id = $access_token !== null ? User::whereAccessToken($access_token)->firstOrFail()->id : null;

            $auto_generated = $user_id === null && $access_token === null;

            $storedEvent->meta_data['user_id'] = $user_id;
            $storedEvent->meta_data['api_user_id'] = $api_user_id;
            $storedEvent->meta_data['access_token'] = $access_token;
            $storedEvent->meta_data['auto_generated'] = $auto_generated;
        });
    }

    public function user()
    {
        return User::withoutGlobalScopes()->find($this->meta_data['user_id']);
    }

    public function apiUser()
    {
        return User::withoutGlobalScopes()->whereAccessToken($this->meta_data['access_token'])->first();
    }
}
