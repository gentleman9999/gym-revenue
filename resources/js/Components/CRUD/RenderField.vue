<template>
    <component
        v-if="field.component"
        :is="field.component"
        v-bind="{ ...field.props, [modelKey]: data, data, value , field }"
    >
        {{ value }}
    </component>
    <template v-else>
        <vue-json-pretty
            v-if="isObject(data[field.name]) && field.transformNoop"
            :data="data[field.name]"
        />
        <span v-else :title="value">
            {{ value }}
        </span>
    </template>
</template>

<script>
import { defineComponent } from "vue";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import { isObject } from "lodash";

export default defineComponent({
    props: {
        field: {
            type: Object,
            required: true,
        },
        baseRoute: {
            type: String,
            // required: true,
        },
        data: {
            type: Object,
            required: true,
        },
        modelName:{
            type: String,
            required: true
        },
        modelKey: {
            type: String,
            required: true
        },
    },
    components: {
        VueJsonPretty,
    },
    setup(props) {
        const value = props.field.transform(props.data[props.field.name]);
        return { isObject, value };
    },
});
</script>
