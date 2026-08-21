<?php
use App\Http\Controllers\AdminAuthController; use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAccountController; use App\Http\Controllers\AdminProjectController; use App\Http\Controllers\AdminReleaseController; use App\Http\Controllers\AdminReviewController; use App\Http\Controllers\ReleaseDownloadController;
use App\Http\Controllers\NovaosAdminController;
use App\Http\Controllers\NovaosReleaseController; use App\Livewire\Hub; use Illuminate\Support\Facades\Route;
Route::get('/',Hub::class)->name('hub');
Route::get('/download/{release}',ReleaseDownloadController::class)->name('releases.download');
Route::get('/admin/login',[AdminAuthController::class,'showLogin'])->name('admin.login');
Route::post('/admin/login',[AdminAuthController::class,'login'])->name('admin.login.submit');
Route::post('/admin/logout',[AdminAuthController::class,'logout'])->name('admin.logout');
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
 Route::get('/',[AdminDashboardController::class,'index'])->name('dashboard');
 Route::get('/account',[AdminAccountController::class,'edit'])->name('account');
 Route::put('/account',[AdminAccountController::class,'update'])->name('account.update');
 Route::get('/projects',[AdminProjectController::class,'index'])->name('projects.index');
 Route::get('/projects/create',[AdminProjectController::class,'create'])->name('projects.create');
 Route::post('/projects',[AdminProjectController::class,'store'])->name('projects.store');
 Route::get('/projects/{project}/edit',[AdminProjectController::class,'edit'])->name('projects.edit');
 Route::put('/projects/{project}',[AdminProjectController::class,'update'])->name('projects.update');
 Route::delete('/projects/{project}',[AdminProjectController::class,'destroy'])->name('projects.destroy');
 Route::get('/releases',[AdminReleaseController::class,'index'])->name('releases.index');
 Route::get('/releases/create',[AdminReleaseController::class,'create'])->name('releases.create');
 Route::post('/releases',[AdminReleaseController::class,'store'])->name('releases.store');
 Route::get('/releases/{release}/edit',[AdminReleaseController::class,'edit'])->name('releases.edit');
 Route::put('/releases/{release}',[AdminReleaseController::class,'update'])->name('releases.update');
 Route::post('/releases/{release}/toggle',[AdminReleaseController::class,'toggle'])->name('releases.toggle');
 Route::delete('/releases/{release}',[AdminReleaseController::class,'destroy'])->name('releases.destroy');
 Route::get('/novaos',[NovaosAdminController::class,'index'])->name('novaos.index');
 Route::get('/novaos/releases',[NovaosReleaseController::class,'index'])->name('novaos.releases.index');
 Route::get('/novaos/releases/create',[NovaosReleaseController::class,'create'])->name('novaos.releases.create');
 Route::post('/novaos/releases',[NovaosReleaseController::class,'store'])->name('novaos.releases.store');
 Route::get('/novaos/releases/{release}/edit',[NovaosReleaseController::class,'edit'])->name('novaos.releases.edit');
 Route::put('/novaos/releases/{release}',[NovaosReleaseController::class,'update'])->name('novaos.releases.update');
 Route::post('/novaos/releases/{release}/toggle',[NovaosReleaseController::class,'toggle'])->name('novaos.releases.toggle');
 Route::delete('/novaos/releases/{release}',[NovaosReleaseController::class,'destroy'])->name('novaos.releases.destroy');
 Route::get('/reviews',[AdminReviewController::class,'index'])->name('reviews.index');
 Route::post('/reviews/{review}/toggle',[AdminReviewController::class,'toggle'])->name('reviews.toggle');
 Route::delete('/reviews/{review}',[AdminReviewController::class,'destroy'])->name('reviews.destroy');
});


// Public documentation
Route::get('/docs', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('docs.index');
Route::get('/docs/search', [\App\Http\Controllers\DocumentationController::class, 'search'])->name('docs.search');
Route::get('/docs/{project:slug}/print', [\App\Http\Controllers\DocumentationController::class, 'print'])->name('docs.print');
Route::get('/docs/{project:slug}/download.md', [\App\Http\Controllers\DocumentationController::class, 'markdown'])->name('docs.markdown');
Route::get('/docs/{project:slug}/{pageSlug}', [\App\Http\Controllers\DocumentationController::class, 'page'])->name('docs.page');
Route::get('/docs/{project:slug}', [\App\Http\Controllers\DocumentationController::class, 'project'])->name('docs.project');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/documentation', [\App\Http\Controllers\AdminDocumentationController::class, 'index'])->name('documentation.index');
    Route::get('/documentation/{project:slug}', [\App\Http\Controllers\AdminDocumentationController::class, 'project'])->name('documentation.project');
    Route::post('/documentation/{project:slug}/sections', [\App\Http\Controllers\AdminDocumentationController::class, 'storeSection'])->name('documentation.sections.store');
    Route::put('/documentation/sections/{section}', [\App\Http\Controllers\AdminDocumentationController::class, 'updateSection'])->name('documentation.sections.update');
    Route::delete('/documentation/sections/{section}', [\App\Http\Controllers\AdminDocumentationController::class, 'destroySection'])->name('documentation.sections.destroy');
    Route::get('/documentation/{project:slug}/pages/create', [\App\Http\Controllers\AdminDocumentationController::class, 'createPage'])->name('documentation.pages.create');
    Route::post('/documentation/{project:slug}/pages', [\App\Http\Controllers\AdminDocumentationController::class, 'storePage'])->name('documentation.pages.store');
    Route::get('/documentation/pages/{page}/edit', [\App\Http\Controllers\AdminDocumentationController::class, 'editPage'])->name('documentation.pages.edit');
    Route::put('/documentation/pages/{page}', [\App\Http\Controllers\AdminDocumentationController::class, 'updatePage'])->name('documentation.pages.update');
    Route::post('/documentation/pages/{page}/toggle', [\App\Http\Controllers\AdminDocumentationController::class, 'togglePage'])->name('documentation.pages.toggle');
    Route::delete('/documentation/pages/{page}', [\App\Http\Controllers\AdminDocumentationController::class, 'destroyPage'])->name('documentation.pages.destroy');
});
