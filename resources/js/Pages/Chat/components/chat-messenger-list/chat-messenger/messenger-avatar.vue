<template>
    <div class="chat-avatar-container">
        <img class="chat-avatar" :src="avatar" />
        <div class="messenger-status"></div>
    </div>
</template>
<style scoped>
.chat-avatar-container {
    @apply relative;
}
.chat-avatar {
    @apply h-14 w-14 rounded-full;
}
.messenger-status {
    @apply h-2 w-2 bg-secondary rounded absolute right-0 bottom-2;
}
</style>
<script setup>
import { computed } from "vue";
import { getAvatarImg } from "@/utils";

const props = defineProps({
    messenger: Object,
});

const avatar = computed(() =>
    getAvatarImg(props.messenger.first_name, props.messenger.last_name)
);
</script>
