<template>
    <ModalableWrapper>
        <LayoutHeader title="Create Location">
            <h2 class="font-semibold text-xl leading-tight">
                Create a New Location
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <location-form :client-id="$page.props.user.client_id" />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        LocationForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
