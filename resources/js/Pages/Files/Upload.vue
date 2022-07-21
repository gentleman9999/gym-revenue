<template>
    <ModalableWrapper>
        <LayoutHeader title="Upload Files">
            <h2 class="font-semibold text-xl leading-tight">Upload Files</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <file-manager :client-id="$page.props.user.client_id" />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";
import FileManager from "./Partials/FileManager.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        FileManager,
        LayoutHeader,
        JetFormSection,
        JetInputError,
        JetLabel,
        LocationForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
