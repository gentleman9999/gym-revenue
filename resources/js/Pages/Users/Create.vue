<template>
    <ModalableWrapper>
        <LayoutHeader title="Create User">
            <div class="flex flex-row ml-4">
                <Button
                    type="button"
                    @click="$inertia.visit(route('users'))"
                    info
                    outline
                >
                    Back
                </Button>

                <h2 class="font-semibold text-xl leading-tight ml-4">
                    Create A New {{ $page.props.clientName }} User
                </h2>
            </div>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <user-form
                :client-id="$page.props.user.current_client_id"
                :roles="roles"
                :classifications="classifications"
                :locations="locations"
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
