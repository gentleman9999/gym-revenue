<template>
    <sales-card-wrapper>
        <sales-card
            class="w-full"
            :options="{
                search: true,
            }"
        >
            <template #cardTitle>
                <div
                    class="text-base-content text-lg text-bold w-1/2 border-b border-neutral-500"
                >
                    Clubs
                </div>
            </template>
            <div class="flex flex-row space-x-3">
                <available-location
                    :data="available"
                    :toggleSelect="toggleAvailable"
                />
                <selected-location
                    :data="selected"
                    :toggleSelect="toggleAvailable"
                />
            </div>
        </sales-card>
    </sales-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import SalesCardWrapper from "../SalesCardWrapper.vue";
import SalesCard from "@/Components/Card.vue";
import AvailableLocation from "./AvailableLocation.vue";
import SelectedLocation from "./SelectedLocation.vue";

const available = ref([
    "Location 1",
    "Location 2",
    "Location 3",
    "Location 4",
    "Location 5",
    "Location 6",
    "Location 7",
]);
const selected = ref([]);
const toggleAvailable = (value) => {
    let available_ndx = available.value.indexOf(value);
    let selected_ndx = selected.value.indexOf(value);

    if (available_ndx !== -1) {
        available.value.splice(available_ndx, 1);
        selected.value.push(value);
    } else {
        selected.value.splice(selected_ndx, 1);
        available.value.push(value);
    }
};
</script>
