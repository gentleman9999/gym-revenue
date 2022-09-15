<template>
    <div class="chat-user">
        <img class="chat-avatar" :src="avatar" />
        <span> {{ user.first_name }} {{ user.last_name }} </span>
    </div>
</template>
<style scoped>
.chat-user {
    @apply flex flex-row items-center space-x-2 cursor-pointer;
}
.chat-avatar {
    @apply h-10 w-10 rounded-full;
}
</style>
<script setup>
import { computed } from "vue";
import { getAvatarImg } from "@/utils";

const props = defineProps({
    user: Object,
});

const avatar = computed(() =>
    getAvatarImg(props.user.first_name, props.user.last_name)
);
</script>
