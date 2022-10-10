<template>
    <button type="button" @click.prevent.stop="handleClick" class="capitalize">
        {{ field.label }}
        <font-awesome-icon
            v-if="direction === 'DESC'"
            :icon="['fas', 'chevron-down']"
            size="sm"
        />
        <font-awesome-icon
            v-if="direction === 'ASC'"
            :icon="['fas', 'chevron-up']"
            size="sm"
        />
    </button>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faChevronUp, faChevronDown } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faChevronUp, faChevronDown);

export default defineComponent({
    components: {
        FontAwesomeIcon,
    },
    props: {
        field: {
            type: Object,
            required: true,
        },
        form: {
            type: Object,
            required: true,
        },
    },
    setup(props, { emit }) {
        const modelValue = ref(null);
        const direction = ref(null);
        const handleClick = () => {
            modelValue.value = props.field.name;
            if (direction.value === null || direction.value === "DESC") {
                direction.value = "ASC";
            } else {
                direction.value = "DESC";
            }
            emit("update:modelValue", modelValue.value);
            emit("update:direction", direction.value);
        };
        const clear = () => {
            modelValue.value = null;
            direction.value = null;
        };
        watch([props.form], () => {
            if (props.form.sort !== props.field.name) {
                clear();
            }
        });
        return { handleClick, clear, direction };
    },
});
</script>
