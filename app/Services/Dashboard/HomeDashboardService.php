<?php

namespace App\Services\Dashboard;

use App\Actions\Dashboard\Home\GetDashboardWidgets;

class HomeDashboardService
{
    public function __construct()
    {

    }

    public function getDashboardWidgets() : array
    {
        return GetDashboardWidgets::run();
    }
}
