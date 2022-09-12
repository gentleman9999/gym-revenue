<template>
    <div class="twilo-ui-container">
        <twilo-chat-box :showChat="showChat" @toggle="toggleChat" />
        <twilo-button v-if="!showChat" @toggle="toggleChat" />
    </div>
</template>
<style scoped>
.twilo-ui-container {
    @apply fixed bottom-8 right-8 flex flex-col items-end;
}
</style>
<script setup>
import { ref } from "vue";
import TwiloButton from "./TwiloButton.vue";
import TwiloChatBox from "./TwiloChatBox/index.vue";
import { TransitionRoot } from "@headlessui/vue";

const showChat = ref(false);

const toggleChat = () => {
    showChat.value = !showChat.value;
};
</script>
