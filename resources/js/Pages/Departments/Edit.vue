<template>
    <LayoutHeader title="Edit Department">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Department</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <department-form
                :client-id="$page.props.user.client_id"
                :department="depart"
                :positions="positions"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from "@vue/reactivity";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import DepartmentForm from "@/Pages/Departments/Partials/DepartmentForm.vue";

const props = defineProps({
    department: {
        type: Object,
        required: true,
    },
    positions: {
        type: Object,
        required: true,
    },
});

const depart = computed({
    get() {
        return {
            ...props.department,
            positions: props.department.positions.map((item) => item.id),
        };
    },
});
</script>
