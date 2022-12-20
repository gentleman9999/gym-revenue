<template>
    <gym-revenue-crud
        ref="tasksCrud"
        base-route="tasks"
        :model-name="`Task-${taskType}`"
        model-key="task"
        class="border-transparent"
        :fields="fields"
        :param="param"
        :edit-component="TaskForm"
        :handleCrudUpdate="handleCrudUpdate"
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
                    'bg-secondary': props.taskType === 'incomplete_tasks',
                    'bg-error': props.taskType === 'overdue_tasks',
                    'bg-neutral': props.taskType === 'completed_tasks',
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
import { computed, ref, watch } from "vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import queries from "@/gql/queries";
import { useQuery } from "@vue/apollo-composable";
import TaskForm from "./TaskForm.vue";

const props = defineProps({
    taskType: {
        type: String,
        required: true,
    },
    start: {
        type: String,
    },
});
const fields = [
    {
        name: "title",
        label: "Title",
    },
    {
        name: "start",
        label: "Due At",
    },
    {
        name: "created_at",
        label: "Created At",
    },
    {
        name: "event_completion",
        label: "Completed At",
    },
];

const tasksCrud = ref(null);
const param = ref({
    param: {
        type: props.taskType,
        start: props.start,
    },
    pagination: {
        page: 1,
    },
});
watch(
    () => props.start,
    (newValue) => {
        param.value = {
            ...param.value,
            param: {
                ...param.value.param,
                start: newValue,
            },
        };
        tasksCrud.value.refetch(param.value);
    }
);

watch(props.start, () => {
    param.value = {
        ...param.value,
        param: {
            ...param.value.param,
            start: props.start,
        },
    };
    tasksCrud.value.refetch(param.value);
});

const handleCrudUpdate = (key, value) => {
    if (key === "page") {
        param.value = {
            ...param.value,
            pagination: {
                page: value,
            },
        };
    }
    tasksCrud.value.refetch(param.value);
};
const emit = defineEmits(["edit"]);

const headers = {
    incomplete_tasks: "Today",
    overdue_tasks: "Overdue",
    completed_tasks: "Completed",
};

const getTasks = (data) => {
    return _.cloneDeep(data.tasks);
};

const editTask = (item) => {
    emit("edit", item);
};
</script>
