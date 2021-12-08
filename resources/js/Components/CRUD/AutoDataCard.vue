<template>
    <data-card>
        <template #title>
            <slot name="title">
                <div :title="title">
                    {{ title }}
                </div>
            </slot>
        </template>
        <template
            #actions
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
        </template>
        <div v-for="(field, index) in fields" class="col-span-3 truncate">
            <div class="text-xs text-gray-500">
                {{ field.label }}
            </div>
            <render-field :field="field" :data="data" :model-name="modelName" />
        </div>
    </data-card>
</template>

<script>
import { defineComponent } from "vue";
import DataCard from "./DataCard";
import CrudActions from "./CrudActions";
import RenderField from "./RenderField";
import { getFields } from "./helpers/getFields";

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
        titleField: {
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
        let __title;
        if (props.titleField) {
            __title = props.data[props.titleField];
        } else {
            __title = props.data.name || props.data.id;
        }

        const fields = getFields(props);

        let titleKey = props.titleField;
        if (!titleKey) {
            titleKey = props.data.name ? "name" : "id";
        }

        let __baseUrl =
            props.baseUrl || props.modelNamePlural || props.modelName + "s";

        return { fields, title: __title, baseUrl: __baseUrl };
    },
});
</script>
