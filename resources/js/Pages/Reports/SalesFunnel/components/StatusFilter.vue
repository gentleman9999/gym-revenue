<template>
    <div class="flex flex-row space-x-4 pr-2">
        <span>From</span>
        <select-box
            :items="filter"
            size="xs"
            label="Date Range"
            color="secondary"
        ></select-box>
    </div>
</template>
<script setup>
import SelectBox from "@/Components/SelectBox";
const filter = ["Search Date", "February 21-27, 2022", "April 1-24, 2022"];
</script>
