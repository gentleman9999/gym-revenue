<template>
    <div class="form-control">
        <slot name="label"><label :for="$attrs.id" class="label">{{ label }}</label></slot>
        <textarea
            class="form-control w-full"
            rows="4"
            cols="40"
            :maxlength="charLimit"
            v-bind="$attrs"
            @input="handleInput"
            :value="modelValue"
        ></textarea>
        <div class="col-md-12" style="text-align: right">
            <small>Character Count - {{ charsUsed }}/{{ charLimit }}</small>
        </div>
    </div>
</template>

<script>
import { computed, defineComponent, mounted, ref } from "vue";

export default defineComponent({
    props: {
        charLimit: {
            type: Number,
            default: 130,
        },
        modelValue:{
            type: String
        },
        label:{
            type: String,
            default: "Message"
        }
    },
    emits: ["done"],
    setup(props, { emit, attrs }) {
        const node = ref(null);
        const charsUsed = ref(0);

        const handleInput = (event) => {
            charsUsed.value = event.target.value.length;
            emit('update:modelValue', event.target.value);
        };

        // mounted(node.value);

        // const charsUsed = computed(() => node.message?.length || 0);
        const charsLeft = computed(
            () => props.charLimit - (charsUsed.value || 0)
        );

        return { charsUsed, charsLeft, node, handleInput };
    },
});
</script>
