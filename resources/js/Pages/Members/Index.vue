<template>
    <page-toolbar-nav title="Member" :links="navLinks" />
    <div
        class="max-w-screen lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset relative"
    >
        <div class="flex flex-row md:space-x-2 flex-wrap">
            <div class="flex w-full lg:w-3/5 flex-wrap">
                <div class="w-4/5 m-auto md:w-1/3 px-2">
                    <calendar-summary-card title="Confirmed" :progress="[65]" />
                </div>
                <div class="w-4/5 m-auto md:w-1/3 px-2">
                    <calendar-summary-card title="Canceled" :progress="[25]" />
                </div>
                <div class="w-4/5 m-auto md:w-1/3 px-2">
                    <calendar-summary-card
                        title="Rescheduled"
                        :progress="[10]"
                    />
                </div>
            </div>
            <calendar-grid />
        </div>
        <calendar-schedule-table :data="schedule" />
    </div>
    <ApolloQuery :query="(gql) => member_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                :resource="getMembers(data)"
                @update-page="(value) => (param = { ...param, page: value })"
                model-key="member"
                :fields="fields"
                :base-route="baseRoute"
                :top-actions="{
                    create: { label: 'Add Member' },
                }"
                :actions="actions"
                :preview-component="MemberPreview"
            >
                <template #filter>
                    <member-filters :base-route="baseRoute" />
                </template>
            </gym-revenue-crud>
        </template>
    </ApolloQuery>
    <confirm
        title="Really Trash?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
        :disabled="trashReason === null || trashReason === 'none'"
    >
        {{ firstName }} {{ lastName }} Are you sure you want to remove this
        lead?<br />
        Reason for Deleting:<br />
        <select name="reasonforremoving" v-model="trashReason">
            <option value="none">Select a reason</option>
            <option value="duplicate">Is a duplicate</option>
            <option value="test-lead">Is a test lead</option>
            <option value="DNC">Lead requested DNC and data removal</option>
            <option value="person-non-existing">
                This person does not exist
            </option>
            <option value="mistake-creating">
                I made a mistake creating this lead
            </option>
            <option value="other">Other</option>
        </select>
    </confirm>
</template>

<script>
import { computed, defineComponent, ref } from "vue";
import { comingSoon } from "@/utils/comingSoon.js";
import { Inertia } from "@inertiajs/inertia";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Confirm from "@/Components/Confirm.vue";

import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import MemberFilters from "@/Pages/Members/Partials/MemberFilters.vue";
import MemberPreview from "@/Pages/Members/Partials/MemberPreview.vue";

import CalendarGrid from "@/Pages/components/CalendarGrid.vue";
import CalendarSummaryCard from "@/Pages//components/CalendarSummaryCard.vue";
import usePage from "@/Components/InertiaModal/usePage";
import gql from "graphql-tag";

export default defineComponent({
    components: {
        MemberFilters,
        PageToolbarNav,
        GymRevenueCrud,
        LayoutHeader,
        Confirm,
        Button,
        JetBarContainer,
        MemberPreview,
        CalendarGrid,
        CalendarSummaryCard,
    },
    props: [
        "members",
        "routeName",
        "title",
        "filters",
        "grlocations",
        "user",
        "nameSearch",
    ],
    setup(props) {
        const fields = [
            { name: "first_name", label: "First Name" },
            { name: "last_name", label: "Last Name" },
            { name: "location.name", label: "Location" },
            { name: "created_at", label: "Joined" },
            { name: "updated_at", label: "Updated" },
        ];

        const page = usePage();
        const actions = {
            trash: {
                handler: ({ data }) => handleClickTrash(data.id),
            },
            contact: {
                label: "Contact Member",
                shouldRender: (data) => {
                    return (
                        (data?.owner_user_id === page.props.value.user.id &&
                            !data?.unsubscribed_comms) ||
                        page.props.value.user.roles.find((role) =>
                            ["Account Owner"].includes(role.name)
                        )
                    );
                },
                handler: ({ data }) => {
                    Inertia.visit(route("data.members.show", data.id));
                },
            },
        };
        const trashReason = ref(null);

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        handleClickTrash();
        const handleConfirmTrash = () => {
            Inertia.delete(route("data.members.trash", confirmTrash.value), {
                data: { reason: trashReason.value },
            });
            confirmTrash.value = null;
        };
        const baseRoute = "data.members";
        const navLinks = [
            {
                label: "Dashboard",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "CalendarEvent",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Members",
                href: "#",
                onClick: comingSoon,
                active: true,
            },
            {
                label: "Tasks",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Contacts",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Consultants",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
        ];
        const param = ref({
            page: 1,
        });
        const member_query = gql`
            query Members($page: Int) {
                members(page: $page) {
                    data {
                        id
                        first_name
                        last_name
                        created_at
                        updated_at
                        location {
                            name
                        }
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

        const getMembers = (data) => {
            return _.cloneDeep(data.members);
        };
        return {
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            fields,
            actions,
            Inertia,
            comingSoon,
            navLinks,
            baseRoute,
            MemberPreview,
            trashReason,
            param,
            member_query,
            getMembers,
        };
    },
});
</script>

<style scoped></style>
