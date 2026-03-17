<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    public function index(Request $request): Response
    {
        $partners = Partner::with('category')
            ->withCount('benefits')
            ->when($request->input('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/partners/Index', [
            'partners' => $partners,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/partners/Form', [
            'categories' => Category::where('active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_url'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);
        Partner::create($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Parceiro cadastrado com sucesso!');
    }

    public function edit(Partner $partner): Response
    {
        return Inertia::render('admin/partners/Form', [
            'partner' => $partner,
            'categories' => Category::where('active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo_url) {
                Storage::disk('public')->delete($partner->logo_url);
            }
            $validated['logo_url'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);
        $partner->update($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Parceiro atualizado com sucesso!');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo_url) {
            Storage::disk('public')->delete($partner->logo_url);
        }

        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Parceiro excluído com sucesso!');
    }
}
