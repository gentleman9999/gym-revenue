<template>
    <div class="chat-container">
        <chat-messenger-list @set-messenger="setMessenger" />
        <chat-content-box :messenger="messenger" />
    </div>
</template>
<style scoped>
.chat-container {
    @apply flex flex-row;
    margin-top: -32px;
    height: calc(100vh - 140px);
}
</style>
<script setup>
import { ref } from "vue";
import ChatContentBox from "./components/chat-content-box/index.vue";
import ChatMessengerList from "./components/chat-messenger-list/index.vue";

const messenger = ref({});

const setMessenger = (val) => {
    messenger.value = val;
};
</script>
