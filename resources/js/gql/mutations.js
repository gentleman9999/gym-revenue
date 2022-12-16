import gql from "graphql-tag";

const department = {
    create: gql`
        mutation createDepartment($name: String, $positions: [ID]) {
            createDepartment(name: $name, positions: $positions) {
                id
                name
            }
        }
    `,
    update: gql`
        mutation updateDepartment($id: ID, $name: String, $positions: [ID]) {
            updateDepartment(id: $id, name: $name, positions: $positions) {
                id
                name
            }
        }
    `,
};
const position = {
    create: gql`
        mutation createPosition($name: String, $departments: [ID]) {
            createPosition(name: $name, departments: $departments) {
                id
                name
            }
        }
    `,
    update: gql`
        mutation updatePosition($id: ID, $name: String, $departments: [ID]) {
            updatePosition(id: $id, name: $name, departments: $departments) {
                id
                name
            }
        }
    `,
};

const role = {
    create: gql`
        mutation createRole(
            $name: String
            $ability_names: [String]
            $group: Int
        ) {
            createRole(
                name: $name
                ability_names: $ability_names
                group: $group
            ) {
                id
                name
                created_at
                updated_at
            }
        }
    `,
    update: gql`
        mutation updateRole(
            $id: ID
            $name: String
            $ability_names: [String]
            $group: Int
        ) {
            updateRole(
                id: $id
                name: $name
                ability_names: $ability_names
                group: $group
            ) {
                id
                name
                created_at
                updated_at
            }
        }
    `,
    delete: gql`
        mutation deleteRole($id: ID) {
            deleteRole(id: $id) {
                id
                name
            }
        }
    `,
};

const user = {
    create: gql`
        mutation createUser($input: UserInput) {
            createUser(input: $input) {
                id
                name
            }
        }
    `,
    update: gql`
        mutation updateUser($input: UserInput) {
            updateUser(input: $input) {
                id
                name
            }
        }
    `,
};

const team = {
    create: gql`
        mutation createTeam($name: String, $positions: [ID]) {
            createTeam(name: $name, positions: $positions) {
                id
                name
            }
        }
    `,
    update: gql`
        mutation updateTeam($id: ID, $name: String, $positions: [ID]) {
            updateTeam(id: $id, name: $name, positions: $positions) {
                id
                name
            }
        }
    `,
};

const location = {
    create: gql`
        mutation createLocation($location: CreateLocationInput!) {
            createLocation(location: $location) {
                id
                gymrevenue_id
                location_no
                location_type
                name
                city
                state
                active
                zip
                address1
                address2
                phone
                open_date
                close_date
            }
        }
    `,
    update: gql`
        mutation updateLocation($location: UpdateLocationInput!) {
            updateLocation(location: $location) {
                id
                gymrevenue_id
                location_no
                location_type
                name
                city
                state
                active
                zip
                address1
                address2
                phone
                open_date
                close_date
            }
        }
    `,
};

const task = {
    create: gql`
        mutation createCalendarEvent($input: CreateCalendarEventInput!) {
            createCalendarEvent(input: $input) {
                id
                title
            }
        }
    `,
    update: gql`
        mutation updateCalendarEvent($input: PatchCalendarEventInput!) {
            updateCalendarEvent(input: $input) {
                id
                title
            }
        }
    `,
};

const emailTemplate = {
    create: gql`
        mutation createEmailTemplate(
            $name: name
            $subject: subject
            $markup: markup
            $json: json
        ) {
            createEmailTemplate(
                name: $name
                subject: $subject
                markup: $markup
                json: $json
            ) {
                id
                name
            }
        }
    `,
};

const note = {
    create: gql`
        mutation createNote($input: CreateNoteInput!) {
            createNote(input: $input) {
                id
                title
                note
                active
            }
        }
    `,
    update: gql`
        mutation updateNote($input: UpdateNoteInput!) {
            updateNote(input: $input) {
                id
                title
                note
                active
            }
        }
    `,
};

const folder = {
    create: gql`
        mutation createFolder($name: String!) {
            createFolder(name: $name) {
                id
                name
                created_at
            }
        }
    `,
    update: gql`
        mutation updateFolder($id: ID!, $name: String!) {
            updateFolder(id: $id, name: $name) {
                id
                name
            }
        }
    `,
    trash: gql`
        mutation trashFolder($id: ID!) {
            trashFolder(id: $id) {
                id
                name
            }
        }
    `,
    share: gql`
        mutation updateFolderSharing($input: FolderSharingInput!) {
            updateFolderSharing(input: $input) {
                id
                name
            }
        }
    `,
};

const file = {
    create: gql`
        mutation createFile($file: CreateFileInput) {
            createFile(file: $file) {
                id
                filename
            }
        }
    `,
    rename: gql`
        mutation renameFile($id: ID!, $filename: String!) {
            renameFile(id: $id, filename: $filename) {
                id
                filename
            }
        }
    `,
    trash: gql`
        mutation trashFile($id: ID!) {
            trashFile(id: $id) {
                id
                filename
            }
        }
    `,
};
export default {
    department,
    position,
    role,
    user,
    team,
    location,
    task,
    emailTemplate,
    note,
    folder,
    file,
};
