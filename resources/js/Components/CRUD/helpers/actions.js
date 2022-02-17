import {Inertia} from "@inertiajs/inertia";
import {computed} from "vue";
import {merge} from "lodash";
import {setPreviewData} from "@/Components/CRUD/helpers/previewData";

export const defaults = Object.freeze({
    edit: {
        label: "Edit",
        handler: ({baseRoute, data}) =>
            Inertia.visit(route(`${baseRoute}.edit`, data.id)),
    },
    trash: {
        label: "Trash",
        handler: ({baseRoute, data}) =>
            Inertia.delete(route(`${baseRoute}.trash`, data.id)),
        shouldRender: ({data}) => data.deleted_at === null,
    },
    restore: {
        label: "Restore",
        handler: ({baseRoute, data}) =>
            Inertia.post(route(`${baseRoute}.restore`, data.id)),
        shouldRender: ({data}) => data.deleted_at !== null,
    },
});

export const getDefaults = ({hasPreviewComponent}) => {
    if (!hasPreviewComponent) {
        return defaults;
    }
    return merge({
        preview: {
            label: "Preview",
            handler: async ({baseRoute, data}) => {
                const routeName = `${baseRoute}.view`;
                const resp = await axios.get(
                    route(routeName, data.id),
                );
                setPreviewData(resp.data);
                console.log({routeName, resp});
            },
        }
    }, defaults);
}

export const getActions = (props) => {
    return computed(() => {
        if (!props.actions) {
            return [];
        }
        const defaults = getDefaults(props);
        return Object.values(merge({...defaults}, {...props.actions}))
            .filter((action) => action)
            .filter((action) =>
                action?.shouldRender ? action.shouldRender(props) : true
            );
    });
};
