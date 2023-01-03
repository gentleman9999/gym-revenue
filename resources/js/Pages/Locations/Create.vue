<template>
    <LayoutHeader title="Create Location">
        <h2 class="font-semibold text-xl leading-tight">
            Create a New Location
        </h2>
    </LayoutHeader>

    <location-form
        :client-id="$page.props.user.client_id"
        :location-types="locationTypes"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";

const props = defineProps({
    locationTypes: {
        type: Array,
    },
});
</script>
