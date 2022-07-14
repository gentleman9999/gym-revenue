<template>
    <gym-revenue-crud
        base-route="tasks"
        model-name="Task"
        model-key="task"
        class="border-transparent"
        :resource="tasks"
        :fields="fields"
        :actions="{
            edit: {
                label: 'Edit',
                handler: ({ data }) => editTask(data),
            },
            trash: false,
            restore: false,
            // delete: {
            //     label: 'Delete',
            //     handler: ({ data }) => handleClickDelete(data),
            // },
        }"
    >
        <template #top-actions><div></div></template>
        <template #filter><div></div></template>
        <template #title>
            <div
                class="task-list-header"
                :class="{
                    'bg-secondary': props.taskType === 'incomplete_tasks',
                    'bg-error': props.taskType === 'overdue_tasks',
                    'bg-neutral-500': props.taskType === 'completed_tasks',
                }"
            >
                {{ headers[props.taskType] }}
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
import { computed } from "vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";

const props = defineProps({
    taskType: {
        type: String,
        required: true,
    },
    tasks: {
        type: Object,
        required: true,
    },
    fields: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["edit"]);

const headers = {
    incomplete_tasks: "Today",
    overdue_tasks: "Overdue",
    completed_tasks: "Completed",
};

const editTask = (item) => {
    emit("edit", item);
};
</script>
