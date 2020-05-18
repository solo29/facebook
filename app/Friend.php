<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $guarded = [];
    protected $dates = ['confirmed_at'];


    public static function friendship($anotherUser)
    {
        return (new static())
            ->where(function ($query) use ($anotherUser) {
                return $query->where('user_id', auth()->user()->id)
                    ->where('friend_id', $anotherUser);
            })
            ->orWhere(function ($query) use ($anotherUser) {
                return $query->where('friend_id', auth()->user()->id)
                    ->where('user_id', $anotherUser);
            })
            ->first();
    }
}
