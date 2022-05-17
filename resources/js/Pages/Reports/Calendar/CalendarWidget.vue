<template>
    <dashboard-widget
        title="Calendar Reporting"
        :modal-component="CalendarModal"
    >
        <div class="flex flex-row gap-4">
            <div class="w-12 flex flex-col gap-2 flex-shrink-0">
                <template v-for="date in dates">
                    <div
                        class="flex flex-col border-[1px] border-secondary rounded flex items-center justify-center h-12 text-xl font-bold"
                    >
                        <div>{{ date.getDate() }}</div>
                        <div class="text-xs">
                            {{ days[date.getDay()] }}
                        </div>
                    </div>
                </template>
            </div>
            <div class="flex flex-col gap-2">
                <div
                    class="flex flex-row bg-base-content border-secondary border-[1px] rounded p-2 items-center gap-4"
                >
                    <div class="text-secondary text-3xl font-bold">201</div>
                    <div class="text-base-300 font-bold">New Members</div>
                </div>
                <div class="text text-secondary">Campaign /To-Do</div>
                <p>
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor invidunt ut labore et
                </p>
            </div>
        </div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import CalendarModal from "@/Pages/Reports/Calendar/CalendarModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        let dates = [];
        for (let i = 0; i < 6; i++) {
            let date = new Date();
            date.setDate(date.getDate() - i);
            dates.push(date);
        }

        const days = [
            "Sun",
            "Mon",
            "Tues",
            "Wed",
            "Thurs",
            "Fri",
            "Sat",
        ];

        return { CalendarModal, dates, days };
    },
});
</script>
