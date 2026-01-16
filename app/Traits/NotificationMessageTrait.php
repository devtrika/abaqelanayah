<?php

namespace App\Traits;

trait NotificationMessageTrait
{

    public function getTitle(array $notification_data, $local = 'ar')
    {
        // Handle simple string title (backward compatibility)
        if(isset($notification_data['title']) && is_string($notification_data['title'])){
            return $notification_data['title'];
        }

        // Handle localized title array
        if(isset($notification_data['title'][$local])){
            return $notification_data['title'][$local];
        }

        // Fallback to other language if current language not available
        if(isset($notification_data['title'])){
            $availableLanguages = array_keys($notification_data['title']);
            if(!empty($availableLanguages)){
                return $notification_data['title'][$availableLanguages[0]];
            }
        }

        // Final fallback to translation
        if(isset($notification_data['type']) && !empty($notification_data['type'])){
            return trans('notification.title_' . $notification_data['type'], [], $local);
        }

        return 'Notification';
    }

    public function getBody(array $notification_data, $local = 'ar')
    {
        // Handle simple string body (backward compatibility)
        if (isset($notification_data['body']) && is_string($notification_data['body'])) {
            return $notification_data['body'];
        }

        // If a localized body array is provided, prefer it for any type
        if (isset($notification_data['body']) && is_array($notification_data['body'])) {
            if (isset($notification_data['body'][$local])) {
                return $notification_data['body'][$local];
            }

            // Fallback to other language if current language not available
            $availableLanguages = array_keys($notification_data['body']);
            if (!empty($availableLanguages)) {
                return $notification_data['body'][$availableLanguages[0]];
            }
        }

        // For admin_notify without explicit body, return a generic message
        if (isset($notification_data['type']) && 'admin_notify' == $notification_data['type']) {
            return 'Notification message';
        }

        // Default: translate based on type and available data
        return $this->transTypeToBody($notification_data, $local);
    }

    private function transTypeToBody($notification_data, $local)
    {
        $transData = [];
        if (isset($notification_data['order_num'])) {
            $transData['order_num'] = $notification_data['order_num'];
        }

        if (isset($notification_data['amount'])) {
            $transData['amount'] = $notification_data['amount'];
        }

        if (isset($notification_data['date'])) {
            $transData['date'] = $notification_data['date'];
        }

        if (isset($notification_data['status'])) {
            $transData['status'] = trans('order.' . $notification_data['status'], [], $local);
        }

        // Check if type exists before using it in translation
        if (isset($notification_data['type']) && !empty($notification_data['type'])) {
            $msg = trans('notification.body_' . $notification_data['type'], $transData, $local);
            return $msg;
        }

        // Fallback message if type is missing
        return 'Notification message';
    }

}
