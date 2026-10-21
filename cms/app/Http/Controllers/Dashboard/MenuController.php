<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
class MenuController extends Controller {
  public function index()    { return Inertia::render('Dashboard/ComingSoon', ['title' => 'MenuController']); }
  public function create()   { return Inertia::render('Dashboard/ComingSoon', ['title' => 'MenuController']); }
  public function store()    { return back()->with('success', 'সংরক্ষিত হয়েছে।'); }
  public function show($id)  { return Inertia::render('Dashboard/ComingSoon', ['title' => 'MenuController']); }
  public function edit($id)  { return Inertia::render('Dashboard/ComingSoon', ['title' => 'MenuController']); }
  public function update($id){ return back()->with('success', 'আপডেট হয়েছে।'); }
  public function destroy($id){ return back()->with('success', 'মুছে ফেলা হয়েছে।'); }
  public function saveItems($id){ return back()->with('success', 'মেনু আইটেম সংরক্ষিত হয়েছে।'); }
}
