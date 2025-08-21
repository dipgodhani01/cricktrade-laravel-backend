<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';
    protected $primaryKey = 'player_id'; // Set custom primary key
    public $incrementing = false; // Since auction_id is string, not auto-incrementing
    protected $keyType = 'string'; // Primary key is string type

    // Mass assignable fields
    protected $fillable = [
        'player_id',
        'index',
        'auction_id',
        'sold_team_id',
        'sold_team',
        'player_logo',
        'player_name',
        'minimum_bid',
        'final_bid',
        'category',
        'phone',
        'tshirt_size',
        'trouser_size',
        'tshirt_name',
        'tshirt_number',
        'status',
    ];

    // Relations

    /**
     * Player belongs to an Auction
     */
    public function auction()
    {
        return $this->belongsTo(Auction::class, 'auction_id');
    }

    /**
     * Player belongs to a Team (sold_team)
     */
    public function soldTeam()
    {
        return $this->belongsTo(Team::class, 'sold_team_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->player_id)) {
                $model->player_id = self::generatePlayerId();
            }
        });
    }

    private static function generatePlayerId()
    {
        return substr(str_shuffle(str_repeat(
            '0123456789',
            12
        )), 0, 12);
    }
}