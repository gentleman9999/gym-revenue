<template>
    <div class="mass-note-container">
        <mass-note-title />
        <div class="flex flex-row mt-9 px-6">
            <div class="mass-note-sticky-container">
                <mass-note-sticky
                    v-for="item in stickys"
                    :key="item.id"
                    :title="item.title"
                    :class="item.class"
                />
            </div>
            <textarea class="mass-note-add-text" placeholder="notes"></textarea>
        </div>
        <div class="mass-note-actions">
            <Button ghost size="sm">Save</Button>
            <Button ghost size="sm">Share</Button>
            <Button ghost size="sm">Delete</Button>
        </div>
    </div>
</template>
<style scoped>
.mass-note-container {
    @apply bg-neutral border border-secondary rounded;
}
.mass-note-add-text {
    @apply h-64 rounded bg-base-content text-neutral w-full;
}
.mass-note-actions {
    @apply flex flex-row my-3 px-6 justify-end;
}
.mass-note-sticky-container {
    @apply flex flex-col justify-center space-y-3;
}
</style>
<script setup>
import MassNoteTitle from "./mass-note-title.vue";
import MassNoteSticky from "./mass-note-sticky.vue";
import Button from "@/Components/Button.vue";

const stickys = [
    {
        id: 15,
        title: "Note 15#",
        class: "bg-secondary",
    },
    {
        id: 16,
        title: "Note 16#",
        class: "bg-accent",
    },
    {
        id: 17,
        title: "Note 17#",
        class: "bg-warning",
    },
    {
        id: 18,
        title: "Note 18#",
        class: "bg-primary",
    },
    {
        id: 19,
        title: "Note 19#",
        class: "bg-error",
    },
];
</script>
