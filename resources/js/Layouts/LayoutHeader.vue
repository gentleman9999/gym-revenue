<template>
    <Head :title="title" v-if="title" />
    <teleport to="#layout-header" v-if="$slots?.default">
        <slot />
    </teleport>
</template>

<script setup>
import { Head } from "@inertiajs/inertia-vue3";

const props = defineProps({
    title: {
        type: String,
        required: false,
    },
});
</script>
