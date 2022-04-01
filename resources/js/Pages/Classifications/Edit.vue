<template>
    <app-layout title="Edit Classification">
        <template #header>
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">
                Edit Classification
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <classification-form
                    :client-id="$page.props.user.current_client_id"
                    :classification="$page.props.classification"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";
import ClassificationForm from "@/Pages/Classifications/Partials/ClassificationForm";

export default defineComponent({
    components: {
        ClassificationForm,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
    },
    props: {
        classification: {
            type: Object,
            required: true,
        }
    },
});
</script>
