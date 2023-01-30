/**
 * Resolve border color level out of three values
 * low, medium, and high. case insensitive
 * @param {BorderColorLevelIndicator?} level
 * @returns {BorderColorLevel} as css style strings
 */
export const resolveBorderColorLevel = (level) => {
    let color = "transparent";

    switch (String(level).toUpperCase()) {
        case "HIGH":
            color = "green";
        case "MEDIUM":
            color = "yellow";
        case "LOW":
            color = "red";
    }

    return {
        "border-color": color,
        "border-width": "5px",
    };
};

/**
 * @typedef {'High'|'Medium'|'Low'} BorderColorLevelIndicator
 */

/**
 * @typedef {Object} BorderColorLevel
 * @property {BorderColorLevelIndicator} color
 * @property {string} width
 */
