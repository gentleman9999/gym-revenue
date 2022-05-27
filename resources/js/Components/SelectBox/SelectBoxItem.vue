<template>
    <div
        class="hover:text-white hover:bg-secondary px-4 py-2"
        :class="{
            'bg-secondary text-white': selected,
        }"
        @click="handleClick"
    >
        {{ label }}
    </div>
</template>
<script setup>
const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    selected: {
        type: Boolean,
        default: false,
    },
    onClick: {
        type: Function,
        default: () => null,
    },
});
const handleClick = (e) => {
    props.onClick(props.label);
};
</script>
