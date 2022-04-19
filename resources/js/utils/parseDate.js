//Because Safari is the new IE.
export const parseDate = (date) => {
    const parsed = Date.parse(date);
    if (!isNaN(parsed)) {
        return parsed;
    }

    return Date.parse(date.replace(/-/g, "/").replace(/[a-z]+/gi, " "));
};
