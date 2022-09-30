<template>
    <div class="twilio-ui-container">
        <twilio-chat-box :showChatBox="showChatBox" @toggle="toggleChat" />
        <twilio-button v-if="!showChatBox" @toggle="toggleChat" />
    </div>
</template>
<style scoped>
.twilio-ui-container {
    @apply fixed bottom-8 right-8 flex flex-col items-end;
}
</style>
<script setup>
import { ref } from "vue";
import TwilioButton from "./TwilioButton.vue";
import TwilioChatBox from "./TwilioChatBox/index.vue";
import { TransitionRoot } from "@headlessui/vue";

const showChatBox = ref(false);

const toggleChat = () => {
    showChatBox.value = !showChatBox.value;
};
</script>
