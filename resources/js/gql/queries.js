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

const USER_EDIT = gql`
    query User($id: ID) {
        user(id: $id) {
            first_name
            last_name
            contact_preference {
                value
            }
            alternate_email
            address1
            address2
            city
            state
            zip
            phone
            start_date
            end_date
            positions {
                id
                name
            }
            departments {
                id
                name
            }
            termination_date
            isClientUser
        }
        availableDepartments: departments(filter: { client_id: $id }) {
            data {
                id
                name
            }
        }
        availablePositions: positions(filter: { client_id: $id }) {
            data {
                id
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

const LEAD_EDIT = gql`
    query Lead($id: ID) {
        lead(id: $id) {
            id
            first_name
            middle_name
            last_name
            email
            primary_phone
            alternate_phone
            gender
            date_of_birth
            agreement_number
            misc
            external_id
            gr_location_id
            location {
                id
            }
            client {
                id
            }
            leadSource {
                id
            }
            leadType {
                id
            }
            owner_user_id
            lead_owners {
                user {
                    id
                    name
                }
            }
            lead_status {
                id
            }
            opportunity
            notes {
                title
                note
                id
            }
            interaction_count {
                calledCount
                emailedCount
                smsCount
            }
            owner_user_id
            locations {
                id
                name
            }
        }
        lead_types {
            id
            name
        }
        lead_statuses {
            id
            status
        }
        lead_sources {
            id
            name
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
const LOCATION_EDIT = gql`
    query Location($id: ID) {
        location(id: $id) {
            name
            location_no
            city
            state
            zip
            address1
            address2
            phone
            open_date
            close_date
            details {
                field
                value
            }
            client {
                id
            }
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

const MEMBER_EDIT = gql`
    query Member($id: ID) {
        member(id: $id) {
            id
            first_name
            middle_name
            last_name
            email
            primary_phone
            alternate_phone
            gender
            date_of_birth
            agreement_number
            external_id
            misc
            client {
                id
            }
            location {
                id
            }
            locations {
                id
                name
            }
            notes {
                id
                title
                note
                read
            }
            all_notes
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

const TEAM_EDIT = gql`
    query Team($id: ID) {
        team(id: $id) {
            name
            client {
                name
            }
            users {
                id
                name
                email
                role
            }
            locations {
                id
            }
            client_id
        }
        availableLocations: locations {
            data {
                id
                name
                gymrevenue_id
            }
        }
    }
`;

const ROLES = gql`
    query Roles($page: Int, $filter: Filter) {
        roles(page: $page, filter: $filter) {
            data {
                id
                title
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

const ROLE_EDIT = gql`
    query Role($id: ID) {
        role(id: $id) {
            id
            name
            abilities {
                name
            }
            availableAbilities {
                name
                title
            }
            securityGroups {
                value
                name
            }
        }
    }
`;

const DEPARTMENTS = gql`
    query Departments($page: Int, $filter: DepartmentFilter) {
        departments(page: $page, filter: $filter) {
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

const DEPARTMENT_EDIT = gql`
    query Department($id: ID) {
        department(id: $id) {
            id
            name
            client_id
            positions {
                id
                name
            }
        }
        positions {
            data {
                id
                name
            }
        }
    }
`;

const POSITIONS = gql`
    query Positions($page: Int, $filter: PositionFilter) {
        positions(page: $page, filter: $filter) {
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

const POSITION_EDIT = gql`
    query Position($id: ID) {
        position(id: $id) {
            id
            name
            client_id
            departments {
                id
                name
            }
        }
        departments {
            data {
                id
                name
            }
        }
    }
`;

const EVENT_TYPES = gql`
    query CalendarEventTypes($page: Int, $filter: Filter) {
        calendar_event_types(page: $page, filter: $filter) {
            data {
                id
                name
                description
                type
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

const EVENT_TYPES_EDIT = gql`
    query EventType($id: ID) {
        eventType: calendar_event_type(id: $id) {
            id
            name
            description
            type
            color
            client {
                id
            }
        }
    }
`;

const NOTES = gql`
    query Notes($page: Int, $filter: Filter) {
        notes(page: $page, filter: $filter) {
            data {
                id
                title
                note
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

const NOTE_EDIT = gql`
    query Note($id: ID) {
        note(id: $id) {
            id
            title
            note
            active
        }
    }
`;

const REMINDERS = gql`
    query Reminders($page: Int, $filter: Filter) {
        reminders(page: $page, filter: $filter) {
            data {
                id
                name
                description
                remind_time
                triggered_at
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

const REMINDER_EDIT = gql`
    query Reminder($id: ID) {
        reminder(id: $id) {
            id
            name
            description
            remind_time
        }
    }
`;

export default {
    user: {
        preview: USER_PREVIEW,
        edit: USER_EDIT,
    },
    lead: {
        preview: LEAD_PREVIEW,
        edit: LEAD_EDIT,
    },
    location: {
        preview: LOCATION_PREVIEW,
        edit: LOCATION_EDIT,
    },
    member: {
        preview: MEMBER_PREVIEW,
        edit: MEMBER_EDIT,
    },
    team: {
        preview: TEAM_PREVIEW,
        edit: TEAM_EDIT,
    },
    role: {
        edit: ROLE_EDIT,
    },
    department: {
        edit: DEPARTMENT_EDIT,
    },
    position: {
        edit: POSITION_EDIT,
    },
    eventType: {
        edit: EVENT_TYPES_EDIT,
    },
    note: {
        edit: NOTE_EDIT,
    },
    reminder: {
        edit: REMINDER_EDIT,
    },
    users: USERS,
    leads: LEADS,
    locations: LOCATIONS,
    members: MEMBERS,
    teams: TEAMS,
    roles: ROLES,
    departments: DEPARTMENTS,
    positions: POSITIONS,
    eventTypes: EVENT_TYPES,
    notes: NOTES,
    reminders: REMINDERS,
};
