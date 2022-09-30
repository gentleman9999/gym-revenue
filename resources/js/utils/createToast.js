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
 * @param {string} text message supplied to toast window with a default if supplied value is nullish
 * @param {object?} opts override default options
 */
export const generateToast = (text, opts = {}) => {
    return useToast()(text, opt(opts));
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
