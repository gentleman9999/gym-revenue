<template>
    <div class="message-input-container">
        <input v-model="msg" class="message-input" @keypress="handleKeyPress" />
        <Button secondary size="xs" @click="sendMsg">Send</Button>
    </div>
</template>
<style scoped>
.message-input-container {
    @apply border-t border-neutral-content/20 flex flex-row w-full h-16 items-center px-4 space-x-4;
}
.message-input {
    @apply bg-transparent rounded border border-secondary;
    width: -webkit-fill-available;
}
</style>
<script setup>
import { ref, defineEmits, inject } from "vue";
import Button from "@/Components/Button.vue";

const msg = ref(null);
const sendMessage = inject("sendMessage");

const handleKeyPress = (e) => {
    if (e.key === "Enter") {
        sendMsg();
    }
};
const sendMsg = () => {
    sendMessage(msg.value);
    msg.value = "";
};
</script>
