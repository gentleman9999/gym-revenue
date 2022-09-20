<template>
    <div class="messenger-container">
        <div class="row">
            <!--            <messenger-avatar :messenger="data" />-->
            <div class="chat-messenger-info">
                <div class="chat-messenger-name">
                    {{ conversation.name }}
                </div>
                <div class="">{{ conversation.lastMessage.body }}</div>
            </div>
        </div>
        <div class="row">
            <span class="messenger-unread">2</span>
            <span class="text-xs">{{
                conversation.lastMessage.dateFormatted()
            }}</span>
        </div>
    </div>
</template>
<style scoped>
.messenger-container {
    @apply flex flex-row px-4 py-2 items-center justify-between border-b border-base-content/20 cursor-pointer text-sm;
    .row {
        @apply flex flex-row space-x-4 items-center;
    }
}
.messenger-unread {
    @apply flex text-[10px] bg-secondary w-4 h-4 rounded-full items-center justify-center;
}
</style>
<script setup>
import MessengerAvatar from "./messenger-avatar.vue";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
const props = defineProps({
    conversation: ConversationMsg,
});
</script>
