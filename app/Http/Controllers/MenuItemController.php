<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    /**
     * List the business menu.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));

        $items = MenuItem::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = MenuItem::query()
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('menu.index', [
            'items' => $items,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
        ]);
    }

    /**
     * Show the form for creating a menu item.
     */
    public function create(): View
    {
        $categories = MenuItem::query()->distinct()->orderBy('category')->pluck('category');

        return view('menu.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created menu item.
     */
    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_available'] = $request->boolean('is_available');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $request->user()->business->menuItems()->create($data);

        return redirect()->route('menu.index')->with('status', 'Producto agregado al menú.');
    }

    /**
     * Show the form for editing a menu item.
     */
    public function edit(MenuItem $menuItem): View
    {
        $categories = MenuItem::query()->distinct()->orderBy('category')->pluck('category');

        return view('menu.edit', [
            'item' => $menuItem,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the given menu item.
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validated();
        $data['is_available'] = $request->boolean('is_available');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $menuItem->update($data);

        return redirect()->route('menu.index')->with('status', 'Producto actualizado.');
    }

    /**
     * Remove the given menu item.
     */
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return redirect()->route('menu.index')->with('status', 'Producto eliminado del menú.');
    }

    /**
     * Toggle the availability of a menu item.
     */
    public function toggleAvailability(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return back()->with('status', $menuItem->is_available
            ? 'Producto marcado como disponible.'
            : 'Producto marcado como no disponible.');
    }
}
