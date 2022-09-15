<template>
    <div class="chat-message-container">
        <div class="chat-message">Chat Message</div>
        <div class="text-xs">Tommy</div>
    </div>
</template>
<style scoped>
.chat-message-container {
    @apply flex flex-col w-fit space-y-1;
}
.chat-message {
    @apply rounded px-4 py-1 bg-secondary self-start;
}
</style>
