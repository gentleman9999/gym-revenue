import gql from "graphql-tag";

export const CALL_TEMPLATES = gql`
    query CallTemplates($page: Int, $filter: Filter) {
        callTemplates(page: $page, filter: $filter) {
            data {
                id
                name
                script
                active
                team_id
                created_at
                updated_at
                use_once
                creator {
                    id
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

export const CALL_TEMPLATE_EDIT = gql`
    query CallTemplate($id: ID) {
        callTemplate(id: $id) {
            id
            name
            script
            active
            team_id
            created_at
            updated_at
            use_once
        }
    }
`;
