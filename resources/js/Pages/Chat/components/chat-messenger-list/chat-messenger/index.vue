<template>
    <div class="messenger-container" @click="$emit('set-messenger', data)">
        <div class="row">
            <messenger-avatar :messenger="data" />
            <messenger-info :messenger="data" />
        </div>
        <div class="row">
            <span class="messenger-unread">2</span>
            <span>Fri</span>
        </div>
    </div>
</template>
<style scoped>
.messenger-container {
    @apply flex flex-row px-4 py-2 items-center justify-between border-b border-base-content/20 cursor-pointer;
    .row {
        @apply flex flex-row space-x-4 items-center;
    }
}
.messenger-unread {
    @apply flex text-sm bg-secondary w-6 h-6 rounded-full items-center justify-center;
}
</style>
<script setup>
import MessengerAvatar from "./messenger-avatar.vue";
import MessengerInfo from "./messenger-info.vue";
const props = defineProps({
    data: Object,
});
</script>
