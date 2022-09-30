/**
 * Returns the type of template the passed object resembles - or null if none found
 * @param {Object} t - object to test for template resemblance
 * @returns {"email"|"call"|"sms"|null} - the type of tempplate in string format or null if no resemblance found
 */
export function resolveTemplateType(t) {
    let templateType = null;

    let email = isEmailTemplate(t);
    let call = isCallTemplate(t);
    let sms = isSmsTemplate(t);

    switch (true) {
        case email:
            templateType = "Email";
            break;
        case call:
            templateType = "Call";
            break;
        case sms:
            templateType = "SMS";
            break;
        default:
            templateType = null;
            break;
    }

    return templateType;
}

/**
 * Is the object i'm passing a call template?
 * @param {Object} t template object to test
 * @returns {Boolean} whether the object received resembles a call template
 */
export function isCallTemplate(t = null) {
    if (!t || t["markup"]) return false;
    if (typeof t["script"] === "string") return true;
    return false;
}

/**
 * Is the object i'm passing an email template?
 * @param {Object} t template object to test
 * @returns {Boolean} whether the object received resembles an email template
 */
export function isEmailTemplate(t = null) {
    if (!t || typeof t["json"] !== "object") return false;
    if (typeof t["markup"] === "string") return true;
    return false;
}

/**
 * Is the object i'm passing an sms template?
 * @param {Object} t template object to test
 * @returns {Boolean} whether the object received resembles a sms template
 */
export function isSmsTemplate(t = null) {
    if (!t || typeof t["json"] === "object") return false;
    if (typeof t["markup"] === "string") return true;
    return false;
}

/**
 * @note
 *  I've read from multiple sources that text requires a whitespace-nowrap and
 *  overflow-hidden, but I cannot find this anywhere in the actual CSS specification.
 *
 *  @see https://www.w3.org/TR/css-overflow-3/#text-overflow
 *
 *  However browsers may implement it differently and it seems widely accepted,
 *  so unless I just don't see it anywhere, we can just simulate multiline
 *  text ellipsis
 */

/**
 * Truncate a string with ellipsis
 * @param {String} str string we'll ellipse
 * @param {Number} max maximum string size allowed before ellipse can occur, default 90
 */
export function truncateEllipsis(str, max = 90) {
    let truncSize = max + 3 + 1;
    if (str.length <= truncSize) return str;

    let substr = str.substring(0, max);

    return substr + " ...";
}

/** animation helpers */
export const lerp = (x, y, a) => x * (1 - a) + y * a;
export const clamp = (a, min = 0, max = 1) => Math.min(max, Math.max(min, a));
export const invlerp = (x, y, a) => clamp((a - x) / (y - x));
export const range = (x1, y1, x2, y2, a) => lerp(x2, y2, invlerp(x1, y1, a));
