<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Announcements;
use App\Models\DismissedNotification;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Get notifications for admin users (Inertia response)
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return redirect()->route('login');
        }

        $notifications = $this->getAdminNotifications();

        $notifications = $notifications->sortByDesc(function ($item) {
            return $item['created_at'] ?? $item['updated_at'] ?? now();
        })->values();

        // Debug log
        Log::info('Admin notifications sent to page', [
            'count' => $notifications->count(),
            'user_id' => $user->id
        ]);

        return Inertia::render('Admin/Notifications', [
            'notifications' => $notifications,
            'filters' => $request->only(['search', 'sort', 'order']),
        ]);
    }

    /**
     * Get notifications for staff users (Inertia response)
     */
    public function staffIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('staff')) {
            return redirect()->route('login');
        }

        $notifications = $this->getStaffNotifications();

        $notifications = $notifications->sortByDesc(function ($item) {
            return $item['created_at'] ?? $item['updated_at'] ?? now();
        })->values();

        // Debug log
        Log::info('Staff notifications sent to page', [
            'count' => $notifications->count(),
            'user_id' => $user->id
        ]);

        return Inertia::render('Staff/Notifications', [
            'notifications' => $notifications,
            'filters' => $request->only(['search', 'sort', 'order']),
        ]);
    }

    /**
     * Get notifications for customer users (Inertia response)
     */
    public function customerIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'customer') {
            return redirect()->route('login');
        }

        Log::info('Fetching customer notifications for page', ['user_id' => $user->id]);

        $notifications = collect();
        try {
            $notifications = $this->getCustomerNotifications();
        } catch (\Exception $e) {
            Log::error('Error fetching customer notifications: ' . $e->getMessage());
            $notifications = collect();
        }

        $notifications = $notifications->sortByDesc(function ($item) {
            return $item['created_at'] ?? $item['updated_at'] ?? now();
        })->values();

        // Debug log - check what's being sent
        Log::info('Customer notifications sent to page', [
            'count' => $notifications->count(),
            'user_id' => $user->id,
            'sample_notifications' => $notifications->take(3)->toArray()
        ]);

        return Inertia::render('Customer/Notifications', [
            'notifications' => $notifications,
            'filters' => $request->only(['search', 'sort', 'order']),
        ]);
    }

    /**
     * Get notifications for API calls (JSON response only)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        Log::info('Fetching API notifications for user', ['user_id' => $user->id, 'role' => $user->role]);

        $notifications = collect();
        try {
            switch ($user->role) {
                case 'admin':
                    $notifications = $this->getAdminNotifications();
                    break;
                case 'staff':
                    $notifications = $this->getStaffNotifications();
                    break;
                default:
                    $notifications = $this->getCustomerNotifications();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching API notifications: ' . $e->getMessage());
            $notifications = collect();
        }

        Log::info('API Notifications fetched', ['count' => $notifications->count()]);

        // Convert to array format expected by the modal
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'title' => $notification['title'] ?? $this->getDefaultTitle($notification['type']),
                'message' => $notification['message'],
                'due_date' => $notification['due_date'] ?? null,
                'created_at' => $notification['created_at'],
                'unread' => $notification['unread'] ?? true,
                'important' => $notification['important'] ?? false,
                'action_url' => $notification['action_url'] ?? $this->getDefaultActionUrl($notification['type']),
            ];
        })->values()->toArray();

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => collect($formattedNotifications)->where('unread', true)->count()
        ]);
    }

    /**
     * Improved getCustomerNotifications method with better debugging
     */
    private function getCustomerNotifications()
    {
        $notifications = collect();
        $user = Auth::user();

        Log::info('Getting customer notifications for user: ' . $user->id);

        // 1. Report status updates
        $userReports = Report::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->get();

        Log::info('Customer reports found: ' . $userReports->count());

        foreach ($userReports as $report) {
            $notification = [
                'id' => 'report_status_' . $report->id . '_' . $report->updated_at->timestamp,
                'type' => 'info',
                'title' => 'Report Status Updated',
                'message' => "Your report #{$report->tracking_code} status has been updated to: " . ucfirst($report->status),
                'action_url' => '/customer/reports',
                'unread' => !$report->viewed_by_user,
                'created_at' => $report->updated_at->toISOString(),
                'important' => true
            ];
            $notifications->push($notification);
            Log::info('Added report notification', $notification);
        }

        // 2. Bill amount updates
        $updatedBills = MeterReading::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->get();

        Log::info('Customer bill updates found: ' . $updatedBills->count());

        foreach ($updatedBills as $bill) {
            $notification = [
                'id' => 'bill_updated_' . $bill->id . '_' . $bill->updated_at->timestamp,
                'type' => 'alert',
                'title' => 'Bill Amount Updated',
                'message' => "Your bill for {$bill->billing_month} has been updated. New amount: ₱" . number_format($bill->amount, 2),
                'action_url' => '/customer/usage',
                'unread' => (bool) $bill->amount_updated,
                'created_at' => $bill->updated_at->toISOString(),
                'important' => true
            ];
            $notifications->push($notification);
        }

        // 3. Staff viewed water consumption
        $staffViewedReadings = MeterReading::where('user_id', $user->id)
            ->where('staff_viewed_at', '>=', now()->subDays(7))
            ->latest('staff_viewed_at')
            ->get();

        Log::info('Staff viewed readings found: ' . $staffViewedReadings->count());

        foreach ($staffViewedReadings as $reading) {
            $notification = [
                'id' => 'staff_viewed_' . $reading->id . '_' . $reading->staff_viewed_at->timestamp,
                'type' => 'info',
                'title' => 'Water Consumption Reviewed',
                'message' => "Your water consumption for {$reading->billing_month} has been reviewed by our staff",
                'action_url' => '/customer/usage',
                'unread' => (bool) $reading->viewed_by_staff,
                'created_at' => $reading->staff_viewed_at->toISOString(),
                'important' => false
            ];
            $notifications->push($notification);
        }

        // 4. Regular bill notifications (overdue, due soon, etc.)
        $billNotifications = $this->getBillNotifications();
        Log::info('Bill notifications found: ' . $billNotifications->count());
        $notifications = $notifications->merge($billNotifications);

        // 5. Customer announcements
        $announcements = Announcements::where('active', true)
            ->where(function ($query) {
                $query->where('target_audience', 'customer')
                    ->orWhere('target_audience', 'all');
            })
            ->latest()
            ->get();

        Log::info('Customer announcements found: ' . $announcements->count());

        foreach ($announcements as $announcement) {
            $message = $announcement->content ?? $announcement->message ?? $announcement->description ?? 'No content available';

            $notification = [
                'id' => 'announcement_' . $announcement->id,
                'type' => 'info',
                'title' => $announcement->title ?? 'Announcement',
                'message' => $message,
                'action_url' => '/customer/announcements',
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString(),
                'important' => false
            ];
            $notifications->push($notification);
        }

        Log::info('Final customer notifications count', [
            'total' => $notifications->count(),
            'unread' => $notifications->where('unread', true)->count(),
            'types' => $notifications->groupBy('type')->map->count()
        ]);

        return $notifications;
    }


    /**
     * Get bill-related notifications for customers
     */
    private function getBillNotifications()
    {
        $notifications = collect();
        $user = Auth::user();

        // Only for customers
        if ($user->role !== 'customer') {
            return $notifications;
        }

        $today = now();

        // Get user's unpaid meter readings
        $meterReadings = MeterReading::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'Pending')
                    ->orWhere('status', 'Overdue');
            })
            ->whereNotNull('due_date')
            ->get();

        foreach ($meterReadings as $reading) {
            $dueDate = Carbon::parse($reading->due_date);
            $daysUntilDue = $today->diffInDays($dueDate, false);

            // Overdue notification
            if ($daysUntilDue < 0) {
                $overdueDays = abs($daysUntilDue);
                $notifications->push([
                    'id' => 'overdue_bill_' . $reading->id,
                    'type' => 'alert',
                    'title' => 'Bill Overdue',
                    'message' => "🚨 Your bill for {$reading->billing_month} is {$overdueDays} day(s) overdue. Amount: ₱" . number_format($reading->amount, 2),
                    'action_url' => '/customer/usage',
                    'unread' => !$this->isNotificationDismissed('overdue_bill_' . $reading->id),
                    'created_at' => $today->toISOString(),
                    'important' => true
                ]);
            }
            // Due in 5 days notification
            elseif ($daysUntilDue <= 5 && $daysUntilDue > 2) {
                $notifications->push([
                    'id' => 'due_soon_' . $reading->id,
                    'type' => 'reminder',
                    'title' => 'Bill Due Soon',
                    'message' => "📅 Reminder: Your bill for {$reading->billing_month} is due in {$daysUntilDue} days. Amount: ₱" . number_format($reading->amount, 2),
                    'action_url' => '/customer/usage',
                    'unread' => !$this->isNotificationDismissed('due_soon_' . $reading->id),
                    'created_at' => $today->toISOString(),
                    'important' => true
                ]);
            }
            // Final reminder
            elseif ($daysUntilDue <= 2 && $daysUntilDue > 0) {
                $notifications->push([
                    'id' => 'final_reminder_' . $reading->id,
                    'type' => 'reminder',
                    'title' => 'Final Reminder',
                    'message' => "⏰ Final reminder: Your bill for {$reading->billing_month} is due in {$daysUntilDue} days. Amount: ₱" . number_format($reading->amount, 2),
                    'action_url' => '/customer/usage',
                    'unread' => !$this->isNotificationDismissed('final_reminder_' . $reading->id),
                    'created_at' => $today->toISOString(),
                    'important' => true
                ]);
            }
            // Due today notification
            elseif ($daysUntilDue === 0) {
                $notifications->push([
                    'id' => 'due_today_' . $reading->id,
                    'type' => 'alert',
                    'title' => 'Bill Due Today',
                    'message' => "⚠️ Your bill for {$reading->billing_month} is due today! Amount: ₱" . number_format($reading->amount, 2),
                    'action_url' => '/customer/usage',
                    'unread' => !$this->isNotificationDismissed('due_today_' . $reading->id),
                    'created_at' => $today->toISOString(),
                    'important' => true
                ]);
            }
        }

        return $notifications;
    }

    /**
     * Get notifications for admin users
     */
    private function getAdminNotifications()
    {
        $notifications = collect();

        // New reports from customers/guests
        $newReports = Report::with('user')
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($newReports as $report) {
            $notifications->push([
                'id' => 'new_report_' . $report->id,
                'type' => 'info',
                'title' => 'New Report Submitted',
                'message' => "New water quality report from " . ($report->user ? $report->user->name : ($report->reporter_name ?? 'Guest User')),
                'action_url' => '/admin/reports',
                'unread' => !($report->viewed_by_admin ?? false),
                'created_at' => $report->created_at ? $report->created_at->toISOString() : now()->toISOString(),
                'important' => true
            ]);
        }

        // System announcements
        $announcements = Announcements::where('active', true)
            ->where(function ($query) {
                $query->where('target_audience', 'admin')
                    ->orWhere('target_audience', 'all');
            })
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $notifications->push([
                'id' => 'announcement_' . $announcement->id,
                'type' => 'info',
                'title' => $announcement->title ?? 'Announcement',
                'message' => $announcement->content ?? 'No content',
                'action_url' => '/admin/announcements',
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString(),
                'important' => false
            ]);
        }

        return $notifications;
    }

    /**
     * Get notifications for staff users
     */
    private function getStaffNotifications()
    {
        $notifications = collect();
        $user = Auth::user();

        // Assigned reports
        $assignedReports = Report::with('user')
            ->where('assigned_to', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->get();

        foreach ($assignedReports as $report) {
            $notifications->push([
                'id' => 'assigned_report_' . $report->id,
                'type' => 'info',
                'title' => 'Report Assigned',
                'message' => "Report assigned to you from " . ($report->user ? $report->user->name : ($report->reporter_name ?? 'System')),
                'action_url' => '/staff/dashboard',
                'unread' => !($report->viewed_by_staff ?? false),
                'created_at' => $report->updated_at ? $report->updated_at->toISOString() : now()->toISOString(),
                'important' => true
            ]);
        }

        // Staff announcements
        $announcements = Announcements::where('active', true)
            ->where(function ($query) {
                $query->where('target_audience', 'staff')
                    ->orWhere('target_audience', 'all');
            })
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $notifications->push([
                'id' => 'announcement_' . $announcement->id,
                'type' => 'info',
                'title' => $announcement->title ?? 'Announcement',
                'message' => $announcement->message ?? 'No message',
                'action_url' => '/staff/dashboard',
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString(),
                'important' => false
            ]);
        }

        return $notifications;
    }

    /**
     * Get notifications for customer users
     */
    // In your getCustomerNotifications method, add:
    // private function getCustomerNotifications()
    // {
    //     $notifications = collect();
    //     $user = Auth::user();

    //     Log::info('Getting customer notifications for user: ' . $user->id);

    //     // 1. Report status updates - REMOVE THE viewed_by_user FILTER
    //     $userReports = Report::where('user_id', $user->id)
    //         ->where('updated_at', '>=', now()->subDays(7))
    //         ->latest('updated_at')
    //         ->get();

    //     foreach ($userReports as $report) {
    //         $notifications->push([
    //             'id' => 'report_status_' . $report->id . '_' . $report->updated_at->timestamp,
    //             'type' => 'info',
    //             'title' => 'Report Status Updated',
    //             'message' => "Your report #{$report->tracking_code} status has been updated to: " . ucfirst($report->status),
    //             'action_url' => '/customer/reports',
    //             'unread' => !$report->viewed_by_user, // Use this to control read/unread display
    //             'created_at' => $report->updated_at->toISOString(),
    //             'important' => true
    //         ]);
    //     }

    //     // 2. Bill amount updates - REMOVE THE amount_updated FILTER
    //     $updatedBills = MeterReading::where('user_id', $user->id)
    //         ->where('updated_at', '>=', now()->subDays(7))
    //         ->latest('updated_at')
    //         ->get();

    //     foreach ($updatedBills as $bill) {
    //         $notifications->push([
    //             'id' => 'bill_updated_' . $bill->id . '_' . $bill->updated_at->timestamp,
    //             'type' => 'alert',
    //             'title' => 'Bill Amount Updated',
    //             'message' => "Your bill for {$bill->billing_month} has been updated. New amount: ₱" . number_format($bill->amount, 2),
    //             'action_url' => '/customer/usage',
    //             'unread' => $bill->amount_updated, // Use this to control read/unread display
    //             'created_at' => $bill->updated_at->toISOString(),
    //             'important' => true
    //         ]);
    //     }

    //     // 3. Staff viewed water consumption - REMOVE THE viewed_by_staff FILTER
    //     $staffViewedReadings = MeterReading::where('user_id', $user->id)
    //         ->where('staff_viewed_at', '>=', now()->subDays(7))
    //         ->latest('staff_viewed_at')
    //         ->get();

    //     foreach ($staffViewedReadings as $reading) {
    //         $notifications->push([
    //             'id' => 'staff_viewed_' . $reading->id . '_' . $reading->staff_viewed_at->timestamp,
    //             'type' => 'info',
    //             'title' => 'Water Consumption Reviewed',
    //             'message' => "Your water consumption for {$reading->billing_month} has been reviewed by our staff",
    //             'action_url' => '/customer/usage',
    //             'unread' => $reading->viewed_by_staff, // Use this to control read/unread display
    //             'created_at' => $reading->staff_viewed_at->toISOString(),
    //             'important' => false
    //         ]);
    //     }

    //     // 4. Regular bill notifications (overdue, due soon, etc.)
    //     $billNotifications = $this->getBillNotifications();
    //     $notifications = $notifications->merge($billNotifications);

    //     // 5. Customer announcements
    //     $announcements = Announcements::where('active', true)
    //         ->where(function ($query) {
    //             $query->where('target_audience', 'customer')
    //                 ->orWhere('target_audience', 'all');
    //         })
    //         ->latest()
    //         ->get();

    //     foreach ($announcements as $announcement) {
    //         $message = $announcement->content ?? $announcement->message ?? $announcement->description ?? 'No content available';

    //         $notifications->push([
    //             'id' => 'announcement_' . $announcement->id,
    //             'type' => 'info',
    //             'title' => $announcement->title ?? 'Announcement',
    //             'message' => $message,
    //             'action_url' => '/customer/announcements',
    //             'unread' => !$this->isAnnouncementRead($announcement->id),
    //             'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString(),
    //             'important' => false
    //         ]);
    //     }

    //     Log::info('Customer notifications fetched', [
    //         'total' => $notifications->count(),
    //         'unread' => $notifications->where('unread', true)->count()
    //     ]);

    //     return $notifications;
    // }


    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        try {
            if (str_starts_with($id, 'report_status_')) {
                // Extract report ID from: report_status_{id}_{timestamp}
                $parts = explode('_', $id);
                $reportId = $parts[2]; // The report ID is at index 2

                $report = Report::find($reportId);

                if ($report && $report->user_id == $user->id) {
                    $report->viewed_by_user = true;
                    $report->save();

                    Log::info('Report notification marked as read', [
                        'report_id' => $reportId,
                        'user_id' => $user->id,
                        'notification_id' => $id
                    ]);
                }
            } elseif (str_starts_with($id, 'bill_updated_')) {
                $parts = explode('_', $id);
                $billId = $parts[2];
                $bill = MeterReading::find($billId);

                if ($bill && $bill->user_id == $user->id) {
                    $bill->amount_updated = false;
                    $bill->save();
                }
            } elseif (str_starts_with($id, 'staff_viewed_')) {
                $parts = explode('_', $id);
                $readingId = $parts[2];
                $reading = MeterReading::find($readingId);

                if ($reading && $reading->user_id == $user->id) {
                    $reading->viewed_by_staff = false;
                    $reading->save();
                }
            } elseif (str_starts_with($id, 'announcement_')) {
                $announcementId = (int) str_replace('announcement_', '', $id);
                $this->markAnnouncementAsRead($announcementId);
            } elseif (str_starts_with($id, 'overdue_bill_') || str_starts_with($id, 'due_soon_') || str_starts_with($id, 'final_reminder_') || str_starts_with($id, 'due_today_')) {
                $this->dismissNotification($id);
            }

            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to mark notification as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        try {
            switch ($user->role) {
                case 'admin':
                    Report::where('viewed_by_admin', false)
                        ->orWhere('update_viewed_by_admin', false)
                        ->update([
                            'viewed_by_admin' => true,
                            'update_viewed_by_admin' => true
                        ]);
                    break;
                case 'staff':
                    Report::where('assigned_to', $user->id)
                        ->where('viewed_by_staff', false)
                        ->update(['viewed_by_staff' => true]);
                    break;
                default:
                    Report::where('user_id', $user->id)
                        ->where('viewed_by_user', false)
                        ->update(['viewed_by_user' => true]);
            }

            // Mark all announcements as read
            $this->markAllAnnouncementsAsRead();

            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark all notifications as read'], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::info('Dismissing notification', ['notification_id' => $id, 'user_id' => $user->id]);

            // Store the dismissed notification
            DismissedNotification::create([
                'user_id' => $user->id,
                'notification_id' => $id,
                'type' => $this->getNotificationType($id)
            ]);

            return response()->json(['success' => true, 'message' => 'Notification dismissed successfully']);
        } catch (\Exception $e) {
            Log::error('Notification dismissal failed: ' . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to dismiss notification'], 500);
        }
    }

    /**
     * Helper methods
     */
    private function dismissNotification($notificationId)
    {
        DismissedNotification::create([
            'user_id' => Auth::id(),
            'notification_id' => $notificationId,
            'type' => $this->getNotificationType($notificationId)
        ]);
    }

    /**
     * Get notifications for API calls (used by NotificationModal)
     */
    public function getNotificationsApi(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        Log::info('Fetching API notifications for user', ['user_id' => $user->id, 'role' => $user->role]);

        $notifications = collect();
        try {
            switch ($user->role) {
                case 'admin':
                    $notifications = $this->getAdminNotifications();
                    break;
                case 'staff':
                    $notifications = $this->getStaffNotifications();
                    break;
                default:
                    $notifications = $this->getCustomerNotifications();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching API notifications: ' . $e->getMessage());
            $notifications = collect();
        }

        Log::info('API Notifications fetched', ['count' => $notifications->count()]);

        // Convert to array format expected by the modal
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'title' => $notification['title'] ?? $this->getDefaultTitle($notification['type']),
                'message' => $notification['message'],
                'due_date' => $notification['due_date'] ?? null,
                'created_at' => $notification['created_at'],
                'unread' => $notification['unread'] ?? true,
                'important' => $notification['important'] ?? false,
                'action_url' => $notification['action_url'] ?? $this->getDefaultActionUrl($notification['type']),
            ];
        })->values()->toArray();

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => collect($formattedNotifications)->where('unread', true)->count()
        ]);
    }

    /**
     * Get default action URL based on notification type
     */
    private function getDefaultActionUrl($type)
    {
        $urls = [
            'bill_overdue' => '/customer/usage',
            'bill_due_soon' => '/customer/usage',
            'bill_final_reminder' => '/customer/usage',
            'bill_due_today' => '/customer/usage',
            'new_report' => '/customer/reports',
            'report_update' => '/customer/reports',
            'announcement' => '/customer/announcements',
            'alert' => '/customer/notifications',
            'reminder' => '/customer/notifications',
            'info' => '/customer/notifications',
        ];

        return $urls[$type] ?? '/customer/notifications';
    }

    /**
     * Get default title based on notification type
     */
    private function getDefaultTitle($type)
    {
        $titles = [
            'bill_overdue' => 'Overdue Bill',
            'bill_due_soon' => 'Bill Reminder',
            'bill_final_reminder' => 'Final Reminder',
            'bill_due_today' => 'Bill Due Today',
            'new_report' => 'New Report',
            'report_update' => 'Report Update',
            'announcement' => 'Announcement',
            'alert' => 'Alert',
            'reminder' => 'Reminder',
            'info' => 'Information',
            'system' => 'System Notification',
            'order' => 'Order Update',
        ];

        return $titles[$type] ?? 'Notification';
    }

    private function getNotificationType($notificationId)
    {
        if (str_starts_with($notificationId, 'new_report_')) {
            return 'new_report';
        } elseif (
            str_starts_with($notificationId, 'report_update_') ||
            str_starts_with($notificationId, 'assigned_report_') ||
            str_starts_with($notificationId, 'my_report_')
        ) {
            return 'report_update';
        } elseif (str_starts_with($notificationId, 'announcement_')) {
            return 'announcement';
        } elseif (
            str_starts_with($notificationId, 'overdue_bill_') ||
            str_starts_with($notificationId, 'due_soon_') ||
            str_starts_with($notificationId, 'final_reminder_') ||
            str_starts_with($notificationId, 'due_today_')
        ) {
            return 'bill_notification';
        }

        return 'unknown';
    }

    private function isNotificationDismissed($notificationId)
    {
        return DismissedNotification::where('user_id', Auth::id())
            ->where('notification_id', $notificationId)
            ->exists();
    }

    private function isAnnouncementRead($announcementId)
    {
        return DB::table('announcement_reads')
            ->where('announcement_id', $announcementId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    private function markAnnouncementAsRead($announcementId)
    {
        DB::table('announcement_reads')->updateOrInsert([
            'announcement_id' => $announcementId,
            'user_id' => Auth::id(),
        ], [
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function markAllAnnouncementsAsRead()
    {
        $announcements = Announcements::where('active', true)->get();

        foreach ($announcements as $announcement) {
            $this->markAnnouncementAsRead($announcement->id);
        }
    }
}
