/**
 * Transform dates into strings with a consistent format.
 *  format: YYYY-MM-DD HH:MM:SS'
 * @param {Date|string} date
 * @returns {Date} transformed date string
 */
export const transformDate = (date) => {
    if (typeof date === "string") {
        /**TODO:sometimes we are passed a string here**/
    }
    if (!date?.toISOString) return date;
    return date.toISOString().slice(0, 19).replace("T", " ");
};

/** potential work around? */
// export const transformDate = (date) => {
//     const d = date instnaceof Date ? d : new Date(date);
//     return d.toISOString().slice(0, 19).replace("T", " ");
// };
