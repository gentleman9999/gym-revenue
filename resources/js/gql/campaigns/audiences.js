import gql from "graphql-tag";

export const AUDIENCES = gql`
    query Audiences($page: Int, $filter: Filter) {
        audiences(page: $page, filter: $filter) {
            data {
                id
                name
                filters {
                    lead_type_id
                    membership_type_id
                }
                editable
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

export const AUDIENCE_EDIT = gql`
    query Audience($id: ID) {
        audience(id: $id) {
            id
            name
            filters
            created_at
            updated_at
            editable
        }
    }
`;
