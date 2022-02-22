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
        </template>
        <div v-for="(field, index) in fields" class="col-span-3 truncate">
            <div class="text-xs text-gray-500">
                {{ field.label }}
            </div>
            <render-field :field="field" :data="data" :base-route="baseRoute" :model-name="modelName" :model-key="modelKey"/>
        </div>
    </data-card>
</template>

<script>
import {defineComponent, computed} from "vue";
import DataCard from "./DataCard";
import CrudActions from "./CrudActions";
import RenderField from "./RenderField";
import {getFields} from "./helpers/getFields";

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
        baseRoute: {
            type: String,
            // required: true,
        },
        modelName: {
            type: String,
            default: 'Record'
        },
        modelKey: {
            type: String,
            required: true
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: [Object, Boolean],
            default: {},
        },
        hasPreviewComponent: {
            type: Boolean
        }
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

        const __fields = computed(() => {
            return fields.value.filter(({name}) => {
                return name !== titleKey;
            })
        })


        return {fields: __fields, title: __title};
    },
});
</script>
