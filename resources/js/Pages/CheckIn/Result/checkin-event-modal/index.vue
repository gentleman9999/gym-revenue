<template>
    <card-with-title accent :title="data.title" class="bg-base-200">
        <div class="p-5 flex flex-col">
            <div class="flex flex-row space-x-5 justify-between">
                <div class="flex flex-row">
                    <calendar-icon class="mr-2" />{{ data.date }}
                </div>
                <div>
                    <font-awesome-icon :icon="['fa', 'clock']" size="lg" />
                    {{ data.time }}
                </div>
            </div>
            <!-- <div class="my-5">Guests Attending</div> -->
            <Button class="self-center" ghost>Check In</Button>
        </div>
    </card-with-title>
</template>
<style scoped></style>
<script setup>
import Button from "@/Components/Button.vue";
import CardWithTitle from "../../components/card-with-title.vue";
import { CalendarIcon } from "@/Components/Icons";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faClock } from "@fortawesome/pro-solid-svg-icons";
library.add(faClock);

const props = defineProps({
    data: {
        type: Object,
        default: {
            title: "Small Group PT Event",
            date: "2022-09-19",
            time: "10:30 am",
        },
    },
});
</script>
