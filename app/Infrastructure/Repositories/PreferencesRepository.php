<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Preferences;
use App\Domain\Repositories\PreferencesRepository as PreferencesRepositoryInterface;
use App\Infrastructure\Database\Models\Preference as PreferenceModel;

class PreferencesRepository implements PreferencesRepositoryInterface
{
    public function __construct(
        private readonly PreferenceModel $model
    ) {}

    public function save(Preferences $preferences): void
    {
        $model = $this->model->find($preferences->getId());

        if (!$model) {
            $model = new PreferenceModel();
        }

        $model->user_id = $preferences->getUserId();
        $model->theme = $preferences->getOption('theme');
        $model->language = $preferences->getOption('language');
        $model->currency = $preferences->getOption('currency');
        $model->date_format = $preferences->getOption('date_format');
        $model->time_format = $preferences->getOption('time_format');
        $model->notifications_enabled = $preferences->getOption('notifications_enabled');
        $model->email_notifications = $preferences->getOption('email_notifications');
        $model->sms_notifications = $preferences->getOption('sms_notifications');
        $model->mei_annual_limit = $preferences->getOption('mei_annual_limit');
        $model->mei_alert_threshold = $preferences->getOption('mei_alert_threshold');
        $model->mei_monthly_alert_day = $preferences->getOption('mei_monthly_alert_day');
   
        $model->save();
    }

    public function find(int $userId): ?Preferences
    {
        $model = $this->model->where('user_id', $userId)->first();
        if (!$model) {
            return null;
        }

        $preferences = new Preferences();
        $preferences->setId($model->id);
        $preferences->setUserId($model->user_id);
        $preferences->setOption('theme', $model->theme);
        $preferences->setOption('language', $model->language);
        $preferences->setOption('currency', $model->currency);
        $preferences->setOption('date_format', $model->date_format);
        $preferences->setOption('time_format', $model->time_format);
        $preferences->setOption('notifications_enabled', $model->notifications_enabled);
        $preferences->setOption('email_notifications', $model->email_notifications);
        $preferences->setOption('sms_notifications', $model->sms_notifications);
        $preferences->setOption('mei_annual_limit', $model->mei_annual_limit);
        $preferences->setOption('mei_alert_threshold', $model->mei_alert_threshold);
        $preferences->setOption('mei_monthly_alert_day', $model->mei_monthly_alert_day);

        return $preferences;
    }

    public function updateOption(string $key, mixed $value): void
    {
        $model = $this->model->first();
        if (!$model) {
            return;
        }

        switch ($key) {
            case 'theme':
                $model->theme = $value;
                break;
            case 'language':
                $model->language = $value;
                break;
            case 'currency':
                $model->currency = $value;
                break;
            case 'date_format':
                $model->date_format = $value;
                break;
            case 'time_format':
                $model->time_format = $value;
                break;
            case 'notifications_enabled':
                $model->notifications_enabled = $value;
                break;
            case 'notificationChannels':
                if (isset($value['email'])) {
                    $model->email_notifications = $value['email'];
                }
                if (isset($value['sms'])) {
                    $model->sms_notifications = $value['sms'];
                }
                break;
            case 'meiSettings':
                if (isset($value['annualLimit'])) {
                    $model->mei_annual_limit = $value['annualLimit'];
                }
                if (isset($value['alertThreshold'])) {
                    $model->mei_alert_threshold = $value['alertThreshold'];
                }
                if (isset($value['monthlyAlertDay'])) {
                    $model->mei_monthly_alert_day = $value['monthlyAlertDay'];
                }
                break;
        }

        $model->save();
    }

    public function getOption(string $key): mixed
    {
        $model = $this->model->first();
        if (!$model) {
            return null;
        }

        switch ($key) {
            case 'theme':
                return $model->theme;
            case 'language':
                return $model->language;
            case 'currency':
                return $model->currency;
            case 'date_format':
                return $model->date_format;
            case 'time_format':
                return $model->time_format;
            case 'notifications_enabled':
                return $model->notifications_enabled;
            case 'email_notifications':
                return $model->email_notifications;
            case 'sms_notifications':
                return $model->sms_notifications;
            case 'mei_annual_limit':
                return $model->mei_annual_limit;
            case 'mei_alert_threshold':
                return $model->mei_alert_threshold;
            case 'mei_monthly_alert_day':
                return $model->mei_monthly_alert_day;
            default:
                return null;
        }
    }

