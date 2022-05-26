<template>
    <div :class="derivedClass">
        <div
            v-if="label"
            class="absolute top-[-36px] text-sm font-bold rounded-t px-3 py-2 text-white bg-secondary"
        >
            {{ label }}
        </div>
        <div
            v-if="header"
            class="flex flex-row justify-between items-center mb-3"
        >
            <div class="text-secondary text-lg text-bold">{{ header }}</div>
            <div class="flex flex-row space-x-2 cursor-pointer">
                <favorite-btn
                    v-if="options.favorite"
                    :value="isFavorite"
                    :onChange="toggleFavorite"
                />
                <span class="flex items-center w-4" v-if="options.collapse">
                    <img src="/img/arrow-up.svg" />
                </span>
            </div>
        </div>
        <slot></slot>
    </div>
</template>
<script setup>
import { computed } from "@vue/reactivity";
import { ref } from "vue";
import FavoriteBtn from "@/Components/FavoriteBtn";
const props = defineProps({
    class: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    header: {
        type: String,
        default: "",
    },
    options: {
        type: Object,
        default: {
            collapse: true,
            favorite: true,
        },
    },
});
const derivedClass = computed({
    get() {
        return `px-4 py-3 border border-secondary rounded-lg bg-neutral-900 relative mb-3 ${
            props.class
        } ${props.label ? "mt-9" : ""}`;
    },
});
const isFavorite = ref(false);
const toggleFavorite = (value) => {
    isFavorite.value = value;
};
</script>
