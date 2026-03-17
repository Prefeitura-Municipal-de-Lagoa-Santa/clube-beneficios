<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BenefitController extends Controller
{
    public function index(Request $request): Response
    {
        $benefits = Benefit::with('partner')
            ->when($request->input('partner_id'), fn ($q, $id) => $q->where('partner_id', $id))
            ->when($request->input('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/benefits/Index', [
            'benefits' => $benefits,
            'partners' => Partner::where('active', true)->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('search', 'partner_id'),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('admin/benefits/Form', [
            'partners' => Partner::where('active', true)->orderBy('name')->get(['id', 'name']),
            'partnerId' => $request->input('partner_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        Benefit::create($validated);

        return redirect()->route('admin.benefits.index')->with('success', 'Benefício cadastrado com sucesso!');
    }

    public function edit(Benefit $benefit): Response
    {
        return Inertia::render('admin/benefits/Form', [
            'benefit' => $benefit->load('partner'),
            'partners' => Partner::where('active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Benefit $benefit): RedirectResponse
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $benefit->update($validated);

        return redirect()->route('admin.benefits.index')->with('success', 'Benefício atualizado com sucesso!');
    }

    public function destroy(Benefit $benefit): RedirectResponse
    {
        $benefit->delete();

        return redirect()->route('admin.benefits.index')->with('success', 'Benefício excluído com sucesso!');
    }
}
