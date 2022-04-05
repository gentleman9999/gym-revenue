<template>
    <tr class="hover">
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
        }
    },
    setup(props) {
        const fields = getFields(props);
        const customizedFields = getCustomizedFields(fields, props.modelKey);


        return { fields: customizedFields };
    },
});
</script>
