<?php

namespace App\Aggregates\Clients\Traits;

use App\Aggregates\Clients\Traits\Actions\ClientCommsPrefsActions;
use App\Aggregates\Clients\Traits\Actions\ClientCrudActions;
use App\Aggregates\Clients\Traits\Actions\ClientImportActions;
use App\Aggregates\Clients\Traits\Actions\ClientNoteActions;
use App\Aggregates\Clients\Traits\Actions\ClientServicesActions;
use App\Aggregates\Clients\Traits\Actions\ClientTrialMembershipActions;
use App\Aggregates\Clients\Traits\Actions\ClientUserActions;

trait ClientActions
{
    use ClientCrudActions;
    use ClientServicesActions;
    use ClientTrialMembershipActions;
    use ClientNoteActions;
    use ClientUserActions;
    use ClientImportActions;
    use ClientCommsPrefsActions;
}
