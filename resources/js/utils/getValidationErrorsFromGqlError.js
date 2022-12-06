import { setProperty } from "dot-prop";
/**
 * extracts the validation errors from the Apollo GraphQL Errors
 * @param error
 * @returns Object | {null}
 */
export const getValidationErrorsFromGqlError = (error) => {
    let validationErrors = {};
    let hasValidationErrors = false;
    const { graphQLErrors } = error;

    if (graphQLErrors) {
        graphQLErrors.forEach(({ extensions }) => {
            if (extensions?.category === "validation") {
                Object.entries(graphQLErrors[0].extensions.validation).forEach(
                    ([fqn, fieldErrors]) => {
                        setProperty(validationErrors, fqn, fieldErrors);
                    }
                );
                hasValidationErrors = true;
            }
        });
    }

    return hasValidationErrors ? validationErrors : null;
};
