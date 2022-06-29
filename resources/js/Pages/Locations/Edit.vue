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
                :client-id="this.$page.props.user.current_client_id"
                :location="location"
                :poc_first="poc_first"
                :poc_last="poc_last"
                :poc_phone="poc_phone"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";

import LocationForm from "@/Pages/Locations/Partials/LocationForm";
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
    props: ["location", "poc_first", "poc_last", "poc_phone"],
});
</script>
