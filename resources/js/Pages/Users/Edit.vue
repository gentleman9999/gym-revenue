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
                :is-client-user="$page.props.user.current_team?.isClientTeam"
                :user="selectedUser"
                :roles="roles"
                :locations="locations"
                :available-departments="availableDepartments"
                :available-positions="availablePositions"
            />
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

import UserForm from "@/Pages/Users/Partials/UserForm.vue";

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
        availablePositions: {
            type: Array,
            required: true,
        },
        availableDepartments: {
            type: Array,
            required: true,
        },
        roles: {
            type: Array,
            required: true,
        },
    },
});
</script>
