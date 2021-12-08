<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Locations</h2>
        </template>
        <gym-revenue-crud
            base-route="locations"
            model-name="Location"
            :fields="fields"
            :resource="locations"
        />
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
    },
    props: ["sessions", "locations", "title", "isClientUser", "filters"],
    computed: {
        fields() {
            if (this.isClientUser) {
                return ["name", "city", "state", "active"];
            }

            return [
                { name: "client.name", label:"client"},
                "name",
                "city",
                "state",
                "active",
            ];
        },
    },
});
</script>
