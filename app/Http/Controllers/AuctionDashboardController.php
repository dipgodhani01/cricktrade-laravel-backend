<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class AuctionDashboardController
{
    public function soldPlayer(Request $request)
    {
        try {
            $auction = Auction::where('auction_id', $request->auction_id)->first();
            if (!$auction) {
                return response()->json(['success' => false, 'message' => 'Auction not found'], 404);
            }
            $player = Player::where('player_id', $request->player_id)
                ->where('auction_id', $auction->auction_id)
                ->first();
            if (!$player) {
                return response()->json(['success' => false, 'message' => 'Player not found'], 404);
            }
            $team = Team::where('team_id', $request->sold_team_id)
                ->where('auction_id', $auction->auction_id)
                ->first();
            if (!$team) {
                return response()->json(['success' => false, 'message' => 'Team not found'], 404);
            }

            $player->update([
                'sold_team_id' => $request->sold_team_id,
                'final_bid' => (int) $request->final_bid,
                'sold_team' => $team->team_name,
                'status' => 1,
            ]);
            $playerBuyCount = Player::where('sold_team_id', $request->sold_team_id)
                ->where('auction_id', $auction->auction_id)
                ->where('status', 1)
                ->count();
            $newBalance = $team->remember_balance - $request->final_bid;

            $team->update([
                'remember_balance' => $newBalance,
                'player_buy' => $playerBuyCount,
                'player_remember' => $team->player_allow - $playerBuyCount,
                'reserve_balance' => ($team->player_allow - $playerBuyCount) * $auction->minimum_bid,
                'status' => $team->player_allow === $playerBuyCount ? 1 : $team->status
            ]);

            $allTeamsFilled = Team::where('auction_id', $auction->auction_id)
                ->get()
                ->every(fn($team) => $team->player_allow === $team->player_buy);



            if ($allTeamsFilled) {
                $auction->update(['status' => 1]);
                Player::where('auction_id', $auction->auction_id)
                    ->where('status', 0)
                    ->update(['status' => 2]);
            }

            if (now()->greaterThan($auction->auction_date)) {
                $auction->update(['status' => 2]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Player sold successfully.',
                'data' => [
                    'player' => $player->fresh(),
                    'team' => $team->fresh(),
                    'auction' => $auction->fresh(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function unsoldPlayer(Request $request)
    {
        try {
            $auction = Auction::findOrFail($request->auction_id);

            $player = Player::where('player_id', $request->player_id)
                ->where('auction_id', $auction->auction_id)
                ->firstOrFail();

            $player->update([
                'status' => 2,
            ]);

            $player->save();

            return response()->json([
                'success' => true,
                'message' => 'Unsold',
                'data' => $player,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function unsoldToSold(Request $request)
    {
        $auction_id = $request->query('auction_id');
        try {
            $unsoldCount = Player::where('auction_id', $auction_id)
                ->where('status', 2)
                ->count();

            if ($unsoldCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No unsold players found for this auction.',
                ], 404);
            }
            Player::where('auction_id', $auction_id)
                ->where('status', 2)
                ->update(['status' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'All unsold players are now available for auction.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
