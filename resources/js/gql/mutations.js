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
export default {
    department,
    position,
    role,
};
