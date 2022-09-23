<template>
    <div class="fa-sm text-center py-2">
        <div class="mb-1">{{ message }}</div>
        <div>
            <a href="/chat" class="btn w-max rounded btn-xs mr-1">View</a>
            <Button
                size="xs"
                class="ml-1 close-btn"
                ghost
                @click="$emit('close-toast')"
                >Close</Button
            >
        </div>
    </div>
</template>

<script setup>
import Button from "@/Components/Button.vue";
import { onBeforeMount, onMounted } from "vue";
defineEmits(["close-toast"]);
defineProps({
    message: String,
});

onBeforeMount(() => {
    // So here is the logic: For conversation notification, we need one at a time, I couldn't find a way to set Toasts
    // "maxToasts" per instance level, so the idea is everytime we have a new notification, we check and close
    // existing toasts.
    document
        .querySelectorAll(
            ".Vue-Toastification__container.bottom-left .conversation-container .close-btn"
        )
        .forEach((btn) => {
            // I'm using "click" to make sure all events attached to toasts close are triggered.
            btn.click();
        });
});
</script>
<style>
.Vue-Toastification__container.bottom-left .conversation-container {
    padding: 0;
    min-height: unset;
    max-width: 326px;
}
</style>
