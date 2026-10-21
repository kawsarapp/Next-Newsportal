<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
class ProfileController extends Controller {
  public function index()    { return Inertia::render('Dashboard/ComingSoon', ['title' => 'ProfileController']); }
  public function create()   { return Inertia::render('Dashboard/ComingSoon', ['title' => 'ProfileController']); }
  public function store()    { return back()->with('success', 'সংরক্ষিত হয়েছে।'); }
  public function show($id)  { return Inertia::render('Dashboard/ComingSoon', ['title' => 'ProfileController']); }
  public function edit($id)  { return Inertia::render('Dashboard/ComingSoon', ['title' => 'ProfileController']); }
  public function update($id){ return back()->with('success', 'আপডেট হয়েছে।'); }
  public function destroy($id){ return back()->with('success', 'মুছে ফেলা হয়েছে।'); }
  public function updatePassword(){ return back()->with('success', 'পাসওয়ার্ড আপডেট করা হয়েছে।'); }
  public function enable2fa(){ return back()->with('success', '২-ফ্যাক্টর অথেনটিকেশন সক্রিয় করা হয়েছে।'); }
  public function disable2fa(){ return back()->with('success', '২-ফ্যাক্টর অথেনটিকেশন নিষ্ক্রিয় করা হয়েছে।'); }
  public function confirm2fa(){ return back()->with('success', '২-ফ্যাক্টর কোড যাচাই সম্পন্ন হয়েছে।'); }
}
