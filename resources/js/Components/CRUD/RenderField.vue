<template>
    <component
        v-if="field.component"
        :is="field.component"
        v-bind="{ [modelName]: data, data, value , ...field.props }"
    >
        {{ value }}
    </component>
    <template v-else>
        <vue-json-pretty
            v-if="isObject(data[field.label]) && field.transformNoop"
            :data="data[field.label]"
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
        modelName: {
            type: Object,
            required: true,
        },
        data: {
            type: Object,
            required: true,
        },
    },
    components: {
        VueJsonPretty,
    },
    setup(props) {
        const value = props.field.transform(props.data[props.field.label]);
        return { isObject, value };
    },
});
</script>
