/** Regex for syncing field state on dynamic fields */
const filterStringRegex =
    /(?:filter\[)(?<field>.*?)]=((?<value>.+?)([&|\n]|$))?/gim;

/**
 * Resolve model name on report to the matching model in the models array
 * @param {String} str - name of the model
 * @returns {Model|null} - will return the correct model if found, otherwise null
 */
export function resolveModel(str) {
    if (!str) return null;
    return models.filter((m) => m.name === str)[0];
}

/** @type {Model[]} */
export const models = [
    {
        id: "users",
        name: "users",
        stringFields: ["email", "first_name", "last_name", "alternate_email"],
    },
    {
        id: "leads",
        name: "leads",
        stringFields: ["email", "first_name", "last_name"],
    },
    {
        id: "members",
        name: "members",
        stringFields: ["email", "first_name", "last_name"],
    },
    {
        id: "leadexport",
        name: "leadexport",
        stringFields: ["email", "first_name", "last_name"],
    },
];

/**
 * Parse filter URL to set state on input fields correctly
 * @param {String} str previously generated filter url to parse
 */
export function parseFilterValues(str) {
    let matches = [...str.matchAll(filterStringRegex)].map((m) => m["groups"]);
    return matches;
}

/**
 * @typedef {Object} Model
 * @property {string} name - matches the back end model name
 * @property {string} id - a unique identifier for component state - for now it's just model name
 * @property {string[]} stringFields - fields we want to enter text in and filter by
 */
