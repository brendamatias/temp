<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    protected $applicationModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->notificationModel = new NotificationModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $applications = $this->applicationModel->getApplicationsByUser($userId);
        
        $stats = [];
        foreach ($applications as $app) {
            $stats[$app['id']] = $this->notificationModel->getNotificationStats($app['id']);
        }

        $data = [
            'applications' => $applications,
            'stats' => $stats,
            'user_name' => session()->get('user_name')
        ];

        return view('dashboard/index', $data);
    }
}