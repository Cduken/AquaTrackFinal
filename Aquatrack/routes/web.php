<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRecordController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\CustomerAnnouncementsController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerUsageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Roles\SelectRolesController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffReadingController;
use App\Http\Controllers\TrackReportController;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing/LandingPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// ==================== AUTHENTICATION ROUTES ====================
// Public customer login (main login page)
Route::get('/customer/login', [AuthenticatedSessionController::class, 'create'])
    ->name('customer.login')
    ->middleware('guest');

// Hidden admin/staff login route (keep URL secret)
Route::get('/system-access', [AuthenticatedSessionController::class, 'staffLogin'])
    ->name('staff.login')
    ->middleware('guest');

// Login submission (handles all types)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Remove the old verify-code route since we're removing verification codes
// Route::post('/verify-code', [AuthenticatedSessionController::class, 'verifyCode'])->name('verify-code');

// ==================== PUBLIC ROUTES ====================
Route::get('/announcements', [AnnouncementsController::class, 'guestIndex'])->name('announcements.guest');
Route::get('/track-report', [TrackReportController::class, 'index'])->name('track-report');
Route::get('/report-issue', [ReportController::class, 'createPage'])->name('report-issue.create');

// Report Routes (Public and Authenticated)
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::match(['get', 'post'], '/reports/track', [ReportController::class, 'track'])->name('reports.track');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/api/reports/find', [ReportController::class, 'findByTrackingCode'])->name('reports.find');
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');

