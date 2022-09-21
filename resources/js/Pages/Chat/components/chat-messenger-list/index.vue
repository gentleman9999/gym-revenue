<template>
    <div class="chat-messenger-list-container">
        <!--        <messenger-header />-->
        <!--        <messenger-search />-->
        <div>
            <div v-show="conversations.length === 0" class="text-center mt-10">
                No active chats
            </div>
            <chat-messenger
                v-for="(conversation, sid) in conversations"
                :key="sid"
                :conversation="conversation"
                @click="$emit('view-conversation', sid)"
            />
        </div>
    </div>
</template>
<style scoped>
.chat-messenger-list-container {
    @apply flex flex-col w-[380px] h-full overflow-x-hidden overflow-y-auto border-r border-neutral-content/70;
}
</style>
<script setup>
import MessengerHeader from "./messenger-header/index.vue";
import MessengerSearch from "./messenger-search.vue";
import ChatMessenger from "./chat-messenger/index.vue";
defineEmits(["view-conversation"]);
defineProps({
    conversations: {
        type: Object,
        required: true,
    },
});
</script>
