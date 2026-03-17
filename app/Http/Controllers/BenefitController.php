<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BenefitController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $categorySlug = $request->input('category');

        $categories = Category::where('active', true)
            ->orderBy('sort_order')
            ->get();

        $partnersQuery = Partner::with(['category', 'benefits' => fn ($q) => $q->where('active', true)->orderBy('sort_order')])
            ->where('active', true)
            ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('slug', $categorySlug)))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"));

        $partners = $partnersQuery->orderBy('name')->paginate(12)->withQueryString();

        return Inertia::render('benefits/Index', [
            'partners' => $partners,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
            ],
        ]);
    }

    public function show(Partner $partner): Response
    {
        $partner->load([
            'category',
            'benefits' => fn ($q) => $q->where('active', true)->orderBy('sort_order'),
        ]);

        return Inertia::render('benefits/PartnerDetail', [
            'partner' => $partner,
        ]);
    }
}
