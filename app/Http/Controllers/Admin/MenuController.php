<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'position' => $request->position,
            'is_active' => true,
        ]);

        return redirect()->route('admin.menus.builder', $menu->id)->with('success', __('Menu created successfully.'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $menu->update([
            'name' => $request->name,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', __('Menu updated successfully.'));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', __('Menu deleted successfully.'));
    }

    public function builder(Menu $menu)
    {
        $menuItems = $menu->rootItems()->with('children.children')->get();
        $allMenuItems = $menu->items()->orderBy('title')->get(); // For parent dropdown

        return view('admin.menus.builder', compact('menu', 'menuItems', 'allMenuItems'));
    }

    // AJAX Methods
    public function addItem(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string',
            'url' => 'nullable|string',
            'type' => 'required|string',
            'target' => 'required|string',
            'parent_id' => 'nullable|exists:menu_items,id',
        ]);

        $item = $menu->items()->create([
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->target,
            'type' => $request->type,
            'related_id' => $request->related_id,
            'parent_id' => $request->parent_id,
            'order' => 999, // Append to end
        ]);

        return response()->json([
            'success' => true,
            'item' => $item,
            'html' => view('admin.menus.partials.item', compact('item'))->render(),
        ]);
    }

    public function updateItem(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->update($request->only(['title', 'url', 'target']));

        return response()->json(['success' => true]);
    }

    public function deleteItem($id)
    {
        MenuItem::destroy($id);

        return response()->json(['success' => true]);
    }

    public function sortItems(Request $request)
    {
        $order = $request->input('order');
        if (is_string($order)) {
            $order = json_decode($order, true);
        }

        $this->updateOrder($order, null);

        return response()->json(['success' => true]);
    }

    private function updateOrder($items, $parentId)
    {
        if (! is_array($items)) {
            return;
        }

        foreach ($items as $index => $itemData) {
            $item = MenuItem::find($itemData['id']);
            if ($item) {
                $item->order = $index;
                $item->parent_id = $parentId;
                $item->save();

                if (isset($itemData['children'])) {
                    $this->updateOrder($itemData['children'], $item->id);
                }
            }
        }
    }
}
