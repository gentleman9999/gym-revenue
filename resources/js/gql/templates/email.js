import gql from "graphql-tag";

export const EMAIL_TEMPLATES = gql`
    query EmailTemplates($page: Int, $filter: Filter) {
        emailTemplates(page: $page, filter: $filter) {
            data {
                id
                json
                name
                markup
                subject
                thumbnail {
                    key
                    url
                }
                active
                team_id
                created_by_user_id
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
        topolApiKey
    }
`;

export const EMAIL_TEMPLATE_EDIT = gql`
    query EmailTemplate($id: ID) {
        emailTemplate(id: $id) {
            id
            name
            markup
            json
            subject
            thumbnail {
                key
                url
            }
            active

            team_id
            created_by_user_id
            creator {
                id
            }
            created_at
            updated_at
        }
        topolApiKey
    }
`;

export const EMAIL_TEMPLATE_CREATE = gql`
    query EmailTemplate {
        topolApiKey
    }
`;

/** Mutations */
