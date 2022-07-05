<template>
    <gym-revenue-crud
        base-route="tasks"
        model-name="Task"
        model-key="task"
        class="border-transparent"
        :fields="fields"
        :resource="tasks"
        :actions="{
            edit: {
                label: 'Edit',
                handler: ({ data }) => editTask(data),
            },
            trash: false,
            restore: false,
            delete: {
                label: 'Delete',
                handler: ({ data }) => handleClickDelete(data),
            },
        }"
    >
        <template #top-actions><div></div></template>
        <template #filter><div></div></template>
        <template #title>
            <div
                class="task-list-header"
                :class="{
                    'bg-secondary': taskType === 'incomplete_tasks',
                    'bg-error': taskType === 'ovedue_tasks',
                    'bg-neutral-500': taskType === 'completed_tasks',
                }"
            >
                {{ headers[taskType] }}
            </div>
        </template>
    </gym-revenue-crud>
</template>
<style scoped>
.task-list-header {
    margin: -10px -34px 22px -34px;
    @apply text-xl px-6 py-2.5 rounded-t-lg;
}
</style>
<script setup>
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";

const props = defineProps({
    taskType: {
        type: String,
    },
    tasks: {
        type: Array,
        default: [],
    },
});

const headers = {
    incomplete_tasks: "Today",
    ovedue_tasks: "Overdue",
    completed_tasks: "Completed",
};
</script>
