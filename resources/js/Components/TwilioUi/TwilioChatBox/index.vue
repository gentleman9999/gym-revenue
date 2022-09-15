<template>
    <div
        class="twilio-chat-box"
        :class="{
            'h-0 opacity-0': !showChatBox,
            'h-96 opacity-100': showChatBox,
        }"
    >
        <twilio-header
            @toggle-mode="toggleMode"
            :mode="mode"
            :header="boxTitle"
        />

        <div
            class="twilio-chat-items"
            :class="{
                'h-0 opacity-0': !showChatBox,
                'h-80 opacity-100': showChatBox,
            }"
        >
            <Button class="close-btn" @click="$emit('toggle')">X</Button>
            <div
                class="flex flex-col flex-wrap space-y-3"
                v-if="mode === 'user'"
            >
                <twilio-user-item
                    v-for="user in mockUsers"
                    :key="user.id"
                    :user="user"
                    @click="setSelectedUser(user)"
                />
            </div>
            <div
                class="flex flex-col flex-wrap space-y-3"
                v-if="mode !== 'user'"
            >
                <twilio-chat-item
                    v-for="chat in mockChat"
                    :key="chat.id"
                    :chat="chat"
                    :user="user"
                />
            </div>
        </div>
        <twilio-chat-input @send-msg="sendMsg" v-if="mode !== 'user'" />
    </div>
</template>
<style scoped>
.twilio-chat-box {
    @apply bg-base-100 w-64 rounded border border-secondary flex flex-col justify-between;
    transition: height 650ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
.twilio-chat-items {
    @apply overflow-y-auto p-2;
}
.close-btn {
    @apply h-6 w-6 absolute bg-transparent border-none top-0 right-0;
}
.chat-item {
    @apply flex flex-col;
}
.chat-name {
    @apply text-sm;
}
.chat-msg {
    @apply bg-secondary rounded-md py-1 px-2;
}
</style>
<script setup>
import { ref, computed } from "vue";
import Button from "@/Components/Button.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import TwilioChatItem from "./TwilioChatItem.vue";
import TwilioUserItem from "./TwilioUserItem.vue";
import TwilioChatInput from "./TwilioChatInput.vue";
import TwilioHeader from "./TwilioHeader.vue";
const props = defineProps({
    showChatBox: Boolean,
});

const mockChat = [
    {
        id: 1,
        user_id: 1,
        user_name: "Fred",
        msg: "Fred's message",
    },
    {
        id: 2,
        user_id: 2,
        user_name: "John",
        msg: "John's message",
    },
    {
        id: 3,
        user_id: 24,
        user_name: "James",
        msg: "James's message",
    },
];

const mockUsers = [
    {
        id: 1,
        first_name: "Chrys",
        last_name: "Ugwu",
    },
    {
        id: 2,
        first_name: "Tommy",
        last_name: "Lee",
    },
];
const sendMsg = (msg) => {
    console.log(msg);
};

const mode = ref("user");
const toggleMode = () => {
    mode.value = mode.value === "user" ? "chat" : "user";
};

const selectedUser = ref(null);

const setSelectedUser = (val) => {
    selectedUser.value = val;
    mode.value = "chat";
};

const boxTitle = computed(() =>
    mode.value === "user"
        ? "Users"
        : selectedUser.value.first_name + " " + selectedUser.value.last_name
);
</script>
