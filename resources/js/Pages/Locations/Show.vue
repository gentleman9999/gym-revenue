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
        >
        <div class="block py-2 text-xs text-gray-400">Trashed:</div>
        <select
            v-model="form.trashed"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option value="with">With Trashed</option>
            <option value="only">Only Trashed</option>
        </select>
<!--Filters to Add -->
        <div  class="block py-2 text-xs text-gray-400">State:</div>
        <select
            v-model="form.state"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
     <!--       <option v-for="(state, i) in this.$page.props.locations" :value="grlocations.gymrevenue_id">{{grlocations.name }}
            </option>
       --> </select>
        -

        </gym-revenue-crud>
<!--        {{this.$page.props.locations}}         -->
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
