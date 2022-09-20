<template>
    <div class="chat-message-container" :class="{ mine: isMine }">
        <div class="chat-message">{{ message.body }}</div>
        <div class="text-xs">
            {{ message.creator }} <small>{{ message.dateFormatted() }}</small>
        </div>
    </div>
</template>
<script setup>
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";

defineProps({
    message: MessageInfo,
    isMine: Boolean,
});
</script>
<style scoped>
.chat-message-container {
    @apply flex flex-col w-fit space-y-1;
}
.chat-message {
    @apply rounded px-4 py-1 bg-secondary self-start;
}
.mine,
.mine .chat-message {
    @apply ml-auto;
}
.mine .chat-message {
    @apply bg-accent-800;
}
</style>
