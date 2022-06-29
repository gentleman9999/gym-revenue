<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit User">
            <h2 class="font-semibold text-xl leading-tight">
                Edit User {{ $page.props.selectedUser.name }}
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <user-form
                :client-id="$page.props.user.current_client_id"
                :user="selectedUser"
                :roles="roles"
                :classifications="classifications"
                :locations="locations"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import UserForm from "@/Pages/Users/Partials/UserForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        UserForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: {
        selectedUser: {
            type: Object,
            required: true,
        },
        locations: {
            type: Array,
            required: true,
        },
        roles: {
            type: Array,
            required: true,
        },
        classifications: {
            type: Array,
            required: true,
        },
    },
});
</script>
