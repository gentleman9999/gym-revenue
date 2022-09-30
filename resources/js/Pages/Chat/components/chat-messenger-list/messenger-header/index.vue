<template>
    <div class="chat-messenger-header">
        <messenger-setting />
        <div class="text-base-content">Messenger</div>
        <messenger-edit />
    </div>
</template>
<style scoped>
.chat-messenger-header {
    @apply flex flex-row w-full h-14 px-4 items-center justify-between text-secondary border-b border-neutral-content/20;
}
</style>
<script setup>
import MessengerSetting from "./messenger-setting.vue";
import MessengerEdit from "./messenger-edit.vue";
</script>
