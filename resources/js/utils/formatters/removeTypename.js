/**
 * Removes __typename fields from objects recursively
 * @param {Object} data form object or whatever that has __typename fields on it that you want gone
 */
export function removeTypename(data) {
    for (const field in data) {
        if (field === "__typename") delete data[field];
        else if (typeof data[field] === "object") removeTypename(data[field]);
    }
}
