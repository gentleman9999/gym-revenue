<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Location">
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">
                Edit {{ $page.props.location.name }} ({{
                    $page.props.location["gymrevenue_id"]
                }})
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <location-form
                :client-id="this.$page.props.user.client_id"
                :location="location"
                :location-types="locationTypes"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";

import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";
import { defineComponent } from "vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
        LocationForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: ["location", "locationTypes"],
});
</script>
