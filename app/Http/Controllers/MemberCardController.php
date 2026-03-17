<?php

namespace App\Http\Controllers;

use App\Models\MemberCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberCardController extends Controller
{
    public function show(Request $request): Response
    {
        $card = $request->user()->memberCard;

        return Inertia::render('card/Show', [
            'card' => $card,
            'appUrl' => config('app.url'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->memberCard) {
            return back()->with('error', 'Você já possui uma carteirinha.');
        }

        MemberCard::create([
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Carteirinha gerada com sucesso!');
    }
}
