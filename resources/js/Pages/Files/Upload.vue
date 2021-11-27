<template>
    <app-layout title="Upload Files">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Upload Files
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
              <file-upload-form/>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'

import LocationForm from '@/Pages/Locations/Partials/LocationForm.vue'
import FileUploadForm from "./Partials/FileUploadForm";


export default defineComponent({
    components: {
        FileUploadForm,
        AppLayout,
        JetFormSection,
        JetInputError,
        JetLabel,
        LocationForm
    },

})
</script>
