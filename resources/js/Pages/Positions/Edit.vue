<template>
    <LayoutHeader title="Edit Department">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Department</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <position-form
                :client-id="$page.props.user.client_id"
                :position="position"
            />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import PositionForm from "@/Pages/Positions/Partials/PositionForm.vue";

export default defineComponent({
    components: {
        PositionForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
    },
    props: {
        position: {
            type: Object,
            required: true,
        },
    },
});
</script>
