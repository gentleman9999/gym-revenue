<template>
    <div
        class="chat-message-container mb-1 text-sm"
        :class="{ mine: isMine }"
        :title="message.dateFormatted()"
    >
        <div class="chat-message whitespace-pre-wrap">{{ message.body }}</div>
        <div
            v-if="hasMultiParticipants && !isMine"
            class="text-[9px] text-gray-300 author"
        >
            {{ message.creator }}
        </div>
    </div>
</template>
<script setup>
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";

defineProps({
    message: MessageInfo,
    isMine: Boolean,
    hasMultiParticipants: Boolean,
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
    @apply bg-green-700;
}
.author {
    margin: 0 !important;
}
</style>
