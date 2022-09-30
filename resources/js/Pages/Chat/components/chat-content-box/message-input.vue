<template>
    <div class="message-input-container mt-2">
        <textarea
            v-model="msg"
            class="message-input text-sm"
            rows="1"
            @keypress="handleKeyPress"
        />
        <Button secondary size="sm" @click="sendMsg">Send</Button>
    </div>
</template>
<style scoped>
.message-input-container {
    @apply border-t border-neutral-content/20 flex flex-row w-full items-center p-2 space-x-4;
}
.message-input {
    @apply bg-transparent rounded border border-secondary w-11/12;
}
</style>
<script setup>
import { ref, defineEmits, inject } from "vue";
import Button from "@/Components/Button.vue";

const msg = ref(null);
const sendMessage = inject("sendMessage");

/**
 * @param {KeyboardEvent} event
 */
function handleKeyPress(event) {
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        sendMsg();
    }
}
function sendMsg() {
    sendMessage(msg.value);
    msg.value = "";
}
</script>
