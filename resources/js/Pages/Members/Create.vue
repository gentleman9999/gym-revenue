<template>
    <ModalableWrapper>
        <LayoutHeader title="Create New Member">
            <h2 class="font-semibold text-xl leading-tight">
                Create New Member
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <member-form
                :user-id="user_id"
                :client-id="$page.props.user.client_id"
                :locations="locations"
                :lead_types="lead_types"
                :lead_statuses="lead_statuses"
                :lead_sources="lead_sources"
                :lead_owners="lead_owners"
                :available_services="available_services"
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
import MemberForm from "@/Pages/Members/Partials/MemberForm.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        MemberForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: ["user_id", "locations", "available_services"],
});
</script>
