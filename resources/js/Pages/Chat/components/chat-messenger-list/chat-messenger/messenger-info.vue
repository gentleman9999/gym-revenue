<template>
    <div class="chat-messenger-info">
        <div class="chat-messenger-name">
            {{ messenger.first_name }} {{ messenger.last_name }}
        </div>
        <div>You:Hi</div>
    </div>
</template>
<style scoped>
.chat-messenger-info {
    @apply flex flex-col space-y-2;
}
.chat-messenger-name {
    @apply flex flex-row space-x-2;
}
</style>
<script setup>
const props = defineProps({
    messenger: Object,
});
</script>
