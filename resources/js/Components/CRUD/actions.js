import { Inertia } from "@inertiajs/inertia";

export const defaults = {
    edit: {
        label: "Edit",
        handler: ({ baseUrl, data }) =>
            Inertia.visit(route(`${baseUrl}.edit`, data.id)),
    },
    trash: {
        label: "Trash",
        handler: ({ baseUrl, data }) =>
            Inertia.delete(route(`${baseUrl}.trash`, data.id)),
        shouldRender: ({ data }) => data.deleted_at === null,

    },
    restore: {
        label: "Restore",
        handler: ({ baseUrl, data }) =>
            Inertia.post(route(`${baseUrl}.restore`, data.id)),
        shouldRender: ({ data }) => data.deleted_at !== null,
    },
};
