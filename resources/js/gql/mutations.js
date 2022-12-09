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

export default {
    department,
    position,
};
