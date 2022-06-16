const fireEvent = (name, options) => {
    return document.dispatchEvent(new CustomEvent(`inertia:${name}`, options));
};

export const fireErrorEvent = (errors) =>
    fireEvent("error", { detail: { errors } });

export const fireSuccessEvent = (page) =>
    fireEvent("success", { detail: { page } });
