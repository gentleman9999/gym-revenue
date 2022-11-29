<template>
    <div class="bg-base-content">
        <div class="member-note-wrapper">
            <div class="member-note-title">Phone call notes</div>
            <input v-model="form.title" class="member-note-title-input" />
            <textarea
                v-model="form.content"
                class="member-note-content"
                rows="6"
            ></textarea>
            <div class="flex justify-end">
                <Button
                    ghost
                    size="xs"
                    @click="$emit('toggle-mode')"
                    class="text-neutral"
                    >Edit</Button
                >
                <Button secondary size="xs" @click="$emit('toggle-mode')"
                    >Save</Button
                >
            </div>
        </div>
    </div>
</template>
<style scoped>
.member-note-wrapper {
    @apply flex flex-col bg-secondary/20 py-9 px-20;
}
.member-note-title {
    @apply text-neutral text-lg font-bold mb-5;
}
.member-note-title-input {
    @apply w-80 mb-3 h-9 rounded border border-secondary text-neutral-focus;
}
.member-note-content {
    @apply mb-6 rounded border border-secondary bg-base-content text-neutral-focus;
}
</style>
<script setup>
import { useGymRevForm } from "@/utils";
import Button from "@/Components/Button.vue";

const form = useGymRevForm({
    title: "Notes Title",
    content:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
});
</script>
