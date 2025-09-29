<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedbacks = auth()->user()->feedbacks;
        return view('feedbacks.index', compact('feedbacks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('feedbacks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string',
        ]);

        auth()->user()->feedbacks()->create([
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date' => now()->toDateString(),
        ]);

        return redirect()->route('feedbacks.index')->with('success', 'Feedback added!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $feedback = auth()->user()->feedbacks()->findOrFail($id);
        return view('feedbacks.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $feedback = auth()->user()->feedbacks()->findOrFail($id);
        return view('feedbacks.edit', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string',
        ]);

        $feedback = auth()->user()->feedbacks()->findOrFail($id);
        $feedback->update($request->only('note', 'commentaire'));

        return redirect()->route('feedbacks.index')->with('success', 'Feedback updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $feedback = auth()->user()->feedbacks()->findOrFail($id);
        $feedback->delete();

        return redirect()->route('feedbacks.index')->with('success', 'Feedback deleted!');
    }

    /**
     * To get feedbacks for a user
     */
    public function showUserFeedbacks($userId)
    {
        $user = User::findOrFail($userId);
        $feedbacks = $user->feedbacks;
        // return view('feedbacks.index', compact('feedbacks'));
    }
}
