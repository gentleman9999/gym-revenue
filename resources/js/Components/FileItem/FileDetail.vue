<template>
    <div class="w-1/6">{{ prettyBytes(file.size) }}</div>
    <div class="w-1/6 uppercase">
        {{ file.extension ? file.extension : "unknown" }}
    </div>
    <div class="w-4/6">{{ defaultTransform.created_at(file.created_at) }}</div>
</template>
<style scoped></style>
<script setup>
import prettyBytes from "pretty-bytes";
import { defaults as defaultTransform } from "@/Components/CRUD/helpers/transforms";
const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
});
</script>
