<template>
    <tr class="hover">
        <td v-for="(field, index) in fields" class="col-span-3 truncate">
            <render-field :field="field" :data="data" :model-name="modelName" />
        </td>
        <td
            v-if="
                Object.values(actions).length === 0 ||
                Object.values(actions).filter((action) => action).length
            "
        >
            <slot name="actions">
                <crud-actions
                    :actions="actions"
                    :data="data"
                    :base-url="baseUrl"
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

export default defineComponent({
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
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        actions: {
            type: Object,
            default: {},
        },
        baseUrl: {
            type: String,
        },
    },
    setup(props) {
        const fields = getFields(props);
        console.log({ fields: fields.value });

        let __baseUrl =
            props.baseUrl || props.modelNamePlural || props.modelName + "s";

        return { fields, baseUrl: __baseUrl };
    },
});
</script>
