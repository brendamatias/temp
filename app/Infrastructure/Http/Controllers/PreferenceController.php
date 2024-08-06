<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\Preferences\CreatePreferencesDTO;
use App\Application\DTOs\Preferences\UpdatePreferencesDTO;
use App\Application\DTOs\Preferences\GetPreferenceByKeyDTO;
use App\Application\DTOs\Preferences\GetPreferencesDTO;
use App\Application\UseCases\Preferences\CreatePreferencesUseCase;
use App\Application\UseCases\Preferences\UpdatePreferencesUseCase;
use App\Application\UseCases\Preferences\GetPreferencesUseCase;
use App\Application\UseCases\Preferences\GetPreferenceByKeyUseCase;
use App\Infrastructure\Http\Requests\Preference\CreatePreferenceRequest;
use App\Infrastructure\Http\Requests\Preference\UpdatePreferenceRequest;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Repositories\PreferencesRepository;

class PreferenceController extends Controller
{
    public function __construct(
        private readonly CreatePreferencesUseCase $createPreferencesUseCase,
        private readonly UpdatePreferencesUseCase $updatePreferencesUseCase,
        private readonly GetPreferencesUseCase $getPreferencesUseCase,
        private readonly GetPreferenceByKeyUseCase $getPreferenceByKeyUseCase,
        private readonly PreferencesRepository $preferencesRepository
    ) {}

    public function create(CreatePreferenceRequest $request): JsonResponse
    {
        $dto = new CreatePreferencesDTO(auth()->id(), $request->validated());
        $preference = $this->createPreferencesUseCase->execute($dto);
        
        return response()->json([
            'theme' => $preference->getOption('theme'),
            'language' => $preference->getOption('language'),
            'currency' => $preference->getOption('currency'),
            'date_format' => $preference->getOption('date_format'),
            'time_format' => $preference->getOption('time_format'),
            'notifications_enabled' => $preference->getOption('notifications_enabled'),
            'email_notifications' => $preference->getOption('email_notifications'),
            'sms_notifications' => $preference->getOption('sms_notifications'),
            'mei_annual_limit' => $preference->getOption('mei_annual_limit'),
            'mei_alert_threshold' => $preference->getOption('mei_alert_threshold'),
            'mei_monthly_alert_day' => $preference->getOption('mei_monthly_alert_day'),
            'created_at' => $preference->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $preference->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ], 201);
    }

    public function update(UpdatePreferenceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $dto = new UpdatePreferencesDTO(
            id: auth()->id(),
            theme: $data['theme'] ?? null,
            language: $data['language'] ?? null,
            currency: $data['currency'] ?? null,
            date_format: $data['date_format'] ?? null,
            time_format: $data['time_format'] ?? null,
            notifications_enabled: $data['notifications_enabled'] ?? null,
            email_notifications: filter_var($data['email_notifications'] ?? null, FILTER_VALIDATE_BOOLEAN),
            sms_notifications: filter_var($data['sms_notifications'] ?? null, FILTER_VALIDATE_BOOLEAN),
            mei_annual_limit: $data['mei_annual_limit'] ?? null,
            mei_alert_threshold: $data['mei_alert_threshold'] ?? null,
            mei_monthly_alert_day: $data['mei_monthly_alert_day'] ?? null
        );
        $this->updatePreferencesUseCase->execute($dto);
        $preference = $this->preferencesRepository->findById(auth()->id());
        if (!$preference) {
            return response()->json(['message' => 'Preference not found'], 404);
        }

        if ($preference->getUserId() !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'id' => $preference->getId(),
            'theme' => $preference->getOption('theme'),
            'language' => $preference->getOption('language'),
            'currency' => $preference->getOption('currency'),
            'date_format' => $preference->getOption('date_format'),
            'time_format' => $preference->getOption('time_format'),
            'notifications_enabled' => $preference->getOption('notifications_enabled'),
            'email_notifications' => $preference->getOption('email_notifications'),
            'sms_notifications' => $preference->getOption('sms_notifications'),
            'mei_annual_limit' => $preference->getOption('mei_annual_limit'),
            'mei_alert_threshold' => $preference->getOption('mei_alert_threshold'),
            'mei_monthly_alert_day' => $preference->getOption('mei_monthly_alert_day'),
            'created_at' => $preference->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $preference->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ]);
    }

    public function list(): JsonResponse
    {
        $dto = new GetPreferencesDTO();
        $preferences = $this->getPreferencesUseCase->execute($dto);
        $response = array_map(function ($preference) {
            return [
                'id' => $preference->getId(),
                'theme' => $preference->getOption('theme'),
                'language' => $preference->getOption('language'),
                'currency' => $preference->getOption('currency'),
                'date_format' => $preference->getOption('date_format'),
                'time_format' => $preference->getOption('time_format'),
                'notifications_enabled' => $preference->getOption('notifications_enabled'),
                'email_notifications' => $preference->getOption('email_notifications'),
                'sms_notifications' => $preference->getOption('sms_notifications'),
                'mei_annual_limit' => $preference->getOption('mei_annual_limit'),
                'mei_alert_threshold' => $preference->getOption('mei_alert_threshold'),
                'mei_monthly_alert_day' => $preference->getOption('mei_monthly_alert_day'),
                'created_at' => $preference->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $preference->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
            ];
        }, $preferences);
        return response()->json($response);
    }

    public function findById(int $id): JsonResponse
    {
        $preference = $this->preferencesRepository->findById($id);
        if (!$preference) {
            return response()->json(['message' => 'Preference not found'], 404);
        }

        if ($preference->getUserId() !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $preference->getId(),
            'theme' => $preference->getOption('theme'),
            'language' => $preference->getOption('language'),
            'currency' => $preference->getOption('currency'),
            'date_format' => $preference->getOption('date_format'),
            'time_format' => $preference->getOption('time_format'),
            'notifications_enabled' => $preference->getOption('notifications_enabled'),
            'email_notifications' => $preference->getOption('email_notifications'),
            'sms_notifications' => $preference->getOption('sms_notifications'),
            'mei_annual_limit' => $preference->getOption('mei_annual_limit'),
            'mei_alert_threshold' => $preference->getOption('mei_alert_threshold'),
            'mei_monthly_alert_day' => $preference->getOption('mei_monthly_alert_day'),
            'created_at' => $preference->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $preference->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ]);
    }

    public function show(): JsonResponse
    {
        $preference = $this->preferencesRepository->findById(auth()->id());
        if (!$preference) {
            return response()->json([
                'theme' => 'LIGHT',
                'language' => 'pt-BR',
                'currency' => 'BRL',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'notifications_enabled' => true,
                'email_notifications' => false,
                'sms_notifications' => false,
                'mei_annual_limit' => 81000,
                'mei_alert_threshold' => 80,
                'mei_monthly_alert_day' => 15
            ]);
        }

        return response()->json([
            'theme' => $preference->getOption('theme'),
            'language' => $preference->getOption('language'),
            'currency' => $preference->getOption('currency'),
            'date_format' => $preference->getOption('date_format'),
            'time_format' => $preference->getOption('time_format'),
            'notifications_enabled' => $preference->getOption('notifications_enabled'),
            'email_notifications' => $preference->getOption('email_notifications'),
            'sms_notifications' => $preference->getOption('sms_notifications'),
            'mei_annual_limit' => $preference->getOption('mei_annual_limit'),
            'mei_alert_threshold' => $preference->getOption('mei_alert_threshold'),
            'mei_monthly_alert_day' => $preference->getOption('mei_monthly_alert_day')
        ]);
    }
} 