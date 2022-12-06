import { setProperty } from "dot-prop";
/**
 * transform Apollo Validation Error object to Inertia style format
 * @param error
 * @returns Object | {null}
 */
export const transformGqlValidationErrorsToInertiaStyle = (error) => {
    if (!error) {
        return {};
    }
    //remove envelope
    if (Object.keys(error).length === 1) {
        error = Object.values(error)[0];
    }
    //pluck first error in array
    Object.entries(error).forEach(([field, errors]) => {
        error[field] = errors[0];
    });

    return error;
};
