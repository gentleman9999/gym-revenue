<template>
    <div class="chat-content-container">
        <chat-content-header :messenger="messenger" />
        <div class="flex flex-row h-full">
            <div class="chat-area">
                <div class="chat-msg-container">
                    <chat-message v-for="data in messages" :key="data.id" />
                </div>
                <message-input />
            </div>
        </div>
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
</style>
<script setup>
import ChatContentHeader from "./chat-content-header.vue";
import ChatMessage from "./chat-message.vue";
import MessageInput from "./message-input.vue";
const props = defineProps({
    messenger: Object,
});

const messages = [
    {
        id: 1,
        msg: "Hello, world",
        name: "Tommy Lee",
    },
];
</script>
