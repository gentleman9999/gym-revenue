import gql from "graphql-tag";

export const AUDIENCES = gql`
    query Audiences($page: Int, $filter: Filter) {
        audiences(page: $page, filter: $filter) {
            data {
                id
                name
                editable
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

export const AUDIENCE_PERMISSIONS = gql`
    query Audiences {
        audiences {
            data {
                id
                editable
            }
        }
    }
`;

export const AUDIENCE_EDIT = gql`
    query Audience($id: ID) {
        audience(id: $id) {
            id
            name
            filters {
                lead_type_id
                membership_type_id
            }
            created_at
            updated_at
            editable
        }
    }
`;

export const audience = {
    create: gql`
        mutation createAudience($input: CreateAudienceInput) {
            createAudience(input: $input) {
                id
                name
                filters {
                    lead_type_id
                    membership_type_id
                }
                editable
            }
        }
    `,
    update: gql`
        mutation updateAudience($input: UpdateAudienceInput) {
            updateAudience(input: $input) {
                id
                name
                filters {
                    lead_type_id
                    membership_type_id
                }
                editable
            }
        }
    `,
};
