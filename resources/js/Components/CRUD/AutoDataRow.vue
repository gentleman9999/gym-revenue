<template>
    <tr class="hover" @click.prevent.stop="handleClick" @dblclick.prevent.stop="handleDoubleClick">
        <td v-for="(field, index) in fields" class="col-span-3 truncate">
            <render-field
                :field="field"
                :data="data"
                :base-route="modelName"
                v-bind="$props"
            />
        </td>
        <td
            v-if="
                actions &&
                (Object.values(actions).length === 0 ||
                    Object.values(actions).filter((action) => action).length)
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
        </td>
    </tr>
</template>

<script>
import { defineComponent } from "vue";
import DataCard from "./DataCard";
import CrudActions from "./CrudActions";
import { getFields } from "./helpers/getFields";
import RenderField from "./RenderField";
import {Inertia} from "@inertiajs/inertia";
import {preview} from "@/Components/CRUD/helpers/previewData";
import {getCustomizedFields} from "@/Components/CRUD/helpers/getCustomizedFields";

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
            type: Boolean
        },
        onClick: {
            type: Function
        },
        onDoubleClick: {
            type: Function
        }
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
                    if (props.onClick) {
                        props.onClick();
                    }else{
                        openPreview();
                    }
                }
                prevent = false;
            }, delay);
        }

        const openPreview = () => {
            if(!props.hasPreviewComponent){
                return;
            }
            preview(props.baseRoute, props.data.id);
        }
        const handleDoubleClick = () => {
            clearTimeout(timer);
            prevent = true;
            if(props.onDoubleClick){
                props.onDoubleClick();
                return;
            }
            Inertia.visit(route(`${props.baseRoute}.edit`, props.data.id));
        }


        return {fields: customizedFields, handleClick, handleDoubleClick};
    },
});
</script>
