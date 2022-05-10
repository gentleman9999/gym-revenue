<template>
    <app-layout :title="title">

        <page-toolbar-nav
            title="Tasks"
            :links="navLinks"
        />
        <gym-revenue-crud
            base-route="tasks"
            model-name="Task"
            model-key="task"
            :fields="fields"
            :resource="tasks"
            :actions="{
                trash: false,
                restore: false,
                delete: {
                    label: 'Delete',
                    handler: ({ data }) => handleClickDelete(data),
                },
            }"
        />
        <confirm
            title="Really Trash Task?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete task'{{
                confirmDelete.title
            }}'
        </confirm>
    </app-layout>
</template>
<script>
import { defineComponent, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav
    },
    props: ["tasks", "filters"],
    setup(props) {

        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            console.log('click delete', item);
            confirmDelete.value = item;
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("tasks.delete", confirmDelete.value));
            confirmDelete.value = null;
        };

        const fields = [
            "title",
            "created_at",
            "updated_at",
            {
                name: 'event_completion',
                label: "Completed At"
            }
        ];

        let navLinks = [
            {
                label: "Calendar",
                href: route("calendar"),
                onClick: null,
                active: false
            },
            {
                label: "Event Types",
                href: route("calendar.event_types"),
                onClick: null,
                active: false
            },
            {
                label: "Tasks",
                href: route("tasks"),
                onClick: null,
                active: true
            },
        ];

        return {fields, confirmDelete, handleConfirmDelete, handleClickDelete, Inertia, navLinks};
    },
});
</script>
