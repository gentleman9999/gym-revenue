<template>
    <div class="flex flex-col space-y-4">
        <div>Expires By</div>
        <date-picker
            required
            id="start"
            v-model="value"
            :enable-time-picker="false"
            format="MM-dd-yyyy"
            :month-change-on-scroll="false"
            :auto-apply="true"
            :close-on-scroll="true"
            class="bg-neutral text-base-content"
            dark
        />
    </div>
</template>
<script setup>
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const props = defineProps({
    value: String,
});
</script>
