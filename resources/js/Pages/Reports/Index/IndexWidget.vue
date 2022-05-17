<template>
    <dashboard-widget title="Index" :modal-component="IndexModal">
        <div class="divider opacity-30 my-0" />
        <div class="flex flex-col gap-1">
            <template v-for="x in Array(8)">
                <div class="flex flex-row items-center gap-8">
                    <div class="text-secondary text-2xl">$18,000</div>
                    <div>Name</div>
                </div>
                <div class="divider opacity-30 my-0" />
            </template>
        </div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import IndexModal from "@/Pages/Reports/Index/IndexModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { IndexModal };
    },
});
</script>
