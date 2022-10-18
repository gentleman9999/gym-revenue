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
                opportunity
                lead_type: leadType {
                    id
                    name
                }
                location {
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

const LOCATIONS = gql`
    query Locations($page: Int, $filter: Filter) {
        locations(page: $page, filter: $filter) {
            data {
                id
                name
                city
                state
                active
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
const LOCATION_PREVIEW = gql`
    query Location($id: ID) {
        location(id: $id) {
            name
            location_no
            city
            state
            zip
            address1
            address2
        }
    }
`;

const MEMBERS = gql`
    query Members($page: Int, $filter: Filter) {
        members(page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                created_at
                updated_at
                location {
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

const MEMBER_PREVIEW = gql`
    query Member($id: ID) {
        member(id: $id) {
            id
            first_name
            last_name
            email
            primary_phone
            alternate_phone
            gender
            date_of_birth
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

const TEAMS = gql`
    query Teams($page: Int, $filter: Filter) {
        teams(page: $page, filter: $filter) {
            data {
                id
                name
                created_at
                updated_at
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

const TEAM_PREVIEW = gql`
    query Team($id: ID) {
        team(id: $id) {
            name
            users {
                id
                name
                email
                role
            }
            client {
                name
            }
        }
        clubs: locations {
            data {
                id
                name
                location_no
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
    location: {
        preview: LOCATION_PREVIEW,
        edit: LOCATION_PREVIEW,
    },
    member: {
        preview: MEMBER_PREVIEW,
        edit: MEMBER_PREVIEW,
    },
    team: {
        preview: TEAM_PREVIEW,
        edit: TEAM_PREVIEW,
    },
    users: USERS,
    leads: LEADS,
    locations: LOCATIONS,
    members: MEMBERS,
    teams: TEAMS,
};
