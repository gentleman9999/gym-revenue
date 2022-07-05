<?php

namespace App\Actions\Auth;

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientDetail;
use App\Domain\Teams\Models\Team;
use App\Models\Admin\RegisterToken;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class RenderRegisterPage
{
    use AsAction;

    public function handle()
    {
        $passing_props = [
            'show-registration' => false,
            'error-msg' => 'You need a registration URL to view this page.',
        ];

        if (request()->has('token')) {
            $token = request()->get('token');
            // Check RegistrationTokens for the token.
            $token_record = RegisterToken::find($token);

            if (! is_null($token_record)) {
                if ($token_record->active) {
                    $uses_left = $token_record->uses;
                    if ($uses_left == 0) {
                        // Check for Uses, if 0 fail scenario and update active to 0
                        $passing_props['error-msg'] = "This token URL no longer available.";
                        $token_record->active = 0;
                        $token_record->save();
                    } else {
                        // If uses is -1 or > 0, continue
                        $passing_props['show-registration'] = true;
                        $is_cnb = is_null($token_record->client_id);

                        if ($is_cnb) {
                            // Get the Role or Admin
                            // @todo - better define roles so there's no slug;
                            $role = is_null($token_record->role) ? 'Admin' : $token_record->role;
                            if (is_null($token_record->team_id)) {
                                $team_record = Team::whereName('Cape & Bay Admin Team')->first();

                                if (! is_null($team_record)) {
                                    $passing_props['role'] = $role;
                                    $passing_props['team'] = $team_record->name;
                                    $passing_props['team-id'] = $team_record->id;
                                    $passing_props['client'] = 'Cape & Bay';
                                    $passing_props['client-id'] = 1;
                                } else {
                                    $passing_props['show-registration'] = false;
                                    $passing_props['error-msg'] = "This project is not yet configured.";
                                }
                            } else {
                                $team_record = Team::find($token_record->team_id)->first();

                                if (! is_null($team_record)) {
                                    $passing_props['role'] = $role;
                                    $passing_props['team'] = $team_record->name;
                                    $passing_props['team-id'] = $team_record->id;
                                    $passing_props['client'] = 'Cape & Bay';
                                    $passing_props['client-id'] = 1;
                                } else {
                                    $passing_props['show-registration'] = false;
                                    $passing_props['error-msg'] = "This team linked to this token URL is no longer available.";
                                }
                            }

                            $passing_props['extra-img'] = '';
                        } else {
                            $client = Client::find($token_record->client_id);

                            if (! is_null($client)) {
                                // Get the AuthCardLogo svg markup from Client Details or fail scenario
                                $logo_record = ClientDetail::whereClientId($token_record->client_id)
                                    ->whereDetail('registration-logo')->whereActive(1)->first();

                                $passing_props['extra-img'] = $logo_record->value;
                                if (! is_null($logo_record)) {
                                    $role = is_null($token_record->role) ? 'Account Owner' : $token_record->role;

                                    if (is_null($token_record->team_id)) {
                                        if (! is_null($client->home_team_id)) {
                                            $team_record = Team::find($client->home_team_id);

                                            if (! is_null($team_record)) {
                                                $passing_props['role'] = $role;
                                                $passing_props['team'] = $team_record->name;
                                                $passing_props['team-id'] = $team_record->id;
                                                $passing_props['client'] = $client->name;
                                                $passing_props['client-id'] = 1;
                                            } else {
                                                $passing_props['show-registration'] = false;
                                                $passing_props['error-msg'] = "Error - Default Team Mismatch.";
                                            }
                                        } else {
                                            $passing_props['show-registration'] = false;
                                            $passing_props['error-msg'] = "Error - Missing Default Team.";
                                        }
                                    } else {
                                        $team_record = Team::find($token_record->team_id)->first();

                                        if (! is_null($team_record)) {
                                            $passing_props['role'] = $role;
                                            $passing_props['team'] = $team_record->name;
                                            $passing_props['team-id'] = $team_record->id;
                                            $passing_props['client'] = $client->name;
                                            $passing_props['client-id'] = 1;
                                        } else {
                                            $passing_props['show-registration'] = false;
                                            $passing_props['error-msg'] = "This team linked to this token URL is no longer available.";
                                        }
                                    }
                                } else {
                                    $passing_props['show-registration'] = false;
                                    $passing_props['error-msg'] = "Error - Missing Registration Logo.";
                                }
                            } else {
                                $passing_props['show-registration'] = false;
                                $passing_props['error-msg'] = "The client linked to this token is no longer available.";
                                $token_record->active = 0;
                                $token_record->save();
                            }
                        }
                    }
                } else {
                    $passing_props['error-msg'] = "This token URL has expired.";
                }
            } else {
                // If not exist, error message invalid token.
                $passing_props['error-msg'] = "Invalid Register Token.";
            }
        }

        return Inertia::render('Auth/Register', $passing_props);
    }
}
