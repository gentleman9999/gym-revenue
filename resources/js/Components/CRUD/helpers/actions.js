import { Inertia } from "@inertiajs/inertia";

export const defaults = {
    edit: {
        label: "Edit",
        handler: ({ baseRoute, data }) =>
            Inertia.visit(route(`${baseRoute}.edit`, data.id)),
    },
    trash: {
        label: "Trash",
        handler: ({ baseRoute, data }) =>
            Inertia.delete(route(`${baseRoute}.trash`, data.id)),
        shouldRender: ({ data }) => data.deleted_at === null,
    },
    restore: {
        label: "Restore",
        handler: ({ baseRoute, data }) =>
            Inertia.post(route(`${baseRoute}.restore`, data.id)),
        shouldRender: ({ data }) => data.deleted_at !== null,
    },
};
