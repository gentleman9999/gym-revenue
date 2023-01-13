import gql from "graphql-tag";

export const DRIPCAMPAIGNS = gql`
    query DripCampaign($page: Int, $filter: Filter) {
        dripCampaigns(page: $page, filter: $filter) {
            data {
                id
                name
                audience_id
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

export const DRIPCAMPAIGN_EDIT = gql`
    query DripCampaign($id: ID) {
        dripCampaign(id: $id) {
            id
            name
            audience_id
            start_at
            end_at
            completed_at
            status {
                value
            }
            created_at
            updated_at
            deleted_at
            days {
                id
                drip_campaign_id
                day_of_campaign
                email_template_id
                sms_template_id
                call_template_id
            }
        }
        audiences(first: 100) {
            data {
                id
                name
            }
        }
    }
`;

export const DRIPCAMPAIGN_CREATE = gql`
    query DripCampaign {
        audiences(first: 100) {
            data {
                id
                name
            }
        }
    }
`;

/** Mutations */
export const dripCampaign = {
    create: gql`
        mutation createdripCampaign($campaign: CreateDripCampaignInput) {
            createDripCampaign(campaign: $campaign) {
                id
                name
                audience_id
                start_at
                end_at
                completed_at
                status
                created_at
                updated_at
                deleted_at
                days {
                    id
                    drip_campaign_id
                    day_of_campaign
                    email_template_id
                    sms_template_id
                    call_template_id
                }
            }
        }
    `,
    update: gql`
        mutation updateDripCampaign($campaign: UpdateDripCampaignInput) {
            updateDripCampaign(campaign: $campaign) {
                id
                name
                audience_id
                start_at
                end_at
                completed_at
                status
                created_at
                updated_at
                deleted_at
                days {
                    id
                    drip_campaign_id
                    day_of_campaign
                    email_template_id
                    sms_template_id
                    call_template_id
                }
            }
        }
    `,
};
