<template>
    <app-layout title="Upload Files">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Upload Files</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <file-manager :client-id="$page.props.user.current_client_id" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";
import FileManager from "./Partials/FileManager";

export default defineComponent({
    components: {
        FileManager,
        AppLayout,
        JetFormSection,
        JetInputError,
        JetLabel,
        LocationForm,
    },
});
</script>
