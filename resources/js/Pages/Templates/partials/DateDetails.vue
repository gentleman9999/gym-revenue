<template>
    <div
        v-if="!trash_template"
        class="text-xs my-2 h-10 whitespace-nowrap px-2"
    >
        <p class="italic">
            Created:
            <span class="text-secondary">{{
                transformDate(new Date(template?.created_at))
            }}</span>
        </p>
        <p class="italic" v-if="template?.created_at !== template?.updated_at">
            Updated:
            <span class="text-secondary">{{
                transformDate(new Date(template?.updated_at))
            }}</span>
        </p>
    </div>
    <div v-if="trash_template" class="text-xs my-2 whitespace-nowrap px-2">
        <p class="italic">
            Trashed:
            <span class="text-secondary">{{
                transformDate(new Date(template?.deleted_at))
            }}</span>
        </p>
    </div>
</template>

<script setup>
import { transformDate } from "@/utils/transformDate";

const props = defineProps({
    template: {
        type: Object,
        default: {},
    },
    trash_template: {
        type: Boolean,
        default: false,
    },
});
</script>
