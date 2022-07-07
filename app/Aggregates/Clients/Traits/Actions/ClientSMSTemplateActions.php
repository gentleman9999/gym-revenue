<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;

trait ClientSMSTemplateActions
{
    public function createSMSTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSTemplateCreated($this->uuid(), $template_id, $created_by));

        return $this;
    }

    public function updateSmsTemplate(string $template_id, string $updated_by, array $old_vals, array $new_vals)
    {
        $this->recordThat(new SmsTemplateUpdated($this->uuid(), $template_id, $updated_by, $old_vals, $new_vals));

        return $this;
    }
}