    public function getAllOptions(): array
    {
        $model = $this->model->first();
        if (!$model) {
            return [];
        }

        return [
            'theme' => $model->theme,
            'language' => $model->language,
            'currency' => $model->currency,
            'date_format' => $model->date_format,
            'time_format' => $model->time_format,
            'notifications_enabled' => $model->notifications_enabled,
            'email_notifications' => $model->email_notifications,
            'sms_notifications' => $model->sms_notifications,
            'mei_annual_limit' => $model->mei_annual_limit,
            'mei_alert_threshold' => $model->mei_alert_threshold,
            'mei_monthly_alert_day' => $model->mei_monthly_alert_day
        ];
    }

    public function findById(int $id): ?Preferences
    {
        $model = $this->model->find($id);
        if (!$model) {
            return null;
        }

        $preferences = new Preferences();
        $preferences->setId($model->id);
        $preferences->setUserId($model->user_id);
        $preferences->setOption('theme', $model->theme);
        $preferences->setOption('language', $model->language);
        $preferences->setOption('currency', $model->currency);
        $preferences->setOption('date_format', $model->date_format);
        $preferences->setOption('time_format', $model->time_format);
        $preferences->setOption('notifications_enabled', $model->notifications_enabled);
        $preferences->setOption('email_notifications', $model->email_notifications);
        $preferences->setOption('sms_notifications', $model->sms_notifications);
        $preferences->setOption('mei_annual_limit', $model->mei_annual_limit);
        $preferences->setOption('mei_alert_threshold', $model->mei_alert_threshold);
        $preferences->setOption('mei_monthly_alert_day', $model->mei_monthly_alert_day);
        return $preferences;
    }

    public function findAll(): array
    {
        $models = $this->model->all();
        $preferences = [];

        foreach ($models as $model) {
            $preference = new Preferences();
            $preference->setOption('theme', $model->theme);
            $preference->setOption('language', $model->language);
            $preference->setOption('currency', $model->currency);
            $preference->setOption('date_format', $model->date_format);
            $preference->setOption('time_format', $model->time_format);
            $preference->setOption('notifications_enabled', $model->notifications_enabled);
            $preference->setOption('email_notifications', $model->email_notifications);
            $preference->setOption('sms_notifications', $model->sms_notifications);
            $preference->setOption('mei_annual_limit', $model->mei_annual_limit);
            $preference->setOption('mei_alert_threshold', $model->mei_alert_threshold);
            $preference->setOption('mei_monthly_alert_day', $model->mei_monthly_alert_day);
            $preference->setId($model->id);
            $preference->setCreatedAt($model->created_at);
            $preference->setUpdatedAt($model->updated_at);
            $preferences[] = $preference;
        }

        return $preferences;
    }

    public function findByKey(string $key): ?Preferences
    {
        $model = $this->model->where('user_id', auth()->id())
            ->where(function ($query) use ($key) {
                $query->where('theme', $key)
                    ->orWhere('language', $key)
                    ->orWhere('currency', $key)
                    ->orWhere('date_format', $key)
                    ->orWhere('time_format', $key);
            })
            ->first();

        if (!$model) {
            return null;
        }

        $preferences = new Preferences();
        $preferences->setId($model->id);
        $preferences->setUserId($model->user_id);
        $preferences->setOption('theme', $model->theme);
        $preferences->setOption('language', $model->language);
        $preferences->setOption('currency', $model->currency);
        $preferences->setOption('date_format', $model->date_format);
        $preferences->setOption('time_format', $model->time_format);
        $preferences->setOption('notifications_enabled', $model->notifications_enabled);
        $preferences->setOption('email_notifications', $model->email_notifications);
        $preferences->setOption('sms_notifications', $model->sms_notifications);
        $preferences->setOption('mei_annual_limit', $model->mei_annual_limit);
        $preferences->setOption('mei_alert_threshold', $model->mei_alert_threshold);
        $preferences->setOption('mei_monthly_alert_day', $model->mei_monthly_alert_day);
        return $preferences;
    }

    public function findByUserId(int $userId): ?Preferences
    {
        $model = $this->model->where('user_id', $userId)->first();

        if (!$model) {
            return null;
        }

        $preferences = new Preferences();
        $preferences->setId($model->id);
        $preferences->setUserId($model->user_id);
        $preferences->setOption('theme', $model->theme);
        $preferences->setOption('language', $model->language);
        $preferences->setOption('currency', $model->currency);
        $preferences->setOption('date_format', $model->date_format);
        $preferences->setOption('time_format', $model->time_format);
        $preferences->setOption('notifications_enabled', $model->notifications_enabled);
        $preferences->setOption('email_notifications', $model->email_notifications);
        $preferences->setOption('sms_notifications', $model->sms_notifications);
        $preferences->setOption('mei_annual_limit', $model->mei_annual_limit);
        $preferences->setOption('mei_alert_threshold', $model->mei_alert_threshold);
        $preferences->setOption('mei_monthly_alert_day', $model->mei_monthly_alert_day);
        return $preferences;
    }
} 