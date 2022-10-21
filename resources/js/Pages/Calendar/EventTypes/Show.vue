<template>
    <LayoutHeader title="Event Types" />
    <page-toolbar-nav title="Event Types" :links="navLinks" />
    <ApolloQuery :query="(gql) => queries['eventTypes']" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                :resource="getEventTypes(data)"
                @update="handleCrudUpdate"
                base-route="calendar.event_types"
                model-name="Event Type"
                model-key="eventType"
                :edit-component="CalendarEventTypeForm"
                :fields="fields"
                :actions="{
                    trash: {
                        handler: ({ data }) => handleClickTrash(data),
                    },
                }"
            />
        </template>
    </ApolloQuery>
    <confirm
        title="Really Trash Event Type?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to move Event Type '{{ confirmTrash.name }}' to
        the trash?<BR />
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
import queries from "@/gql/queries";
import CalendarEventTypeForm from "@/Pages/Calendar/EventTypes/Partials/CalendarEventTypeForm.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: ["filters"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(
                route("calendar.event_types.trash", confirmTrash.value)
            );
            confirmTrash.value = null;
        };

        const fields = [
            "name",
            "description",
            "type",
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
                label: "My Calendar",
                href: route("calendar.mine"),
                onClick: null,
                active: false,
            },
            {
                label: "Event Types",
                href: route("calendar.event_types"),
                onClick: null,
                active: true,
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
                active: false,
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

        const getEventTypes = (data) => {
            return _.cloneDeep(data.calendar_event_types);
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
            confirmTrash,
            handleConfirmTrash,
            handleClickTrash,
            Inertia,
            navLinks,
            param,
            queries,
            getEventTypes,
            handleCrudUpdate,
            CalendarEventTypeForm,
        };
    },
});
</script>
