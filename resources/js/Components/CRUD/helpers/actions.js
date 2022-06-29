import { Inertia } from "@inertiajs/inertia";
import { computed } from "vue";
import { merge } from "lodash";
import { preview } from "@/Components/CRUD/helpers/previewData";

export const defaults = Object.freeze({
    edit: {
        label: "Edit",
        handler: ({ baseRoute, data }) =>
            Inertia.visitInModal(route(`${baseRoute}.edit`, data.id), {
                reloadOnClose: true,
                redirectInModal: true,
            }),
    },
    trash: {
        label: "Trash",
        handler: ({ baseRoute, data }) =>
            Inertia.delete(route(`${baseRoute}.trash`, data.id)),
        shouldRender: ({ data }) => data?.deleted_at === null,
    },
    restore: {
        label: "Restore",
        handler: ({ baseRoute, data }) =>
            Inertia.post(route(`${baseRoute}.restore`, data.id)),
        shouldRender: ({ data }) => data?.deleted_at !== null,
    },
});

export const getDefaults = ({ hasPreviewComponent }) => {
    if (!hasPreviewComponent) {
        return defaults;
    }
    return merge(
        {
            preview: {
                label: "Preview",
                handler: async ({ baseRoute, data }) => {
                    // const routeName = `${baseRoute}.view`;
                    // const resp = await axios.get(
                    //     route(routeName, data.id),
                    // );
                    // setPreviewData(resp.data);
                    preview(baseRoute, data["id"]);
                },
            },
        },
        defaults
    );
};

export const getActions = (props) => {
    return computed(() => {
        if (!props.actions) {
            return [];
        }
        if (typeof props.actions === "array" || props.actions[0]) {
            console.log("props.actions is array, returning");
            return props.actions;
        }
        const defaults = getDefaults(props);
        const merged = merge({ ...defaults }, { ...props.actions });
        console.log({ merged });
        return Object.values(merged)
            .filter((action) => action)
            .filter((action) =>
                action?.shouldRender ? action.shouldRender(props) : true
            );
    });
};
