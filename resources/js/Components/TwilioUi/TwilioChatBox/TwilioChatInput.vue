<template>
    <div class="twilio-chat-input-container">
        <input
            v-model="msg"
            class="twilio-chat-input"
            @keypress="handleKeyPress"
        />
        <Button class="w-1/4" secondary size="xs" @click="sendMsg">Send</Button>
    </div>
</template>
<style setup>
.twilio-chat-input-container {
    @apply flex flex-row justify-between p-2 border-t border-secondary;
}
.twilio-chat-input {
    @apply w-2/3 bg-transparent rounded border border-secondary;
}
</style>
<script setup>
import { ref, defineEmits } from "vue";
import Button from "@/Components/Button.vue";

const msg = ref(null);

const emit = defineEmits(["sendMsg"]);

const handleKeyPress = (e) => {
    if (e.key === "Enter") {
        sendMsg();
    }
};
const sendMsg = () => {
    emit("sendMsg", msg.value);
    msg.value = "";
};
</script>
