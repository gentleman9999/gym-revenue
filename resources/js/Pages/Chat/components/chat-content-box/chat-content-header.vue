<template>
    <div class="chat-content-header">
        <span>{{ messenger.first_name }} {{ messenger.last_name }}</span>
    </div>
</template>
<style scoped>
.chat-content-header {
    @apply flex flex-col border-b w-full border-neutral-content/20 text-center h-14;
}
</style>
<script setup>
const props = defineProps({
    messenger: {
        type: Object,
    },
});
</script>
