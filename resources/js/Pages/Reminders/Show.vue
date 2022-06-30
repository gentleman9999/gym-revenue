<template>
    <LayoutHeader title="Reminders" />
    <page-toolbar-nav title="Reminders" :links="navLinks" />
    <gym-revenue-crud
        base-route="reminders"
        model-name="Role"
        model-key="role"
        :fields="fields"
        :resource="reminders"
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
        title="Really Trash Security Role?"
        v-if="confirmDelete"
        @confirm="handleConfirmDelete"
        @cancel="confirmDelete = null"
    >
        Are you sure you want to delete Security Role '{{
            confirmDelete.title
        }}'
    </confirm>
</template>
<script>
import { defineComponent, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: ["reminders", "filters"],
    setup(props) {
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            console.log("click delete", item);
            confirmDelete.value = item;
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("reminders.delete", confirmDelete.value));
            confirmDelete.value = null;
        };

        const fields = [
            "name",
            "description",
            "remind_time",
            "created_at",
            "updated_at",
        ];

        let navLinks = [
            {
                label: "Calendar",
                href: route("calendar"),
                onClick: null,
                active: false,
            },
            {
                label: "Event Types",
                href: route("calendar.event_types"),
                onClick: null,
                active: false,
            },
            {
                label: "Tasks",
                href: route("tasks"),
                onClick: null,
                active: false,
            },
            {
                label: "Reminders",
                href: route("reminders"),
                onClick: null,
                active: true,
            },
        ];

        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
            Inertia,
            navLinks,
        };
    },
});
</script>
