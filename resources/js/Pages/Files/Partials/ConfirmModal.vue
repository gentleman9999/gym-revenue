<template>
    <h1 class="flex">
        Are you sure to remove
        <div class="item-name">{{ data?.name }}</div>
        ?
    </h1>
    <div v-if="data?.type === 'folder'" class="folder-warning">
        Folder contains {{ data?.count }} file(s)
    </div>
    <div class="confirm-actions">
        <Button
            size="sm"
            error
            class="text-base-content"
            @click="$emit('success')"
            >DELETE</Button
        >
    </div>
</template>
<style scoped>
.item-name {
    @apply text-secondary whitespace-nowrap overflow-hidden text-ellipsis inline-block mx-1 mb-4;
    max-width: 100px;
}
.folder-warning {
    @apply text-center mb-4;
}
.confirm-actions {
    @apply flex flex-row justify-center text-base-content;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
const props = defineProps({
    data: Object,
});
</script>
