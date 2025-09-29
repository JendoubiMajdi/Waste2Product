<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use Illuminate\Http\Request;

class DonController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $dons = Don::when($type, fn($q) => $q->where('type', $type))
            ->with('user')
            ->paginate(10);

        return view('back.dons.index', compact('dons', 'type'));
    }

    public function destroy(Don $don)
    {
        $don->delete();
        return redirect()->route('admin.dons.index')->with('success', 'Don deleted.');
    }
}