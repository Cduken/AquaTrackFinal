<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a new notification and store it in the database
     */
    public static function createNotification($data)
    {
        try {
            return Notification::create([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'action_url' => $data['action_url'] ?? null,
                'important' => $data['important'] ?? false,
                'data' => $data['data'] ?? null,
                'created_at' => $data['created_at'] ?? now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage(), $data);
            return null;
        }
    }

    /**
     * Create notifications for multiple users
     */
    public static function createBulkNotifications($users, $data)
    {
        $notifications = [];
        foreach ($users as $user) {
            $userId = $user instanceof User ? $user->id : $user;
            $notifications[] = [
                'user_id' => $userId,
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'action_url' => $data['action_url'] ?? null,
                'important' => $data['important'] ?? false,
                'data' => $data['data'] ?? null,
                'created_at' => $data['created_at'] ?? now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);
    }

    /**
     * Create report-related notification
     */
    public static function createReportNotification($report, $type = 'status_update')
    {
        $user = $report->user;
        if (!$user) return null;

        $titles = [
            'status_update' => 'Report Status Updated',
            'new_report' => 'New Report Submitted',
            'assigned' => 'Report Assigned'
        ];

        $messages = [
            'status_update' => "Your report #{$report->tracking_code} status has been updated to: " . ucfirst($report->status),
            'new_report' => "New water quality report has been submitted",
            'assigned' => "A new report has been assigned to you"
        ];

        $actionUrls = [
            'status_update' => '/customer/reports',
            'new_report' => '/admin/reports',
            'assigned' => '/staff/dashboard'
        ];

        return self::createNotification([
            'user_id' => $user->id,
            'title' => $titles[$type] ?? 'Report Notification',
            'message' => $messages[$type] ?? 'Report update',
            'type' => 'info',
            'action_url' => $actionUrls[$type] ?? '/notifications',
            'important' => true,
            'data' => [
                'report_id' => $report->id,
                'tracking_code' => $report->tracking_code,
                'notification_type' => $type
            ]
        ]);
    }

    /**
     * Create bill-related notification
     */
    public static function createBillNotification($meterReading, $type = 'due_soon')
    {
        $user = $meterReading->user;
        if (!$user) return null;

        $titles = [
            'overdue' => 'Bill Overdue',
            'due_soon' => 'Bill Due Soon',
            'final_reminder' => 'Final Reminder',
            'due_today' => 'Bill Due Today',
            'amount_updated' => 'Bill Amount Updated'
        ];

        $messages = [
            'overdue' => "🚨 Your bill for {$meterReading->billing_month} is overdue. Amount: ₱" . number_format($meterReading->amount, 2),
            'due_soon' => "📅 Reminder: Your bill for {$meterReading->billing_month} is due soon. Amount: ₱" . number_format($meterReading->amount, 2),
            'final_reminder' => "⏰ Final reminder: Your bill for {$meterReading->billing_month} is due soon. Amount: ₱" . number_format($meterReading->amount, 2),
            'due_today' => "⚠️ Your bill for {$meterReading->billing_month} is due today! Amount: ₱" . number_format($meterReading->amount, 2),
            'amount_updated' => "Your bill for {$meterReading->billing_month} has been updated. New amount: ₱" . number_format($meterReading->amount, 2)
        ];

        return self::createNotification([
            'user_id' => $user->id,
            'title' => $titles[$type] ?? 'Bill Notification',
            'message' => $messages[$type] ?? 'Bill update',
            'type' => in_array($type, ['overdue', 'due_today']) ? 'alert' : 'reminder',
            'action_url' => '/customer/usage',
            'important' => in_array($type, ['overdue', 'due_today']),
            'data' => [
                'meter_reading_id' => $meterReading->id,
                'billing_month' => $meterReading->billing_month,
                'amount' => $meterReading->amount,
                'notification_type' => $type
            ]
        ]);
    }

    /**
     * Create announcement notification
     */
    public static function createAnnouncementNotification($announcement, $users)
    {
        $notifications = [];
        foreach ($users as $user) {
            $notifications[] = [
                'user_id' => $user->id,
                'title' => $announcement->title ?? 'Announcement',
                'message' => $announcement->content ?? 'No content available',
                'type' => 'announcement',
                'action_url' => '/customer/announcements',
                'important' => false,
                'data' => [
                    'announcement_id' => $announcement->id,
                    'target_audience' => $announcement->target_audience
                ],
                'created_at' => $announcement->created_at ?? now()
            ];
        }

        Notification::insert($notifications);
    }
}
