import gql from "graphql-tag";

const department = {
    create: gql`
        mutation createDepartment($name: String, $positions: [ID]) {
            createDepartment(name: $name, positions: $positions) {
                id
                name
                client_id
            }
        }
    `,
};

export default {
    department,
};
