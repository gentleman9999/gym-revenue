<template>
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

    <user-form
        :is-client-user="$page.props.user.current_team?.isClientTeam"
        :roles="roles"
        :available-departments="availableDepartments"
        :available-positions="availablePositions"
        :locations="locations"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import UserForm from "@/Pages/Users/Partials/UserForm.vue";

const props = defineProps({
    locations: {
        type: Array,
        required: true,
    },
    roles: {
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
});
</script>
