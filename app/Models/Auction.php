<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $table = 'auctions';
    protected $primaryKey = 'auction_id'; // Set custom primary key
    public $incrementing = false; // Since auction_id is string, not auto-incrementing
    protected $keyType = 'string'; // Primary key is string type

    protected $fillable = [
        'auction_id',
        'user_id',
        'auction_logo',
        'auction_name',
        'auction_date',
        'sports_type',
        'point_perteam',
        'minimum_bid',
        'bid_increment',
        'player_perteam',
        'status',
    ];

    // Relations

    /**
     * Auction belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Auction has many Teams
     */
    public function teams()
    {
        return $this->hasMany(Team::class, 'auction_id');
    }

    /**
     * Auction has many Players
     */
    public function players()
    {
        return $this->hasMany(Player::class, 'auction_id');
    }

    // auto-generate 12-char ID when creating user
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->auction_id)) {
                $model->auction_id = self::generateAuctionId();
            }
        });
    }

    private static function generateAuctionId()
    {
        return substr(str_shuffle(str_repeat(
            '0123456789',
            12
        )), 0, 12);
    }
}