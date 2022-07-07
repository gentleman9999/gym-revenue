/**
 * retuns an object with the detected operation type, and form
 * data with only the fields specified
 * @param fields
 * @param data
 * @returns {{operation: string, object: {[p: string]: any}}}
 */
export const getFormObjectFromData = (fields, data) => {
    let object;
    let operation;
    if (data) {
        object = Object.fromEntries(
            Object.entries(data).filter(([key]) => fields.includes(key))
        );
        operation = "Update";
    } else {
        object = Object.fromEntries(fields.map((field) => [field, null]));
        operation = "Create";
    }
    return {
        operation,
        object,
    };
};
