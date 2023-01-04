<template>
    <div class="w-[340px]">
        <div class="group-chat-form-title font-bold">Select Users To Chat</div>
        <div class="group-chat-form-item-container h-64 overflow-y-auto my-4">
            <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="group-chat-form-item mb-1 rounded cursor-pointer p-2"
                :class="{ active: selectedUsers[user.id] !== undefined }"
                @click="toggleUser(user)"
            >
                {{ user.name }}
            </div>
        </div>
        <div class="flex justify-end">
            <Button secondary size="sm" @click="startChat">{{
                btnLabel
            }}</Button>
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
import { computed, ref } from "vue";
import Button from "@/Components/Button.vue";

const emit = defineEmits(["start-chat"]);
const props = defineProps({
    btnLabel: {
        default: "Start",
        type: String,
    },
    ignore: {
        default: [],
        type: Array,
    },
});
/** @type {Ref<int[]>} */
const selectedUsers = ref({});
/** @type {Ref<Object>} */
const users = ref(null);
const ignoreable = computed(() => props.ignore.map((u) => u.id));
const filteredUsers = computed(() =>
    users.value?.filter((u) => !ignoreable.value.includes(u.id))
);

axios.get(route("get-internal-chats-interlocutors")).then(({ data }) => {
    users.value = Object.values(data);
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
