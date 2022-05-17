<template>
    <dashboard-widget
        title="Goals"
        :modal-component="GoalsModal"
        :amount="18000"
    >
        <div class="flex flex-col gap-2">
            <div v-for="goal in goalsData">
                <h2 class="text text-secondary">{{ goal }}</h2>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Proin quis scelerisque lectus.
                </p>
            </div>
        </div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import GoalsModal from "@/Pages/Reports/Goals/GoalsModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        const goalsData = ["Goal One", "Goal Two", "Goal Three"];
        return { GoalsModal, goalsData };
    },
});
</script>
