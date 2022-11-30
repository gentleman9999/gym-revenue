<template>
    <div class="w-[340px]">
        <div class="group-chat-form-title font-bold">Select Users To Chat</div>
        <div class="group-chat-form-item-container h-64 overflow-y-auto my-4">
            <div
                v-for="user in users"
                :key="user.id"
                class="group-chat-form-item mb-1 rounded cursor-pointer p-2"
                :class="{ active: selectedUsers[user.id] !== undefined }"
                @click="toggleUser(user)"
            >
                {{ user.name }}
            </div>
        </div>
        <div class="flex justify-end">
            <Button secondary size="sm" @click="startChat">Start</Button>
        </div>
    </div>
</template>
<style scoped>
.group-chat-form-item:nth-child(odd) {
    background-color: #242424;
}
.group-chat-form-item.active {
    @apply bg-info border border-secondary;
}
</style>
<script setup>
import { ref } from "vue";
import Button from "@/Components/Button.vue";

const emit = defineEmits(["start-chat"]);
/** @type {Ref<int[]>} */
const selectedUsers = ref({});
/** @type {Ref<Object>} */
const users = ref(null);

axios.get(route("get-internal-chats-interlocutors")).then(({ data }) => {
    users.value = data;
});

function toggleUser(user) {
    if (selectedUsers.value[user.id] !== undefined) {
        delete selectedUsers.value[user.id];
    } else {
        selectedUsers.value[user.id] = user;
    }
}

function startChat() {
    // clear interlocutors selection
    emit("start-chat", selectedUsers.value);
    selectedUsers.value = {};
}
</script>
