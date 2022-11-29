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
        isClientUser(id: $id)
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

const USER_CREATE = gql`
    query User($id: ID) {
        isClientUser(id: $id)
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

const LEAD_CREATE = gql`
    query Lead {
        lead_owners: leadOwners {
            user {
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
        locations {
            data {
                id
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
        }
        lead_owners: leadOwners {
            user {
                id
                name
            }
        }
        locations {
            data {
                id
                name
            }
        }
        lead_types {
            id
            name
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
            notes {
                id
                title
                note
                read
            }
            all_notes
        }
        locations: memberLocations {
            id
            name
        }
    }
`;

const MEMBER_CREATE = gql`
    query Member {
        locations: memberLocations {
            id
            name
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
const TEAM_CREATE = gql`
    query AvailableLocations {
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

const ROLE_EDIT = gql`
    query Role($id: ID) {
        role(id: $id) {
            id
            name
            abilities {
                name
            }
        }
        availableAbilities {
            id
            name
            title
        }
        securityGroups {
            value
            name
        }
    }
`;

const ROLE_CREATE = gql`
    query Role {
        availableAbilities {
            id
            name
            title
        }
        securityGroups {
            value
            name
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

const DEPARTMENT_CREATE = gql`
    query Position {
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

const POSITION_CREATE = gql`
    query Departments {
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

const CALENDAR_EVENTS = gql`
    query CalendarEventsQuery($param: CalendarEventInput) {
        calendarEvents(param: $param) {
            id
            title
            owner_id
            start
            end
            title
            description
            full_day_event
            event_type_id
            client_id
        }
    }
`;

const CALENDAR_EVENT_GET = gql`
    query CalendarEventGetQuery($id: ID) {
        event: calendarEventQuery(id: $id) {
            id
            title
            start
            end
            description
            editable
            event_type_id
            full_day_event
            event_completion
            type {
                type
            }
            user_attendees {
                id
            }
            lead_attendees {
                id
            }
            member_attendees {
                id
            }
            attendees {
                id
                entity_type
                invitation_status
                entity_data {
                    profile_photo_url
                    name
                    first_name
                    last_name
                    email
                }
            }
            location_id
            client_id
            call_task
            im_attending
            my_reminder
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

const DEFAULT_CREATE = gql`
    query DefaultQuery($id: ID) {
        clientId(id: $id)
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

const TASKS = gql`
    query TasksQuery($param: TaskParam, $pagination: PaginationInput) {
        tasks: tasksQuery(param: $param, pagination: $pagination) {
            data {
                id
                title
                owner {
                    id
                }
                start
                created_at
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
const FILE_FOLDERS = gql`
    query FilesAndFolders($id: ID, $filter: Filter) {
        folderContent(id: $id, filter: $filter) {
            name
            files {
                id
                extension
                url
                filename
                created_at
                size
            }
            folders {
                id
                name
                created_at
                files_count
            }
        }
    }
`;

const GLOBAL_SEARCH = gql`
    query GlobalSearch($term: String, $pagination: PaginationInput) {
        globalSearch(term: $term, pagination: $pagination) {
            data {
                id
                name
                type
                link
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

const WIDGETS = gql`
    query WidgetQuery {
        widgets: widgetsQuery {
            title
            value
            type
            icon
        }
    }
`;

const DASHBOARD_QUERY = gql`
    query DashboardQuery {
        props: dashboardQuery {
            teamName
            accountName
            teams {
                id
                name
                client {
                    name
                }
            }
            announcements {
                id
                version
                notes
            }
            widgets {
                title
                value
                type
                icon
            }
        }
    }
`;
export default {
    user: {
        preview: USER_PREVIEW,
        edit: USER_EDIT,
        create: USER_CREATE,
    },
    lead: {
        preview: LEAD_PREVIEW,
        create: LEAD_CREATE,
        edit: LEAD_EDIT,
    },
    location: {
        preview: LOCATION_PREVIEW,
        edit: LOCATION_EDIT,
    },
    member: {
        preview: MEMBER_PREVIEW,
        edit: MEMBER_EDIT,
        create: MEMBER_CREATE,
    },
    team: {
        preview: TEAM_PREVIEW,
        edit: TEAM_EDIT,
        create: TEAM_CREATE,
    },
    role: {
        edit: ROLE_EDIT,
        create: ROLE_CREATE,
    },
    department: {
        edit: DEPARTMENT_EDIT,
        create: DEPARTMENT_CREATE,
    },
    position: {
        edit: POSITION_EDIT,
        create: POSITION_CREATE,
    },
    eventType: {
        edit: EVENT_TYPES_EDIT,
        create: DEFAULT_CREATE,
    },
    note: {
        edit: NOTE_EDIT,
    },
    reminder: {
        edit: REMINDER_EDIT,
    },
    event: {
        get: CALENDAR_EVENT_GET,
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
    tasks: TASKS,
    files: FILE_FOLDERS,
    global_search: GLOBAL_SEARCH,
    events: CALENDAR_EVENTS,
    widgets: WIDGETS,
    dashboardQuery: DASHBOARD_QUERY,
};
