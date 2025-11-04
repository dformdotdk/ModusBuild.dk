<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait CreatesApplication
{
    protected function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        RefreshDatabaseState::setRefreshDatabaseDefaultConnection(null);

        return $app;
    }
}
