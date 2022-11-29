<template xmlns="http://www.w3.org/1999/html">
    <div class="chat-messenger-list-container">
        <div class="bg-base-100">
            <Dropdown align="start" width="60">
                <template #trigger>
                    <slot name="trigger">
                        <button
                            type="button"
                            class="px-3 py-2 text-sm leading-4 font-medium bg-base-100 hover:bg-base-200"
                        >
                            <More />
                        </button>
                    </slot>
                </template>
                <template #content>
                    <ul class="menu w-[200px]">
                        <li>
                            <a href="#" @click.prevent="selectGroupChat"
                                >Start Chat</a
                            >
                        </li>
                    </ul>
                </template>
            </Dropdown>
        </div>
        <div v-if="conversations.length === 0" class="text-center mt-10">
            No active chats
        </div>
        <TransitionGroup v-else name="chats" tag="div">
            <ChatMessenger
                v-for="(conversation, index) in conversations"
                :key="index"
                :conversation="conversation"
                @click="$emit('view-conversation', index)"
            />
        </TransitionGroup>

        <DaisyModal ref="groupChatModal">
            <GroupChatForm @start-chat="startChat" />
        </DaisyModal>
    </div>
</template>
<script setup>
import ChatMessenger from "./chat-messenger/index.vue";
import Dropdown from "@/Components/Dropdown.vue";
import More from "@/Components/Icons/More.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import GroupChatForm from "@/Pages/Chat/components/GroupChatForm.vue";
import { ref } from "vue";

const emit = defineEmits(["view-conversation", "start-chat"]);
const props = defineProps({
    /** @type {Array<ConversationMsg>} */
    conversations: {
        type: Array,
        required: true,
    },
});

const groupChatModal = ref(null);

function selectGroupChat() {
    groupChatModal.value.open();
}

function startChat(users) {
    groupChatModal.value.close();
    emit("start-chat", users);
}
</script>
<style scoped>
.chat-messenger-list-container {
    @apply flex flex-col w-[380px] bg-base-300 h-full overflow-x-hidden overflow-y-auto border-r border-neutral-content/30;
}
.chats-enter-active,
.chats-leave-active {
    transition: all 0.2s ease;
}
.chats-enter-from,
.chats-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