Route::post('/reports/sync-offline', [ReportController::class, 'syncOfflineReports'])->name('reports.sync-offline');

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard redirection
    Route::get('/redirect-to-dashboard', [AuthenticatedSessionController::class, 'redirectToDashboard'])
        ->middleware('verified')
        ->name('redirect-to-dashboard');

    // Generic dashboard access
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('staff')) {
            return redirect()->route('staff.dashboard');
        } elseif ($user->hasRole('customer')) {
            return redirect()->route('customer.dashboard');
        }

        return redirect()->route('home');
    })->name('dashboard')->middleware('verified');

    // Notification Routes
    Route::get('/api/notifications', [NotificationController::class, 'getNotificationsApi'])
        ->name('api.notifications');

    Route::put('/customer/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('customer.notifications.read');

    Route::put('/customer/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('customer.notifications.mark-all-read');

    Route::delete('/customer/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('customer.notifications.destroy');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Reports
    Route::get('/admin/reports', [ReportController::class, 'adminIndex'])->name('admin.reports');
    Route::post('/admin/reports/get-reporters-data', [ReportController::class, 'getReportersData'])->name('admin.reports.getReportersData');
    Route::delete('/admin/reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::post('/admin/reports/{report}/update-status', [ReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');
    Route::get('/admin/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.export.pdf');
    Route::get('/admin/reports/{report}', [ReportController::class, 'adminShow'])->name('admin.reports.show');

    // Report merging
    Route::get('/admin/reports/mergeable', [NotificationController::class, 'getMergeableReports'])->name('admin.reports.mergeable');
    Route::post('/admin/reports/merge', [NotificationController::class, 'mergeReports'])->name('admin.reports.merge');

    // Records
    Route::get('/admin/records', [AdminRecordController::class, 'index'])->name('admin.records.index');
    Route::get('/admin/records/{record}', [AdminRecordController::class, 'show'])->name('admin.records.show');
    Route::get('/admin/records/{record}/edit', [AdminRecordController::class, 'edit'])->name('admin.records.edit');
    Route::put('/admin/records/{record}', [AdminRecordController::class, 'update'])->name('admin.records.update');
    Route::delete('/admin/records/{record}', [AdminRecordController::class, 'destroy'])->name('admin.records.destroy');
    Route::get('/admin/records/{record}/details', [AdminRecordController::class, 'details'])->name('admin.records.details');

    // Record utilities
    Route::get('/admin/records/update-overdue', [AdminRecordController::class, 'manualUpdateOverdue'])
        ->name('admin.records.update-overdue');
    Route::get('/admin/records/force-fix-due-dates', [AdminRecordController::class, 'forceFixDueDates'])
        ->name('admin.records.force-fix-due-dates');
    Route::get('/admin/records/export', [AdminRecordController::class, 'export'])->name('admin.records.export');
    Route::get('/admin/records/period-stats', [AdminRecordController::class, 'getPeriodStats'])->name('admin.records.period-stats');
    Route::get('/admin/records/available-periods', [AdminRecordController::class, 'getAvailablePeriodsApi'])->name('admin.records.available-periods');

    // Users Management
    Route::get('/admin/users', [AdminUsersController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUsersController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUsersController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}', [AdminUsersController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/toggle-status', [AdminUsersController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::put('/admin/users/{user}', [AdminUsersController::class, 'update'])->name('admin.users.update');

    Route::post('/admin/users/{user}/reset-password', [AdminUsersController::class, 'resetPassword'])->name('admin.users.reset-password');

    // Stats
    Route::get('/admin/stats', [AdminUsersController::class, 'getStats'])->name('admin.stats');
    Route::get('/reports/stats', [ReportController::class, 'getStats'])->name('reports.stats');
    Route::post('/admin/export-water-analytics', [AdminDashboardController::class, 'exportWaterAnalytics'])->name('admin.export-water-analytics');

    // Announcements
    Route::get('/admin/announcements', [AnnouncementsController::class, 'index'])->name('announcements');
    Route::post('/admin/announcements', [AnnouncementsController::class, 'store'])->name('announcements.store');
    Route::put('/admin/announcements/{announcement}', [AnnouncementsController::class, 'update'])->name('announcements.update');
    Route::delete('/admin/announcements/{announcement}', [AnnouncementsController::class, 'destroy'])->name('announcements.destroy');

    // Activity Logs
    Route::get('/admin/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity-logs');
    Route::get('/activity-logs/export', [AdminActivityLogController::class, 'export'])->name('admin.activity-logs.export');

    // Notifications
    Route::get('/admin/notifications', [NotificationController::class, 'adminIndex'])->name('admin.notifications.index');
    Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::post('/admin/notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('admin.notifications.bulk-delete');
    Route::post('/admin/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');

    // Customer notifications (admin access)
    Route::delete('/customer/notifications/{id}', [NotificationController::class, 'destroy'])->name('customer.notifications.destroy');
});

// ==================== STAFF ROUTES ====================
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/dashboard/data', [StaffDashboardController::class, 'getDashboardData'])->name('staff.dashboard.data');

    // Meter reading routes
    Route::get('/staff/reading', [StaffReadingController::class, 'index'])->name('staff.reading');
    Route::get('/staff/reading/search', [StaffReadingController::class, 'search'])->name('staff.reading.search');
    Route::get('/staff/reading/user/{userId}', [StaffReadingController::class, 'getUserDetails'])->name('staff.reading.user');
    Route::get('/staff/reading/previous/{userId}', [StaffReadingController::class, 'getPreviousReadings'])->name('staff.reading.previous');
    Route::post('/staff/reading', [StaffReadingController::class, 'storeReading'])->name('staff.reading.store');
    Route::put('/staff/reading/{readingId}/update', [StaffReadingController::class, 'updateReading'])->name('staff.reading.update');

    Route::get('/staff/notifications', [NotificationController::class, 'staffIndex'])->name('staff.notifications');
});

// Previous readings API route (accessible by staff)
Route::get('/staff/readings/{userId}/previous', [StaffReadingController::class, 'getPreviousReadings']);

// ==================== CUSTOMER ROUTES ====================
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/usage', [CustomerUsageController::class, 'index'])->name('customer.usage');
    Route::get('/customer/usage/{month}', [CustomerUsageController::class, 'show'])->name('customer.usage.details');
    Route::get('/customer/reports', [ReportController::class, 'customerIndex'])
        ->middleware('verified')
        ->name('customer.reports');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('customer.reports.show');
    Route::get('/customer/announcements', [CustomerAnnouncementsController::class, 'index'])->name('customer.announcements');

    // Customer notifications
    Route::get('/customer/notifications', [NotificationController::class, 'customerIndex'])->name('customer.notifications');
    Route::delete('/customer/notifications/{id}', [NotificationController::class, 'destroy'])->name('customer.notifications.destroy');
    Route::put('/customer/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('customer.notifications.read');
    Route::put('/customer/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('customer.notifications.mark-all-read');
});

// ==================== LEGACY/DEBUG ROUTES ====================
// Remove or comment out the old select-roles route since we're using separate login forms
Route::get('/select-roles', [SelectRolesController::class, 'index'])->name('select-roles');

Route::get('/debug/customers', function () {
    return User::where('role', 'customer')->get();
});

require __DIR__ . '/auth.php';
