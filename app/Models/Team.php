<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';
    protected $primaryKey = 'team_id'; // Set custom primary key
    public $incrementing = false; // Since auction_id is string, not auto-incrementing
    protected $keyType = 'string'; // Primary key is string type

    // Mass assignable fields
    protected $fillable = [
        'team_id',
        'auction_id',
        'team_logo',
        'team_name',
        'owner',
        'team_name',
        'team_balance',
        'remember_balance',
        'reserve_balance',
        'player_allow',
        'player_buy',
        'player_remember',
        'status',
    ];

    /**
     * Relation: Team belongs to an Auction
     */
    public function auction()
    {
        return $this->belongsTo(Auction::class, 'auction_id');
    }

    // auto-generate 12-char ID when creating user
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->team_id)) {
                $model->team_id = self::generateTeamId();
            }
        });
    }

    private static function generateTeamId()
    {
        return substr(str_shuffle(str_repeat(
            '0123456789',
            12
        )), 0, 12);
    }
}