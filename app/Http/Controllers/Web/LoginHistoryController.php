<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index()
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access. Manager is not allowed here.');
        }

        $histories = \App\Models\LoginHistory::orderBy('created_at', 'desc')->paginate(50);
        return view('admin.login_history', compact('histories'));
    }
}
