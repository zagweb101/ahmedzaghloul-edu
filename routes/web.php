<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LiveEventController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionCheckoutController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\Admin\SubscriptionOrderController as AdminSubscriptionOrderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LearningPathController as AdminLearningPathController;
use App\Http\Controllers\Admin\LiveEventController as AdminLiveEventController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CommunityController as AdminCommunityController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/learning-paths', [LearningPathController::class, 'index'])->name('learning-paths.index');
Route::get('/learning-paths/{learningPath:slug}', [LearningPathController::class, 'show'])->name('learning-paths.show');
Route::get('/learning-paths/{learningPath:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show');
Route::get('/live-events', [LiveEventController::class, 'index'])->name('live-events.index');
Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::post('/payments/tap/webhook', [PaymentCallbackController::class, 'tapWebhook'])->name('payments.tap.webhook');
Route::get('/payments/tap/return', [PaymentCallbackController::class, 'tapReturn'])->name('payments.tap.return');
Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/gallery', [CommunityController::class, 'gallery'])->name('community.gallery');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/dashboard/avatar', [DashboardController::class, 'updateAvatar'])->name('dashboard.avatar.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::get('/community/posts/{communityPost}/edit', [CommunityController::class, 'edit'])->name('community.edit');
    Route::put('/community/posts/{communityPost}', [CommunityController::class, 'update'])->name('community.update');
    Route::post('/community/{communityPost}/comments', [CommunityController::class, 'comment'])->name('community.comments.store');
    Route::post('/community/{communityPost}/like', [CommunityController::class, 'toggleLike'])->name('community.likes.toggle');
    Route::delete('/community/{communityPost}', [CommunityController::class, 'destroy'])->name('community.destroy');
    Route::post('/learning-paths/{learningPath:slug}/lessons/{lesson:slug}/complete', [LessonController::class, 'complete'])->name('lessons.complete');
    Route::get('/learning-paths/{learningPath:slug}/lessons/{lesson:slug}/pdf', [LessonController::class, 'downloadPdf'])->name('lessons.pdf');
    Route::post('/live-events/{liveEvent}/register', [LiveEventController::class, 'register'])->name('live-events.register');
    Route::delete('/live-events/{liveEvent}/register', [LiveEventController::class, 'cancel'])->name('live-events.cancel');
    Route::get('/subscription-plans/{subscriptionPlan:slug}/checkout', [SubscriptionCheckoutController::class, 'show'])->name('subscription-plans.checkout');
    Route::post('/subscription-plans/{subscriptionPlan:slug}/checkout', [SubscriptionCheckoutController::class, 'store'])->name('subscription-plans.checkout.store');
    Route::get('/subscription-orders/{subscriptionOrder}', [SubscriptionCheckoutController::class, 'showOrder'])->name('subscription-orders.show');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/learning-paths', [AdminLearningPathController::class, 'index'])->name('learning-paths.index');
        Route::get('/learning-paths/create', [AdminLearningPathController::class, 'create'])->name('learning-paths.create');
        Route::post('/learning-paths', [AdminLearningPathController::class, 'store'])->name('learning-paths.store');
        Route::get('/learning-paths/{learningPath}/edit', [AdminLearningPathController::class, 'edit'])->name('learning-paths.edit');
        Route::put('/learning-paths/{learningPath}', [AdminLearningPathController::class, 'update'])->name('learning-paths.update');
        Route::delete('/learning-paths/{learningPath}', [AdminLearningPathController::class, 'destroy'])->name('learning-paths.destroy');
        Route::get('/learning-paths/{learningPath}/lessons', [AdminLessonController::class, 'index'])->name('learning-paths.lessons.index');
        Route::get('/learning-paths/{learningPath}/lessons/create', [AdminLessonController::class, 'create'])->name('learning-paths.lessons.create');
        Route::post('/learning-paths/{learningPath}/lessons', [AdminLessonController::class, 'store'])->name('learning-paths.lessons.store');
        Route::get('/learning-paths/{learningPath}/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('learning-paths.lessons.edit');
        Route::put('/learning-paths/{learningPath}/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('learning-paths.lessons.update');
        Route::delete('/learning-paths/{learningPath}/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('learning-paths.lessons.destroy');
        Route::get('/live-events', [AdminLiveEventController::class, 'index'])->name('live-events.index');
        Route::get('/live-events/create', [AdminLiveEventController::class, 'create'])->name('live-events.create');
        Route::post('/live-events', [AdminLiveEventController::class, 'store'])->name('live-events.store');
        Route::get('/live-events/{liveEvent}/edit', [AdminLiveEventController::class, 'edit'])->name('live-events.edit');
        Route::put('/live-events/{liveEvent}', [AdminLiveEventController::class, 'update'])->name('live-events.update');
        Route::delete('/live-events/{liveEvent}', [AdminLiveEventController::class, 'destroy'])->name('live-events.destroy');
        Route::get('/subscription-plans', [AdminSubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('/subscription-orders', [AdminSubscriptionOrderController::class, 'index'])->name('subscription-orders.index');
        Route::post('/subscription-orders/{subscriptionOrder}/approve', [AdminSubscriptionOrderController::class, 'approve'])->name('subscription-orders.approve');
        Route::post('/subscription-orders/{subscriptionOrder}/cancel', [AdminSubscriptionOrderController::class, 'cancel'])->name('subscription-orders.cancel');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/subscription', [AdminUserController::class, 'grantSubscription'])->name('users.subscription.store');
        Route::get('/community', [AdminCommunityController::class, 'index'])->name('community.index');
        Route::patch('/community/{communityPost}/pinned', [AdminCommunityController::class, 'togglePinned'])->name('community.pinned.toggle');
        Route::patch('/community/{communityPost}/published', [AdminCommunityController::class, 'togglePublished'])->name('community.published.toggle');
        Route::delete('/community/{communityPost}', [AdminCommunityController::class, 'destroy'])->name('community.destroy');
    });
