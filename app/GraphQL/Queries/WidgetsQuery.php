<?php

namespace App\GraphQL\Queries;

use App\Services\Dashboard\HomeDashboardService;

final class WidgetsQuery
{
    protected HomeDashboardService $service;

    public function __construct(HomeDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        return $this->service->getDashboardWidgets();
    }
}
