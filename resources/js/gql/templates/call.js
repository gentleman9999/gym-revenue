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

export const CALL_TEMPLATE_CREATE = gql`
    query CallTemplates {
        callTemplates {
            data {
                id
                name
                script
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
            thumbnail {
                key
                url
            }
        }
    }
`;

export const callTemplate = {
    create: gql`
        mutation createCallScriptTemplate(
            $input: CreateCallScriptTemplateInput
        ) {
            createCallScriptTemplate(input: $input) {
                id
                name
                script
                use_once
                active
            }
        }
    `,
    update: gql`
        mutation updateCallScriptTemplate(
            $input: UpdateCallScriptTemplateInput
        ) {
            updateCallScriptTemplate(input: $input) {
                id
                name
                script
                use_once
                active
            }
        }
    `,
};
