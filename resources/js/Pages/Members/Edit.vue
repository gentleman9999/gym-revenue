<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Member">
            <jet-bar-icon type="goback" fill />
            <h2 class="font-semibold text-xl leading-tight">Edit Member</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <member-form
                :user-id="user_id"
                :client-id="this.$page.props.user.client_id"
                :member="member"
                :locations="locations"
                :available_services="available_services"
                :interaction-count="interactionCount"
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

import MemberForm from "@/Pages/Members/Partials/MemberForm.vue";
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
        MemberForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: [
        "user_id",
        "member",
        "locations",
        "available_services",
        "interactionCount",
    ],
});
</script>
