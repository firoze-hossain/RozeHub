<?php
use App\Http\Controllers\AdminAuthController; use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAccountController; use App\Http\Controllers\AdminProjectController; use App\Http\Controllers\AdminReleaseController; use App\Http\Controllers\AdminReviewController; use App\Http\Controllers\ReleaseDownloadController;
use App\Http\Controllers\NovaosAdminController;
use App\Http\Controllers\NovaosReleaseController; use App\Http\Controllers\AdminReleaseUploadController; use App\Livewire\Hub; use App\Models\SoftwareProject; use Illuminate\Support\Facades\Route;
Route::get('/',Hub::class)->name('hub');
Route::get('/download/{release}',ReleaseDownloadController::class)->middleware('throttle:30,1')->name('releases.download');
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
 Route::post('/release-uploads/start',[AdminReleaseUploadController::class,'start'])->name('release-uploads.start');
 Route::post('/release-uploads/chunk',[AdminReleaseUploadController::class,'chunk'])->name('release-uploads.chunk');
 Route::delete('/release-uploads/{token}',[AdminReleaseUploadController::class,'cancel'])->name('release-uploads.cancel');
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


// Public desktop update API. No login is required.
Route::get('/api/v1/updates/{project:slug}', [\App\Http\Controllers\Api\UpdateController::class, 'check'])->middleware('throttle:60,1')->name('api.updates.check');
Route::get('/api/v1/updates/{project:slug}/releases', [\App\Http\Controllers\Api\UpdateController::class, 'releases'])->middleware('throttle:60,1')->name('api.updates.releases');
Route::get('/api/v1/releases/{release}/download', [\App\Http\Controllers\Api\UpdateController::class, 'download'])->middleware('throttle:30,1')->name('api.updates.download');


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


