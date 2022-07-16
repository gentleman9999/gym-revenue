<template>
    <LayoutHeader title="Leads" />
    <page-toolbar-nav title="Leads" :links="navLinks" />
    <div
        class="max-w-screen lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset relative"
    >
        <div class="flex flex-row space-x-2 flex-wrap">
            <div class="flex w-full md:w-3/5 flex-wrap">
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

    <gym-revenue-crud
        :resource="leads"
        model-key="lead"
        :fields="fields"
        :base-route="baseRoute"
        :top-actions="{
            create: { label: 'Add Lead' },
        }"
        :actions="actions"
        :preview-component="LeadPreview"
    >
        <template #filter>
            <leads-filters :base-route="baseRoute" />
        </template>
    </gym-revenue-crud>
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
import LeadAvailabilityBadge from "./Partials/LeadAvailabilityBadge.vue";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import LeadsFilters from "@/Pages/Leads/Partials/LeadsFilters.vue";
import LeadPreview from "@/Pages/Leads/Partials/LeadPreview.vue";

import CalendarGrid from "@/Pages/components/CalendarGrid.vue";
import CalendarSummaryCard from "@/Pages//components/CalendarSummaryCard.vue";
import { usePage } from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        LeadsFilters,
        PageToolbarNav,
        GymRevenueCrud,
        LayoutHeader,
        Confirm,
        Button,
        JetBarContainer,
        LeadPreview,
        CalendarGrid,
        CalendarSummaryCard,
    },
    props: [
        "leads",
        "routeName",
        "title",
        "filters",
        "lead_types",
        "grlocations",
        "leadsources",
        "user",
        "opportunities",
        "leadsclaimed",
        "nameSearch",
    ],
    setup(props) {
        const page = usePage();
        const badgeClasses = (lead_type_id) => {
            if (!lead_type_id) {
                console.log("no lead type id!");
                return "";
            }

            const badges = [
                "badge-primary",
                "badge-secondary",
                "badge-info",
                "badge-accent",
                "badge-success",
                "badge-outline",
                "badge-ghost",
                "badge-error",
                "badge-warning",
            ];

            const lead_type_index = props.lead_types.findIndex(
                (lead_type) => lead_type.id === lead_type_id
            );
            if (props.lead_types.length <= badges.length) {
                return badges[lead_type_index];
            }
            return badges[lead_type_index % badges.length];
            // console.log({lead_type_index, })
        };

        const badgeClassesOpportunity = (opportunity) => {
            if (!opportunity) {
                console.log("no lead type id!");
                return "";
            }
            const badges = [
                "badge-primary",
                "badge-secondary",
                "badge-info",
                "badge-accent",
                "badge-success",
                "badge-outline",
                "badge-ghost",
                "badge-error",
                "badge-warning",
            ];
            if (opportunity === "High") {
                return badges[4]; //success
            }
            if (opportunity === "Medium") {
                return badges[8]; // info
            }
            if (opportunity === "Low") {
                return badges[7]; //warning
            }
        };

        const fields = [
            { name: "created_at", label: "Created" },
            {
                name: "opportunity.value",
                label: "Opportunity",
                component: CrudBadge,
                props: {
                    getProps: ({ data: { opportunity } }) => ({
                        class: badgeClassesOpportunity(opportunity),
                        text: opportunity,
                    }),
                },
            },
            { name: "first_name", label: "First Name" },
            { name: "last_name", label: "Last Name" },
            { name: "location.name", label: "Location" },
            {
                name: "lead_type.name",
                label: "Type",
                component: CrudBadge,
                props: {
                    getProps: ({ data: { lead_type } }) => ({
                        class: badgeClasses(lead_type?.id),
                        text: lead_type?.name,
                    }),
                },
                // transform: data=>data?.lead_type
            },
            {
                name: "owner_user_id",
                label: "Status",
                component: LeadAvailabilityBadge,
                export: (data) => (!!data ? "Claimed" : "Available"),
            },
        ];

        const actions = {
            trash: {
                handler: ({ data }) => handleClickTrash(data.id),
            },

            contact: {
                label: "Contact Lead",
                handler: ({ data }) => {
                    Inertia.visit(route("data.leads.show", data.id));
                },
                shouldRender: ({ data }) => {
                    return (
                        data?.owner_user_id === page.props.value.user.id &&
                        !data?.unsubscribed_comms
                    );
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
            Inertia.delete(route("data.leads.trash", confirmTrash.value), {
                data: { reason: trashReason.value },
            });
            confirmTrash.value = null;
        };
        const baseRoute = "data.leads";
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
                label: "Leads",
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
            {
                label: "Lead Sources",
                href: route("data.leads.sources"),
                onClick: null,
                active: false,
            },
            {
                label: "Lead Statuses",
                href: route("data.leads.statuses"),
                onClick: null,
                active: false,
            },
        ];

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
            LeadPreview,
            trashReason,
        };
    },
});
</script>

<style scoped></style>
