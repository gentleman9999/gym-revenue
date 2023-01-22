import gql from "graphql-tag";
import {
    EMAIL_TEMPLATES,
    EMAIL_TEMPLATE_EDIT,
    EMAIL_TEMPLATE_CREATE,
} from "./templates/email";
import {
    SMS_TEMPLATES,
    SMS_TEMPLATE_EDIT,
    SMS_TEMPLATE_CREATE,
} from "./templates/sms";
import {
    CALL_TEMPLATES,
    CALL_TEMPLATE_EDIT,
    CALL_TEMPLATE_CREATE,
} from "./templates/call";
import {
    AUDIENCES,
    AUDIENCE_EDIT,
    AUDIENCE_PERMISSIONS,
} from "./campaigns/audiences";
import {
    DRIPCAMPAIGNS,
    DRIPCAMPAIGN_EDIT,
    DRIPCAMPAIGN_CREATE,
} from "./campaigns/drip";
import {
    SCHEDULEDCAMPAIGNS,
    SCHEDULEDCAMPAIGN_EDIT,
    SCHEDULEDCAMPAIGN_CREATE,
} from "./campaigns/scheduled";

const USER_PREVIEW = gql`
    query User($id: ID) {
        user(id: $id) {
            id
            first_name
            last_name
            gender
            name
            email
            phone
            roles {
                id
                name
            }
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
            id
            email
            first_name
            last_name
            contact_preference
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
            emergency_contact {
                ec_first_name
                ec_last_name
                ec_phone
            }
        }
        roles(first: 0) {
            data {
                id
                title
            }
        }
        locations(first: 0) {
            data {
                id
                name
                gymrevenue_id
            }
        }
        availableDepartments: departments {
            data {
                id
                name
                positions {
                    id
                    name
                }
            }
        }
        availablePositions: positions {
            data {
                id
                name
            }
        }
    }
`;

const USER_CREATE = gql`
    query User {
        roles(first: 0) {
            data {
                id
                title
            }
        }
        locations(first: 0) {
            data {
                id
                name
                gymrevenue_id
            }
        }
        availableDepartments: departments {
            data {
                id
                name
                positions {
                    id
                    name
                }
            }
        }
        availablePositions: positions {
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
                roles {
                    id
                    name
                }
                home_team: defaultTeam {
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
                home_location {
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
            notes {
                note
            }
            home_location {
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
        #        lead_statuses {
        #            id
        #            status
        #        }
        #        lead_sources {
        #            id
        #            name
        #        }
        locations(first: 100) {
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
            home_location {
                id
            }
            #            leadSource {
            #                id
            #            }
            #            leadType {
            #                id
            #            }
            owner_user_id
            #            lead_status {
            #                id
            #            }
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
        locations(first: 100) {
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
            id
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
            id
            name
            location_no
            gymrevenue_id
            city
            state
            zip
            address1
            address2
            phone
            open_date
            close_date
            latitude
            longitude
            poc_phone
            poc_first
            poc_last
            location_type
        }
    }
`;
const LOCATION_CREATE = gql`
    query Location {
        locationTypes {
            label
            value
            description
        }
    }
`;

const LOCATION_TYPES = gql`
    query LocationTypes {
        locationTypes {
            label
            value
            description
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
                home_location {
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
            notes {
                note
            }
            home_location {
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
            home_location {
                id
            }
            notes {
                id
                title
                note
                read
            }
        }
        locations(first: 100) {
            data {
                id
                name
            }
        }
    }
`;

const MEMBER_CREATE = gql`
    query Member {
        locations(first: 100) {
            data {
                id
                name
            }
        }
    }
`;

const CUSTOMERS = gql`
    query Customers($page: Int, $filter: Filter) {
        customers(page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                created_at
                updated_at
                home_location {
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

const CUSTOMER_PREVIEW = gql`
    query Customer($id: ID) {
        customer(id: $id) {
            id
            first_name
            last_name
            email
            phone
            alternate_phone
            gender
            date_of_birth
            interaction_count {
                calledCount
                emailedCount
                smsCount
            }
            notes {
                note
            }
            home_location {
                name
            }
        }
    }
`;

const CUSTOMER_EDIT = gql`
    query Customer($id: ID) {
        customer(id: $id) {
            id
            first_name
            middle_name
            last_name
            email
            phone
            alternate_phone
            gender
            date_of_birth
            agreement_number
            external_id
            misc
            home_location {
                id
            }
            notes {
                id
                title
                note
                read
            }
        }
        locations(first: 100) {
            data {
                id
                name
            }
        }
    }
