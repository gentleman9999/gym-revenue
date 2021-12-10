<?php

namespace App\Aggregates\Clients;

use App\Aggregates\Clients\Traits\ClientApplies;
use App\Aggregates\Clients\Traits\ClientGetters;
use App\Exceptions\Clients\ClientAccountException;
use App\Models\UserDetails;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use App\StorableEvents\Clients\TeamCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    use ClientGetters, ClientApplies;

    protected string $default_team = '';
    protected array $teams = [];

    protected static bool $allowConcurrency = true;

    public function createDefaultTeam(string $name)
    {
        if(!empty($this->default_team))
        {
            throw ClientAccountException::defaultTeamAlreadyCreated($this->default_team);
        }
        else
        {
            $this->recordThat(new DefaultClientTeamCreated($this->uuid(), $name));
        }

        return $this;
    }

    public function createAudience(string $name, string $slug, /*string $default_email, string $from_name,*/ string $created_by_user_id)
    {
        $this->recordThat(new AudienceCreated($this->uuid(), $name, $slug, /*$default_email, $from_name,*/ $created_by_user_id));
        return $this;
    }

    public function assignAudienceToEmailCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function assignAudienceToSMSCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function unassignAudienceFromEmailCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function unassignAudienceFromSMSCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function createNewEmailTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new EmailTemplateCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function updateEmailTemplate(string $template_id, string $updated_by, array $old_vals, array $new_vals)
    {
        $this->recordThat(new EmailTemplateUpdated($this->uuid(), $template_id, $updated_by, $old_vals, $new_vals));
        return $this;
    }

    public function createNewEmailCampaign(string $campaign_id, string $created_by = null)
    {
        $this->recordThat(new EmailCampaignCreated($this->uuid(), $campaign_id, $created_by));
        return $this;
    }

    public function updateEmailCampaign(string $campaign_id, string $updated_by, string $field, string $new_value, string $old_value = null)
    {
        $this->recordThat(new EmailCampaignUpdated($this->uuid(), $campaign_id, $updated_by, $field, $new_value, $old_value));
        return $this;
    }

    public function assignEmailTemplateToCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new EmailTemplateAssignedToEmailCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));
        return $this;
    }

    public function unassignEmailTemplateFromCampaign($template_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new EmailTemplateUnAssignedFromEmailCampaign($this->uuid(), $template_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function createNewSMSTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSTemplateCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function updateSmsTemplate(string $template_id, string $updated_by, array $old_vals, array $new_vals)
    {
        $this->recordThat(new SmsTemplateUpdated($this->uuid(), $template_id, $updated_by, $old_vals, $new_vals));
        return $this;
    }

    public function createNewSMSCampaign(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSCampaignCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function updateSmsCampaign(string $campaign_id, string $updated_by, string $field, string $new_value, string $old_value = null)
    {
        $this->recordThat(new SmsCampaignUpdated($this->uuid(), $campaign_id, $updated_by, $field, $new_value, $old_value));
        return $this;
    }

    public function assignSmsTemplateToCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new SMSTemplateAssignedToSMSCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));
        return $this;
    }
    public function unassignSmsTemplateFromCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new SMSTemplateUnAssignedFromSMSCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));
        return $this;
    }

    public function addTeam(string $team_id, string $team_name)
    {
        if(array_key_exists($team_id, $this->teams))
        {
            throw ClientAccountException::teamAlreadyAssigned($team_name);
        }
        else {
            // @todo - make sure the team is not assigned to another client
            $this->recordThat(new TeamCreated($this->uuid(), $team_id, $team_name));
        }
        return $this;
    }

    public function addCapeAndBayAdminsToTeam(string $team_id)
    {
        $users = UserDetails::select('user_id')
            ->whereName('default_team')
            ->whereValue(1)->get();

        if(count($users) > 0)
        {
            $payload = [];
            foreach($users as $user)
            {
                $payload[] = $user->user_id;
            }

            $this->recordThat(new CapeAndBayUsersAssociatedWithClientsNewDefaultTeam($this->uuid(), $team_id, $payload));
        }
        else
        {
            throw ClientAccountException::noCapeAndBayUsersAssigned();
        }

        return $this;
    }
}
