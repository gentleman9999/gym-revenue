export const LOCATION_TYPES = Object.freeze({
    /** @see App\Enums\LocationTypeEnum::STORE */
    STORE: "store",
});

/**
 * Resolve location type name
 * @param {LocationType} location_type
 * @returns {LocationTypeName}
 */
export const parseLocationTypeDisplayName = (location_type) => {
    switch (location_type.value) {
        case LOCATION_TYPES.STORE:
            return "gym";
        default:
            return location_type.value;
    }
};

/**
 * Format location data types for multiselect component consumption
 * @param {LocationType[]} data
 */
export const formatLocationTypeForSelect = (data) => {
    if (!data instanceof Array) return;
    return data.map((lt) => {
        return {
            value: lt.value?.toUpperCase(),
            label: parseLocationTypeDisplayName(lt),
        };
    });
};

/**
 * @typedef LocationType
 * @property {string} description
 * @property {string} label
 * @property {string|null} value
 */

/**
 * @typedef {"gym"|"office"|"headquarters"} LocationTypeName
 */
