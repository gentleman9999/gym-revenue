import gql from "graphql-tag";

export const SMS_TEMPLATES = gql`
    query SmsTemplates($page: Int, $filter: Filter) {
        smsTemplates(page: $page, filter: $filter) {
            data {
                id
                name
                markup
                active
                team_id
                updated_at
                created_at
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

export const SMS_TEMPLATE_EDIT = gql`
    query SmsTemplate($id: ID) {
        smsTemplate(id: $id) {
            id
            name
            markup
            active
            team_id
            updated_at
            created_at
        }
    }
`;
