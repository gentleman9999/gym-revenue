import states from "@/Pages/Comms/States/statesOfUnited";

/**
 *
 * @param {State[]} data list of states to transform
 * @returns
 */
export function formatStatesForSelect(data) {
    if (!data instanceof Array) return;
    return data.map((state) => {
        return {
            value: state?.name,
            label: state?.abbreviation?.toUpperCase(),
        };
    });
}

/**
 * Preformatted for selection boxes
 */
export const preformattedForSelect = formatStatesForSelect(states);

/**
 * @typedef {Object} State
 * @property {string} name - full name of the state
 * @property {string} abbreviation - short form two letter abbreviation, ie: TX for texas.
 */
