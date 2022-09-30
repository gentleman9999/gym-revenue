<template>
    <div
        @mouseenter="() => (hovered = true)"
        @mouseleave="() => (hovered = false)"
        class="relative"
    >
        <slot></slot>

        <button
            v-if="hovered && !active"
            @click="handler"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-3/4 bg-secondary rounded-full h-6 w-6"
        >
            <font-awesome-icon icon="plus" />
        </button>

        <button
            v-if="hovered && active"
            @click="handler"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-3/4 bg-secondary rounded-full h-6 w-6"
        >
            <font-awesome-icon icon="pen" />
        </button>

        <button
            v-if="hovered && active"
            @click="$emit('reset')"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 translate-y-1/2 text-sm px-2 py-0 bg-neutral border-red-500 border hover:bg-red-500 rounded-md"
        >
            Reset
        </button>

        <span
            v-if="active && !hovered"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-3/4 bg-secondary rounded-full h-6 w-6 flex items-center justify-center"
        >
            <font-awesome-icon icon="check" />
        </span>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { faPlus, faCheck, faPen } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";

library.add(faPlus);
library.add(faCheck);
library.add(faPen);

const props = defineProps({
    handler: {
        type: Function,
        default: () => {},
    },
    active: {
        type: Boolean,
        default: false,
    },
});

const hovered = ref(false);
</script>
