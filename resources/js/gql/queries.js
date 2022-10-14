import gql from "graphql-tag";

const USER = gql`
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

export default {
    user: USER,
    users: USERS,
};