// Public RozeHub Marketplace: plugins and extensions for desktop applications.
Route::get('/marketplace', [\App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{item:slug}', [\App\Http\Controllers\MarketplaceController::class, 'item'])->name('marketplace.item');
Route::middleware('auth')->post('/marketplace/{item:slug}/reviews', [\App\Http\Controllers\MarketplaceReviewController::class, 'store'])->name('marketplace.reviews.store');

// Public marketplace API used by Lumina and DBNavigator.
Route::get('/api/v1/marketplace/{project}', [\App\Http\Controllers\Api\MarketplaceController::class, 'index'])
    ->middleware('throttle:60,1')->name('api.marketplace.index');
Route::get('/api/v1/marketplace/{project}/{item:slug}', [\App\Http\Controllers\Api\MarketplaceController::class, 'item'])
    ->middleware('throttle:60,1')->name('api.marketplace.item');
Route::get('/api/v1/marketplace/releases/{release}/download', [\App\Http\Controllers\Api\MarketplaceController::class, 'download'])
    ->middleware('throttle:30,1')->name('api.marketplace.download');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/marketplace/categories', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'index'])->name('marketplace.categories');
    Route::post('/marketplace/categories', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'store'])->name('marketplace.categories.store');
    Route::put('/marketplace/categories/{category}', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'update'])->name('marketplace.categories.update');
    Route::delete('/marketplace/categories/{category}', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'destroy'])->name('marketplace.categories.destroy');
    Route::get('/marketplace', [\App\Http\Controllers\AdminMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/create', [\App\Http\Controllers\AdminMarketplaceController::class, 'create'])->name('marketplace.create');
    Route::post('/marketplace', [\App\Http\Controllers\AdminMarketplaceController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/{item}/edit', [\App\Http\Controllers\AdminMarketplaceController::class, 'edit'])->name('marketplace.edit');
    Route::put('/marketplace/{item}', [\App\Http\Controllers\AdminMarketplaceController::class, 'update'])->name('marketplace.update');
    Route::delete('/marketplace/{item}', [\App\Http\Controllers\AdminMarketplaceController::class, 'destroy'])->name('marketplace.destroy');

    Route::get('/marketplace/{item}/releases', [\App\Http\Controllers\AdminMarketplaceController::class, 'releases'])->name('marketplace.releases.index');
    Route::get('/marketplace/{item}/releases/create', [\App\Http\Controllers\AdminMarketplaceController::class, 'createRelease'])->name('marketplace.releases.create');
    Route::post('/marketplace/{item}/releases', [\App\Http\Controllers\AdminMarketplaceController::class, 'storeRelease'])->name('marketplace.releases.store');
    Route::get('/marketplace/releases/{release}/edit', [\App\Http\Controllers\AdminMarketplaceController::class, 'editRelease'])->name('marketplace.releases.edit');
    Route::put('/marketplace/releases/{release}', [\App\Http\Controllers\AdminMarketplaceController::class, 'updateRelease'])->name('marketplace.releases.update');
    Route::post('/marketplace/releases/{release}/toggle', [\App\Http\Controllers\AdminMarketplaceController::class, 'toggleRelease'])->name('marketplace.releases.toggle');
    Route::delete('/marketplace/releases/{release}', [\App\Http\Controllers\AdminMarketplaceController::class, 'destroyRelease'])->name('marketplace.releases.destroy');
});


// Community developer portal. Only authenticated developers can create submissions.
Route::get('/developer/login', [\App\Http\Controllers\DeveloperAuthController::class, 'loginForm'])->name('developer.login');
Route::post('/developer/login', [\App\Http\Controllers\DeveloperAuthController::class, 'login'])->name('developer.login.submit');
Route::get('/developer/register', [\App\Http\Controllers\DeveloperAuthController::class, 'registerForm'])->name('developer.register');
Route::post('/developer/register', [\App\Http\Controllers\DeveloperAuthController::class, 'register'])->name('developer.register.submit');
Route::middleware('auth')->prefix('developer')->name('developer.')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\DeveloperAuthController::class, 'logout'])->name('logout');
    Route::get('/', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'dashboard'])->name('dashboard');
    Route::get('/publisher', [\App\Http\Controllers\DeveloperPublisherController::class, 'edit'])->name('publisher.edit');
    Route::put('/publisher', [\App\Http\Controllers\DeveloperPublisherController::class, 'update'])->name('publisher.update');
    Route::get('/notifications', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'readNotification'])->name('notifications.read');
    Route::get('/marketplace/create', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'create'])->name('marketplace.create');
    Route::post('/marketplace', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/{item}/edit', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'edit'])->name('marketplace.edit');
    Route::put('/marketplace/{item}', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'update'])->name('marketplace.update');
    Route::get('/marketplace/{item}/releases/create', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'createRelease'])->name('marketplace.releases.create');
    Route::post('/marketplace/{item}/releases', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'storeRelease'])->name('marketplace.releases.store');
    Route::get('/marketplace/releases/{release}/edit', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'editRelease'])->name('marketplace.release.edit');
    Route::put('/marketplace/releases/{release}', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'updateRelease'])->name('marketplace.release.update');
    Route::post('/marketplace/releases/{release}/submit', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'submit'])->name('marketplace.submit');
    Route::get('/marketplace/submissions', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'submissions'])->name('marketplace.submissions');
    Route::get('/marketplace/submissions/{submission}', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'submission'])->name('marketplace.submission');
    Route::post('/marketplace/submissions/{submission}/resubmit', [\App\Http\Controllers\DeveloperMarketplaceController::class, 'resubmit'])->name('marketplace.resubmit');
    Route::post('/uploads/start', [\App\Http\Controllers\AdminReleaseUploadController::class, 'start'])->name('uploads.start');
    Route::post('/uploads/chunk', [\App\Http\Controllers\AdminReleaseUploadController::class, 'chunk'])->name('uploads.chunk');
    Route::delete('/uploads/{token}', [\App\Http\Controllers\AdminReleaseUploadController::class, 'cancel'])->name('uploads.cancel');
});

// Admin ecosystem policy management.
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ecosystem', [\App\Http\Controllers\AdminEcosystemController::class, 'index'])->name('ecosystem.index');
    Route::get('/ecosystem/{project}/edit', [\App\Http\Controllers\AdminEcosystemController::class, 'edit'])->name('ecosystem.edit');
    Route::put('/ecosystem/{project}', [\App\Http\Controllers\AdminEcosystemController::class, 'update'])->name('ecosystem.update');
});

