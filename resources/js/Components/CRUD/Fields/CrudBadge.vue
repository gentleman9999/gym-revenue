<template>
    <div class="badge" :class="class">
        {{ text }}
    </div>
</template>
<script>
import { defineComponent } from "vue";
export default defineComponent({
    inheritAttrs: false,
    props: {
        value: {
            type: Boolean,
            required: true,
        },
        truthy: {
            type: String,
            default: "true",
        },
        falsy: {
            type: String,
            default: "false",
        },
        test: {
            type: Function,
        },
        getProps: {
            type: Function,
        },
        data: {
            type: Object,
        },
    },
    setup(props) {
        let renderProps = {};
        let getProps = ({ data, value }) => {
            return !!value
                ? { text: "True", class: "badge-success" }
                : { text: "False", class: "badge-warning" };
        };
        if (props.getProps) {
            getProps = props.getProps;
        }
        renderProps = getProps({ data: props.data, value: props.value });
        return { ...renderProps };
    },
});
</script>
