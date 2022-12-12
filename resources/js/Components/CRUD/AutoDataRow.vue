<template>
    <tr
        class="hover"
        @click.prevent.stop="handleClick"
        @dblclick.prevent.stop="handleDoubleClick"
    >
        <td v-for="field in fields" :key="field.id" class="col-span-3 truncate">
            <div class="tabledata">
                <render-field
                    :field="field"
                    :data="data"
                    :base-route="modelName"
                    v-bind="$props"
                />
            </div>
        </td>
        <td
            class="actions"
            v-if="
                actions &&
                (Object.values(actions).length === 0 ||
                    Object.values(actions).filter((action) => action).length)
            "
        >
            <div
                class="tabledata actions"
                v-if="
                    (actions && actions instanceof Array && actions.length) ||
                    Object.entries(actions).length
                "
            >
                <slot name="actions">
                    <crud-actions
                        :actions="actions"
                        :data="data"
                        :base-route="baseRoute"
                        :has-preview-component="hasPreviewComponent"
                        :model-name="modelName"
                        :model-key="modelKey"
                    />
                </slot>
            </div>
        </td>
    </tr>
    <span aria-hidden="true" class="spacer" />
</template>

<script>
import { defineComponent } from "vue";
import DataCard from "./DataCard.vue";
import CrudActions from "./CrudActions.vue";
import { getFields } from "./helpers/getFields";
import RenderField from "./RenderField.vue";
import { preview, edit } from "@/Components/CRUD/helpers/gqlData";
import { getCustomizedFields } from "@/Components/CRUD/helpers/getCustomizedFields";

export default defineComponent({
    inheritAttrs: false,
    components: {
        DataCard,
        CrudActions,
        RenderField,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelKey: {
            type: String,
            default: "Record",
        },
        modelName: {
            type: String,
            default: "Record",
        },
        modelNamePlural: {
            type: String,
        },
        actions: {
            type: [Object, Boolean],
            default: {},
        },
        baseRoute: {
            type: String,
            // required: true,
        },
        hasPreviewComponent: {
            type: Boolean,
        },
        onClick: {
            type: Function,
        },
        onDoubleClick: {
            type: Function,
        },
    },
    setup(props) {
        const fields = getFields(props);
        const customizedFields = getCustomizedFields(fields, props.modelKey);

        let timer = 0;
        let prevent = false;
        const delay = 200;

        const handleClick = () => {
            timer = setTimeout(() => {
                if (!prevent) {
                    if (props?.onClick) {
                        props.onClick();
                    } else {
                        openPreview();
                    }
                }
                prevent = false;
            }, delay);
        };

        const openPreview = () => {
            if (!props.hasPreviewComponent) {
                return;
            }
            preview(props.data.id);
        };
        const handleDoubleClick = () => {
            clearTimeout(timer);
            prevent = true;
            if (props.onDoubleClick === false) {
                return;
            }
            if (props?.onDoubleClick) {
                props.onDoubleClick({ data: props.data });
                return;
            }
            edit(props.data.id);
        };

        return { fields: customizedFields, handleClick, handleDoubleClick };
    },
});
</script>

<style scoped>
td {
    @apply text-center bg-neutral-focus text-white my-2 mx-8 whitespace-nowrap overflow-hidden text-ellipsis py-4;

    div.tabledata {
        @apply p-2 whitespace-nowrap overflow-hidden text-ellipsis;
    }

    div.actions {
        @apply !overflow-visible;
    }
}

td.actions {
    @apply !overflow-visible;
}

td:first-of-type {
    @apply border-l border-secondary;
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
}

td:last-of-type {
    @apply border-r border-secondary;
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

td:not(:last-of-type) {
    div.tabledata {
        @apply border-r-2 border-secondary;
    }
}

td {
    @apply border-t border-b border-secondary;
}

span.spacer {
    @apply block h-3;
}
</style>
