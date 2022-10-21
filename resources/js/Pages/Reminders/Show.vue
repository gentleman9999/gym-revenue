<template>
    <LayoutHeader title="Reminders" />
    <page-toolbar-nav title="Reminders" :links="navLinks" />
    <ApolloQuery :query="(gql) => queries['reminders']" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="reminders"
                model-name="Reminder"
                model-key="reminder"
                :fields="fields"
                :resource="getReminders(data)"
                @update="handleCrudUpdate"
                :actions="{
                    trash: false,
                    restore: false,
                    delete: {
                        label: 'Delete',
                        handler: ({ data }) => handleClickDelete(data),
                    },
                }"
                :top-actions="false"
                :edit-component="ReminderForm"
            />
        </template>
    </ApolloQuery>
    <confirm
        title="Really Trash Reminder?"
        v-if="confirmDelete"
        @confirm="handleConfirmDelete"
        @cancel="confirmDelete = null"
    >
        Are you sure you want to delete Reminder '{{ confirmDelete.title }}'
    </confirm>
</template>
<script>
import { defineComponent, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";

import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import ReminderForm from "@/Pages/Reminders/Partials/ReminderForm.vue";
import queries from "@/gql/queries";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: [],
    setup(props) {
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            confirmDelete.value = item;
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("reminders.delete", confirmDelete.value));
            confirmDelete.value = null;
        };

        const fields = ["name", "description", "remind_time", "triggered_at"];

        let navLinks = [
            {
                label: "Calendar",
                href: route("calendar"),
                onClick: null,
                active: false,
            },
            {
                label: "My Calendar",
                href: route("calendar.mine"),
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
            {
                label: "QuickView",
                href: route("calendar.quickview"),
                onClick: null,
                active: false,
            },
        ];
        const param = ref({
            page: 1,
        });

        const getReminders = (data) => {
            return _.cloneDeep(data.reminders);
        };
        const handleCrudUpdate = (key, value) => {
            if (typeof value === "object") {
                param.value = {
                    ...param.value,
                    [key]: {
                        ...param.value[key],
                        ...value,
                    },
                };
            } else {
                param.value = {
                    ...param.value,
                    [key]: value,
                };
            }
        };
        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
            Inertia,
            navLinks,
            param,
            getReminders,
            handleCrudUpdate,
            queries,
            ReminderForm,
        };
    },
});
</script>