`;

const CUSTOMER_CREATE = gql`
    query Customer {
        customer {
            email
            first_name
            last_name
            phone
            user_type
            gender
            date_of_birth
            address1
            zip
            city
            state
            home_location {
                id
            }
            notes {
                id
                title
                note
                read
            }
        }
        locations(first: 100) {
            data {
                id
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
            id
            name
            users {
                id
                name
                email
                roles {
                    id
                    name
                }
            }
        }
    }
`;

const TEAM_EDIT = gql`
    query Team($id: ID) {
        team(id: $id) {
            id
            name
            users {
                id
                name
                email
                roles {
                    id
                    name
                }
            }
            locations {
                id
                name
            }
        }
    }
`;
const TEAM_CREATE = gql`
    query AvailableLocations {
        availableLocations: locations(first: 100) {
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

const DEPARTMENT_LIST = gql`
    query DepartmentList {
        departments {
            data {
                id
                name
            }
        }
    }
`;

const DEPARTMENT_EDIT = gql`
    query Department($id: ID) {
        department(id: $id) {
            id
            name
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
        eventTypes: calendar_event_types(page: $page, filter: $filter) {
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
            call_task
            im_attending
            my_reminder {
                id
                remind_time
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

// const DEFAULT_CREATE = gql`
//     query DefaultQuery($id: ID) {
//         clientId(id: $id)
//     }
// `;

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

const REMINDER_CREATE = gql`
    query Reminder(
        $name: String
        $description: String
        $remind_time: String
        $triggered_at: DateTime
    ) {
        reminder(
            name: $name
            description: $description
            remind_time: $remind_time
            triggered_at: $triggered_at
        ) {
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
const PROFILE_QUERY = gql`
    query ProfileQuery {
        props: profileQuery {
            user {
                first_name
                last_name
                phone
                email
                alternate_email
                address1
                address2
                city
                state
                zip
                contact_preference
            }
        }
    }
`;

const TOPOL_API_KEY = gql`
    query TopolApiKey {
        topolApiKey
    }
`;

export default {
    audience: {
        edit: AUDIENCE_EDIT,
    },
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
        create: LOCATION_CREATE,
    },
    member: {
        preview: MEMBER_PREVIEW,
        edit: MEMBER_EDIT,
        create: MEMBER_CREATE,
    },
    customer: {
        preview: CUSTOMER_PREVIEW,
        edit: CUSTOMER_EDIT,
        create: CUSTOMER_CREATE,
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
        // create: DEFAULT_CREATE,
    },
    note: {
        edit: NOTE_EDIT,
    },
    reminder: {
        edit: REMINDER_EDIT,
        create: REMINDER_CREATE,
    },
    event: {
        get: CALENDAR_EVENT_GET,
    },
    emailTemplate: {
        edit: EMAIL_TEMPLATE_EDIT,
        create: EMAIL_TEMPLATE_CREATE,
    },
    smsTemplate: {
        create: SMS_TEMPLATE_CREATE,
        edit: SMS_TEMPLATE_EDIT,
    },
    callTemplate: {
        create: CALL_TEMPLATE_CREATE,
        edit: CALL_TEMPLATE_EDIT,
    },
    task: {
        edit: CALENDAR_EVENT_GET,
    },
    scheduledCampaign: {
        edit: SCHEDULEDCAMPAIGN_EDIT,
        create: SCHEDULEDCAMPAIGN_CREATE,
    },
    dripCampaign: {
        edit: DRIPCAMPAIGN_EDIT,
        create: DRIPCAMPAIGN_CREATE,
    },

    scheduledCampaigns: SCHEDULEDCAMPAIGNS,
    dripCampaigns: DRIPCAMPAIGNS,
    TOPOL_API_KEY,
    audiences: AUDIENCES,
    audiencePermissions: AUDIENCE_PERMISSIONS,
    emailTemplates: EMAIL_TEMPLATES,
    smsTemplates: SMS_TEMPLATES,
    callTemplates: CALL_TEMPLATES,
    customers: CUSTOMERS,
    users: USERS,
    leads: LEADS,
    locations: LOCATIONS,
    locationTypes: LOCATION_TYPES,
    members: MEMBERS,
    teams: TEAMS,
    roles: ROLES,
    departments: DEPARTMENTS,
    departmentList: DEPARTMENT_LIST,
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
    profileQuery: PROFILE_QUERY,
};
