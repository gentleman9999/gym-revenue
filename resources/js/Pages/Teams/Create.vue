<template>
    <LayoutHeader>
        <h2 class="font-semibold text-xl leading-tight">Create Team</h2>
    </LayoutHeader>

    <team-form />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import TeamForm from "@/Pages/Teams/Partials/TeamForm.vue";

const props = defineProps({
    availableLocations: {
        type: Array,
        required: true,
    },
});
</script>
