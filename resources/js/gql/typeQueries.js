import gql from "graphql-tag";

export const LOCATION_TYPES = gql`
    query LocationTypes {
        locationTypes {
            label
            value
            description
        }
    }
`;
