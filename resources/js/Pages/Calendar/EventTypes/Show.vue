<template>
    <app-layout title="Event Types">

        <page-toolbar-nav
            title="Event Types"
            :links="navLinks"
        />
        <gym-revenue-crud
            base-route="calendar.event_types"
            model-name="Event Type"
            model-key="calendar-event-types"
            :fields="fields"
            :resource="calendarEventTypes"
            :actions="{
                trash: {
                    handler: ({ data }) => handleClickTrash(data),
                },
            }"
        />
        <confirm
            title="Really Trash Event Type?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to move Event Type '{{
                confirmTrash.name
            }}' to the trash?<BR />
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
    props: ["calendarEventTypes", "filters"],
    setup(props) {

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("calendar.event_types.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const fields = ["name", "description", "type", "created_at", "updated_at"];

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
                active: true
            },
        ];

        return {fields, confirmTrash, handleConfirmTrash, handleClickTrash, Inertia, navLinks};
    },
});
</script>
