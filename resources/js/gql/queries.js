import gql from "graphql-tag";

const USER_PREVIEW = gql`
    query User($id: ID) {
        user(id: $id) {
            id
            first_name
            last_name
            name
            email
            phone
            role
            city
            zip
            manager
            teams {
                name
            }
        }
    }
`;

const USERS = gql`
    query Users($page: Int, $filter: Filter, $orderBy: [OrderByClause!]) {
        users(page: $page, filter: $filter, orderBy: $orderBy) {
            data {
                id
                name
                email
                manager
                role
                home_team: default_team {
                    name
                }
            }
            pagination: paginatorInfo {
                current_page: currentPage
                last_page: lastPage
                from: firstItem
                to: lastItem
                per_page: perPage
                total
            }
        }
    }
`;

const LEADS = gql`
    query Leads($page: Int, $filter: Filter) {
        leads(page: $page, filter: $filter) {
            data {
                id
                created_at
                first_name
                last_name
                location {
                    name
                }
                lead_type {
                    name
                }
                owner_user_id
            }
            pagination: paginatorInfo {
                current_page: currentPage
                last_page: lastPage
                from: firstItem
                to: lastItem
                per_page: perPage
                total
            }
        }
    }
`;

const LEAD_PREVIEW = gql`
    query Lead($id: ID) {
        lead(id: $id) {
            id
            first_name
            last_name
            email
            primary_phone
            alternate_phone
            gender
            date_of_birth
            owner_user_id
            interaction_count {
                calledCount
                emailedCount
                smsCount
            }
            preview_note
            club_location {
                name
            }
        }
    }
`;
export default {
    user: {
        preview: USER_PREVIEW,
        edit: USER_PREVIEW,
    },
    lead: {
        preview: LEAD_PREVIEW,
        edit: LEAD_PREVIEW,
    },
    users: USERS,
    leads: LEADS,
};