// Admin marketplace moderation queue.
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/marketplace/review', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'index'])->name('marketplace.review.index');
    Route::get('/marketplace/review/{submission}', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'show'])->name('marketplace.review.show');
    Route::post('/marketplace/review/{submission}/start', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'start'])->name('marketplace.review.start');
    Route::patch('/marketplace/review/{submission}/risk', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'updateRisk'])->name('marketplace.review.risk');
    Route::post('/marketplace/review/{submission}/decision', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'decide'])->name('marketplace.review.decide');
    Route::post('/marketplace/review/{submission}/unpublish', [\App\Http\Controllers\AdminMarketplaceReviewController::class, 'unpublish'])->name('marketplace.review.unpublish');
});

// Phase 3 — GitHub ecosystem.
Route::get('/projects/{project:slug}/contribute', [\App\Http\Controllers\GithubController::class, 'contribute'])->name('github.contribute');
Route::post('/github/webhook', [\App\Http\Controllers\GithubController::class, 'webhook'])->name('github.webhook');
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/projects/{project}/github', [\App\Http\Controllers\AdminGithubController::class, 'show'])->name('github.show');
    Route::post('/projects/{project}/github/sync', [\App\Http\Controllers\AdminGithubController::class, 'sync'])->name('github.sync');
    Route::get('/projects/{project}/github/documentation', [\App\Http\Controllers\AdminGithubController::class, 'editDocumentation'])->name('github.documentation');
    Route::put('/projects/{project}/github/documentation', [\App\Http\Controllers\AdminGithubController::class, 'updateDocumentation'])->name('github.documentation.update');
});
Route::get('/api/v1/projects/{project:slug}/github', function(SoftwareProject $project){$repo=$project->githubRepository()->with(['contributors'=>fn($q)=>$q->orderByDesc('contributions')->limit(25),'issues'=>fn($q)=>$q->where('state','open')->orderByDesc('updated_at_github')->limit(20),'pullRequests'=>fn($q)=>$q->where('state','open')->orderByDesc('updated_at_github')->limit(20),'releases'=>fn($q)=>$q->orderByDesc('published_at_github')->limit(10)])->first(); return response()->json(['project'=>$project->only(['name','slug','github_url']),'repository'=>$repo]);})->middleware('throttle:60,1')->name('api.github.project');

// Phase 5 — analytics.
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/analytics', [\App\Http\Controllers\AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/projects/{project:slug}', [\App\Http\Controllers\AdminAnalyticsController::class, 'project'])->name('analytics.project');
});

// Phase 4 — release platform.
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/release-platform', [\App\Http\Controllers\AdminReleasePlatformController::class, 'index'])->name('release-platform.index');
    Route::post('/projects/{project}/release-platform/github-sync', [\App\Http\Controllers\AdminReleasePlatformController::class, 'syncGithub'])->name('release-platform.github-sync');
    Route::post('/release-platform/{release}/process', [\App\Http\Controllers\AdminReleasePlatformController::class, 'process'])->name('release-platform.process');
    Route::post('/release-platform/{release}/health', [\App\Http\Controllers\AdminReleasePlatformController::class, 'health'])->name('release-platform.health');
    Route::post('/release-platform/{release}/rollback', [\App\Http\Controllers\AdminReleasePlatformController::class, 'rollback'])->name('release-platform.rollback');
    Route::get('/projects/{project}/release-channels', [\App\Http\Controllers\AdminReleasePlatformController::class, 'channels'])->name('release-channels.index');
    Route::post('/projects/{project}/release-channels', [\App\Http\Controllers\AdminReleasePlatformController::class, 'storeChannel'])->name('release-channels.store');
    Route::put('/projects/{project}/release-channels/{channel}', [\App\Http\Controllers\AdminReleasePlatformController::class, 'updateChannel'])->name('release-channels.update');
    Route::delete('/projects/{project}/release-channels/{channel}', [\App\Http\Controllers\AdminReleasePlatformController::class, 'destroyChannel'])->name('release-channels.destroy');
});
Route::middleware('auth')->prefix('api/v1')->group(function(){
    Route::get('/release-notifications',[\App\Http\Controllers\ReleaseNotificationController::class,'index'])->name('api.release-notifications');
    Route::post('/release-notifications/{notification}/read',[\App\Http\Controllers\ReleaseNotificationController::class,'read'])->name('api.release-notifications.read');
    Route::post('/release-notifications/read-all',[\App\Http\Controllers\ReleaseNotificationController::class,'readAll'])->name('api.release-notifications.read-all');
});
