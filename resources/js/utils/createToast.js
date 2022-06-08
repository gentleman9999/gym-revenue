import { useToast } from "vue-toastification";

const defaults = {
    position: "top-right",
    timeout: 7500,
    pauseOnHover: false,
    pauseOnFocusLoss: false,
    closeOnClick: true,
};

const opt = (added) => Object.assign(defaults, added);

/**
 *
 * @param {string} kind type of toast with a default of 'info'
 * @param {string} text message supplied to toast window with a default if supplied value is nullish
 * @param {object?} opts override default options
 */
export const generateToast = (kind, text, opts = {}) => {
    let toastType =
        kind === "warning"
            ? "warning"
            : kind === "error"
            ? "error"
            : kind === "success"
            ? "success"
            : "info";
    let toastText =
        typeof text === "string" && text.length > 0
            ? text
            : "You Can't Do That!";
    let component = useToast();
    return component(toastText, { type: toastType, ...opt(opts) });
};

/**
 *  Informative Toast
 * @param {string} text message supplied to toast window
 * @param {object?} opts override default options
 */
export const toastInfo = (text = "Supply A Message", opts = {}) => {
    return useToast().info(text, opt(opts));
};

/**
 *  Success Toast
 * @param {string} text message supplied to toast window
 * @param {object?} opts override default options
 */
export const toastSuccess = (text = "Success!", opts = {}) => {
    return useToast().success(text, opt(opts));
};

/**
 *  Warning Toast
 * @param {string} text message supplied to toast window
 * @param {object?} opts override default options
 */
export const toastWarning = (text = "You Can't Do That!", opts = {}) => {
    return useToast().warning(text, opt(opts));
};

/**
 *  Error Toast
 * @param {string} text message supplied to toast window
 * @param {object?} opts override default options
 */
export const toastError = (text = "Error", opts = {}) => {
    return useToast().error(text, opt(opts));
};
