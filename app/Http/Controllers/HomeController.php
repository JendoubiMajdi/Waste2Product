<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index() { return view('home'); }
    public function about() { return view('about'); }
    public function services() { return view('services'); }
    public function donation() { return view('donation'); }
    public function event() { return view('event'); }
    public function feature() { return view('feature'); }
    public function team() { return view('team'); }
    public function testimonial() { return view('testimonial'); }
    public function contact() { return view('contact'); }
    public function notFound() { return view('404'); }
}
