<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Add newsletter subscription logic here
        return back()->with('success', 'Subscribed successfully!');
    }
}
