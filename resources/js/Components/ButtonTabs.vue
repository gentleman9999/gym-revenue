<template>
    <div class="flex flex-row justify-between mt-2 px-1.5">
        <div
            v-for="(item, index) in items"
            :key="index"
            :class="{
                'text-white cursor-pointer p-1 rounded': true,
                'bg-secondary': index === props.activeIndex,
                'hover:text-secondary': index !== props.activeIndex,
            }"
            @click="activeTab = index"
        >
            {{ item }}
        </div>
    </div>
</template>
<style scoped></style>
<script setup>
import { computed } from "@vue/reactivity";

const props = defineProps({
    items: {
        type: Array,
        default: ["Membership", "Personal Training", "Retail Sales"],
    },
    activeIndex: {
        type: Number,
        default: 0,
    },
    onChange: {
        type: Function,
        default: (ndx) => null,
    },
});

const activeTab = computed({
    get() {
        return props.activeIndex;
    },
    set(value) {
        props.onChange(value);
    },
});
</script>
