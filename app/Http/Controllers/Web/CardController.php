<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $factory = $user->isFactoryAccount() ? $user->factory_code : $request->query('factory', 'BV');

        // Get all cards for the selected factory
        $cards = Card::with('activeSession')
            ->where('code', 'LIKE', $factory . '%')
            ->orderBy('code')
            ->get();

        $stats = [
            'total' => $cards->count(),
            'available' => $cards->where('status', Card::STATUS_AVAILABLE)->count(),
            'in_use' => $cards->where('status', Card::STATUS_IN_USE)->count(),
        ];

        return view('cards.index', compact('factory', 'cards', 'stats'));
    }
}
