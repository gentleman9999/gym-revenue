<template>
    <LayoutHeader title="Create Note">
        <h2 class="font-semibold text-xl leading-tight">Create Note</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <note-form :client-id="$page.props.user.client_id" />
        </div>
    </div>
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import NoteForm from "@/Pages/Notes/Partials/NoteForm.vue";

const props = defineProps({
    userId: {
        type: String,
        default: "",
    },
    title: {
        type: String,
        default: "",
    },
    note: {
        type: String,
        default: "",
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>
