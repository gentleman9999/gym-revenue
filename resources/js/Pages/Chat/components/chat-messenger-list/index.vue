<template>
    <div class="chat-messenger-list-container">
        <messenger-header />
        <messenger-search />
        <div>
            <chat-messenger v-for="user in users" :key="user.id" :data="user" />
        </div>
    </div>
</template>
<style scoped>
.chat-messenger-list-container {
    @apply flex flex-col w-1/4 h-full overflow-y-auto border-r border-neutral-content/70;
}
</style>
<script setup>
import MessengerHeader from "./messenger-header/index.vue";
import MessengerSearch from "./messenger-search.vue";
import ChatMessenger from "./chat-messenger/index.vue";

const users = [
    {
        id: 1,
        first_name: "Tommy",
        last_name: "Lee",
    },
];
</script>
