<template>
    <ApolloQuery
        :query="(gql) => queries['tasks'][taskType]"
        :variables="param"
    >
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="tasks"
                model-name="Task"
                model-key="task"
                class="border-transparent"
                :resource="getTasks(data)"
                :fields="fields"
                @update-page="updatePage"
                :actions="{
                    trash: false,
                    restore: false,
                }"
            >
                <template #top-actions><div></div></template>
                <template #filter><div></div></template>
                <template #title>
                    <div
                        class="task-list-header"
                        :class="{
                            'bg-secondary':
                                props.taskType === 'incomplete_tasks',
                            'bg-error': props.taskType === 'overdue_tasks',
                            'bg-neutral': props.taskType === 'completed_tasks',
                        }"
                    >
                        {{ headers[props.taskType] }}
                    </div>
                </template>
            </gym-revenue-crud>
        </template>
    </ApolloQuery>
</template>
<style scoped>
.task-list-header {
    margin: -10px -34px 22px -34px;
    @apply text-xl px-6 py-2.5 rounded-t-lg;
}
</style>
<script setup>
import { computed } from "vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import queries from "@/gql/queries";

const props = defineProps({
    taskType: {
        type: String,
        required: true,
    },
    fields: {
        type: Array,
        required: true,
    },
    updatePage: {
        type: Function,
    },
});

const emit = defineEmits(["edit"]);

const headers = {
    incomplete_tasks: "Today",
    overdue_tasks: "Overdue",
    completed_tasks: "Completed",
};

const getTasks = (data) => {
    return _.cloneDeep(data[props.taskType]);
};

const editTask = (item) => {
    emit("edit", item);
};
</script>
