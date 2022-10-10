<template>
    <LayoutHeader title="Event Types" />
    <page-toolbar-nav title="Event Types" :links="navLinks" />
    <ApolloQuery :query="(gql) => event_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                :resource="getEventTypes(data)"
                @update-page="(value) => (param = { ...param, page: value })"
                base-route="calendar.event_types"
                model-name="Event Type"
                model-key="calendar-event-types"
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
import gql from "graphql-tag";

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
        const event_query = gql`
            query CalendarEventTypes($page: Int) {
                calendar_event_types(page: $page) {
                    data {
                        id
                        name
                        description
                        type
                        created_at
                        updated_at
                    }
                    pagination: paginatorInfo {
                        current_page: currentPage
                        last_page: lastPage
                        from: firstItem
                        to: lastItem
                        per_page: perPage
                        total
                    }
                }
            }
        `;

        const getEventTypes = (data) => {
            return _.cloneDeep(data.calendar_event_types);
        };

        return {
            fields,
            confirmTrash,
            handleConfirmTrash,
            handleClickTrash,
            Inertia,
            navLinks,
            param,
            event_query,
            getEventTypes,
        };
    },
});
</script>
