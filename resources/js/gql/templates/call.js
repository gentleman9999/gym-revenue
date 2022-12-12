import gql from "graphql-tag";

export const CALL_TEMPLATES = gql`
    query CallTemplates($page: Int, $filter: Filter) {
        callTemplates(page: $page, filter: $filter) {
            data {
                id
                name
                script
                thumbnail
                active
                client_id
                team_id
                created_by_user_id
                creator {
                    id
                }
                created_at
                updated_at
                use_once
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
            thumbnail
            active
            client_id
            team_id
            created_by_user_id
            creator {
                id
            }
            created_at
            updated_at
            use_once
        }
    }
`;
