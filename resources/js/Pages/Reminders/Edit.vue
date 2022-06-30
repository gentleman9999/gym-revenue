<template>
    <ModalableWrapper>
        <LayoutHeader title="Rename File">
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">Rename File</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <file-form
                :client-id="this.$page.props.user.current_client_id"
                :file="file"
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

import FileForm from "@/Pages/Files/Partials/FileForm";
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
        ReminderForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: ["file"],
});
</script>
