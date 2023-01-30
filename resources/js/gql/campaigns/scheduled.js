import gql from "graphql-tag";

export const SCHEDULEDCAMPAIGNS = gql`
    query scheduledCampaigns($page: Int, $filter: Filter) {
        scheduledCampaigns(page: $page, filter: $filter) {
            data {
                id
                name
                audience_id
                send_at
                completed_at
                status {
                    value
                }
            }
            paginatorInfo {
                currentPage
                lastPage
                firstItem
                lastItem
                perPage
                total
            }
        }
    }
`;

export const SCHEDULEDCAMPAIGN_EDIT = gql`
    query ScheduledCampaign($id: ID) {
        scheduledCampaign(id: $id) {
            id
            name
            audience_id
            send_at
            completed_at
            status {
                value
            }
            email_template_id
            sms_template_id
            call_template_id

            created_at
            updated_at
        }
    }
`;

export const SCHEDULEDCAMPAIGN_CREATE = gql`
    query scheduledCampaigns {
        scheduledCampaign {
            data {
                id
                name
                audience_id
                send_at
                completed_at
                status {
                    value
                }
            }
        }
    }
`;

/** Mutations */
export const scheduledCampaign = {
    create: gql`
        mutation createScheduledCampaign(
            $campaign: CreateScheduledCampaignInput
        ) {
            createScheduledCampaign(campaign: $campaign) {
                id
                name
                audience_id
                send_at
                completed_at
                status {
                    value
                }
                email_template_id
                sms_template_id
                call_template_id

                created_at
                updated_at
            }
        }
    `,
    update: gql`
        mutation updateScheduledCampaign(
            $campaign: UpdateScheduledCampaignInput
        ) {
            updateScheduledCampaign(campaign: $campaign) {
                id
                name
                audience_id
                send_at
                completed_at
                status {
                    value
                }
                email_template_id
                sms_template_id
                call_template_id
                created_at
                updated_at
            }
        }
    `,
};
