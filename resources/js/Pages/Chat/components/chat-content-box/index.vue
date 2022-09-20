<template>
    <div class="chat-content-container">
        <div v-if="!conversation" class="text-center mt-10 text-2xl">
            No chat selected
        </div>
        <template v-else>
            <div class="chat-content-header">
                <span>{{ conversation.name }}</span>
            </div>
            <div class="flex flex-row h-full">
                <div class="chat-area">
                    <div class="chat-msg-container">
                        <chat-message
                            v-for="(message, i) in conversation.messages"
                            :key="i"
                            :message="message"
                            :is-mine="message.sid === conversation.sid"
                        />
                    </div>
                    <message-input />
                </div>
            </div>
        </template>
    </div>
</template>
<style scoped>
.chat-content-container {
    @apply flex flex-col w-full justify-between;
}
.chat-msg-container {
    @apply flex flex-col space-y-2 p-4;
}
.chat-area {
    @apply flex flex-col w-full border-r border-base-content/70 h-full justify-between;
}
.chat-content-header {
    @apply flex flex-col border-b w-full pt-3 font-extrabold border-neutral-content/20 text-center h-14;
}
</style>
<script setup>
import ChatMessage from "./chat-message.vue";
import MessageInput from "./message-input.vue";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
const props = defineProps({
    conversation: ConversationMsg,
});
</script>
