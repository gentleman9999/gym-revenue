<template>
    <LayoutHeader title="Create Note">
        <h2 class="font-semibold text-xl leading-tight">Create Note</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <note-form
                :client-id="$page.props.user.current_client_id"
                :notes="notes"
            />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import NoteForm from "@/Pages/Notes/Partials/NoteForm.vue";

export default defineComponent({
    components: {
        NoteForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        userId: {
            type: String,
            default: "",
        },
    },
});
</script>
