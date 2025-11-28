<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    //
    public function index(Request $request)
    {
        $items = Item::all();
        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully',
            'data' => $items,
        ], 200);
    }

    public function store(Request $request)
    {
        $item = Item::create($request->all());
        return response()->json($item);
    }

    public function show($id)
    {
        $item = Item::find($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        return response()->json($item);
    }

    public function userItems(Request $request)
    {
        $items = Item::where('user_id', $request->user()->id)->get();
        return response()->json($items);
    }
}
