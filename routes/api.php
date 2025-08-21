<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuctionDashboardController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;

// =================== User Auth ===================
Route::get('/auth/google-login', [UserAuthController::class, 'handleGoogleCallback']);
Route::post('/auth/logout', [UserAuthController::class, 'logout']);
Route::middleware('jwt.cookie:api')->group(function () {
    Route::get('/auth/user', [UserAuthController::class, 'getUser']);
});

// =================== Admin Auth ===================
Route::post('/auth/admin/create', [AdminAuthController::class, 'createAdmin']);
Route::post('/auth/admin/login', [AdminAuthController::class, 'loginAdmin']);
Route::middleware('jwt.cookie:admin')->group(function () {
    Route::get('/auth/admin/profile', [AdminAuthController::class, 'getAdmin']);
    Route::post('/auth/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/users', [AdminAuthController::class, 'getAllUsers']);
});

Route::middleware('jwt.cookie:api')->group(function () {
    // =================== Auction Routes ===================
    Route::prefix('/auction')->group(function () {
        Route::post('/create', [AuctionController::class, 'createAuction']);
        Route::get('/all', [AuctionController::class, 'getAuctions']);
        Route::get('/{auction_id}', [AuctionController::class, 'getAuctionById']);
        Route::put('/update', [AuctionController::class, 'updateAuction']);
        Route::delete('/delete/{auction_id}', [AuctionController::class, 'deleteAuction']);

        Route::put('/sold-player', [AuctionDashboardController::class, 'soldPlayer']);
        Route::put('/unsold-player', [AuctionDashboardController::class, 'unsoldPlayer']);
        Route::post('/unsold-to-sold', [AuctionDashboardController::class, 'unsoldToSold']);
    });

    // =================== Player Routes ===================
    Route::prefix('player')->group(function () {
        Route::post('/create', [PlayerController::class, 'createPlayer']);
        Route::get('/all', [PlayerController::class, 'getPlayers']);
        Route::get('/team', [PlayerController::class, 'getPlayersByTeam']);
        Route::get('/{player_id}', [PlayerController::class, 'getPlayerById']);
        Route::put('/update', [PlayerController::class, 'updatePlayer']);
        Route::put('/update/minimum-bid', [PlayerController::class, 'updateMinimumBid']);
        Route::delete('/delete/{player_id}', [PlayerController::class, 'deletePlayer']);
    });

    // =================== Team Routes ===================
    Route::prefix('team')->group(function () {
        Route::post('/create', [TeamController::class, 'createTeam']);
        Route::get('/all', [TeamController::class, 'getTeams']);
        Route::get('/{team_id}', [TeamController::class, 'getTeamById']);
        Route::put('/update', [TeamController::class, 'updateTeam']);
        Route::delete('/delete/{team_id}', [TeamController::class, 'deleteTeam']);
    });
});

// =================== Admin-specific Auction & Player ===================
Route::middleware('jwt.cookie:admin')->group(function () {
    Route::get('/admin/auctions', [AuctionController::class, 'getAuctions']);
    Route::get('/admin/players/all', [PlayerController::class, 'getPlayers']);
});
