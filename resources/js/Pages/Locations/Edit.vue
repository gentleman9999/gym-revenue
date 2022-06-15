<template>
    <ModalableWrapper>
        <app-layout title="Edit Location">
            <template #header>
                <jet-bar-icon type="g0back" fill />
                <h2 class="font-semibold text-xl leading-tight">
                    Edit {{ $page.props.location.name }} ({{
                        $page.props.location["gymrevenue_id"]
                    }})
                </h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <location-form
                :client-id="this.$page.props.user.current_client_id"
                :location="$page.props.location"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
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
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        JetBarIcon,
        LocationForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: ["locations"],
});
</script>
