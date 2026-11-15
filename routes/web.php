<?php

use App\Http\Controllers\PdfController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [SiteController::class, 'locale'])->name('locale');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/capabilities', [SiteController::class, 'capabilities'])->name('capabilities');
Route::get('/capacity', [SiteController::class, 'capacity'])->name('capacity');
Route::get('/certifications', [SiteController::class, 'certifications'])->name('certifications');
Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{style}', [SiteController::class, 'style'])->name('styles.show');
Route::get('/news', [SiteController::class, 'blog'])->name('blog');
Route::get('/news/{post}', [SiteController::class, 'post'])->name('posts.show');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'storeInquiry'])->name('inquiries.store');

Route::middleware('auth')->group(function () {
    Route::get('/documents/cut-tickets/{cutPlan}', [PdfController::class, 'cutTicket'])->name('pdf.cut-ticket');
    Route::get('/documents/invoices/{shipment}', [PdfController::class, 'invoice'])->name('pdf.invoice');
    Route::get('/documents/packing-lists/{shipment}', [PdfController::class, 'packingList'])->name('pdf.packing-list');
});
