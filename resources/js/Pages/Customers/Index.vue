<template>
    <page-toolbar-nav title="Customer" :links="navLinks" />
    <div
        class="max-w-screen lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset"
    >
        <div class="flex flex-row md:space-x-2 flex-wrap">
            <div class="flex w-full lg:w-3/5 flex-wrap">
                <div class="w-4/5 m-auto md:w-1/3 px-2">
                    <div
                        class="px-4 py-3 border border-secondary rounded bg-neutral relative mb-3"
                    >
                        <p class="text-base-content text-lg">
                            New Customers Today
                        </p>
                        <div class="h-52 mb-4 mt-5 totalcenter">
                            <span class="text-8xl">{{ newCustomerCount }}</span>
                        </div>
                    </div>
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
        model-key="customer"
        base-route="customers"
        model-name="Customer"
        :fields="fields"
        :base-route="baseRoute"
        :top-actions="{
            create: { label: 'Add Customer' },
        }"
        :actions="actions"
        :preview-component="CustomerPreview"
        :edit-component="CustomerForm"
    >
        <template #filter>
            <customer-filters :base-route="baseRoute" />
        </template>
    </gym-revenue-crud>
    <!--            <gym-revenue-crud-->
    <!--                model-key="customer"-->
    <!--                :fields="fields"-->
    <!--                :base-route="baseRoute"-->
    <!--                :top-actions="{-->
    <!--                    create: { label: 'Add Customer' },-->
    <!--                }"-->
    <!--                :actions="actions"-->
    <!--                :preview-component="CustomerPreview"-->
    <!--                :edit-component="CustomerForm"-->
    <!--            >-->
    <!--                <template #filter>-->
    <!--                    <customer-filters />-->
    <!--                </template>-->
    <!--            </gym-revenue-crud>-->
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
import CustomerFilters from "@/Pages/Customers/Partials/CustomerFilters.vue";
import CustomerForm from "@/Pages/Customers/Partials/CustomerForm.vue";
import CustomerPreview from "@/Pages/Customers/Partials/CustomerPreview.vue";

import CalendarGrid from "@/Pages/components/CalendarGrid.vue";
import CalendarSummaryCard from "@/Pages//components/CalendarSummaryCard.vue";
import usePage from "@/Components/InertiaModal/usePage";
import queries from "@/gql/queries";

export default defineComponent({
    components: {
        CustomerFilters,
        PageToolbarNav,
        GymRevenueCrud,
        LayoutHeader,
        Confirm,
        Button,
        JetBarContainer,
        CustomerPreview,
        CalendarGrid,
        CalendarSummaryCard,
        CustomerForm,
    },
    props: [
        "customers",
        "routeName",
        "title",
        "filters",
        "grlocations",
        "user",
        "nameSearch",
        "newCustomerCount",
    ],
    setup(props) {
        const fields = [
            { name: "first_name", label: "First Name" },
            { name: "last_name", label: "Last Name" },
            { name: "home_location.name", label: "Location" },
            { name: "created_at", label: "Joined" },
            { name: "updated_at", label: "Updated" },
        ];

        const page = usePage();
        const actions = {
            trash: {
                handler: ({ data }) => handleClickTrash(data.id),
            },
            contact: {
                label: "Contact Customer",
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
                    Inertia.visit(route("data.customers.show", data.id));
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
            Inertia.delete(route("data.customers.trash", confirmTrash.value), {
                data: { reason: trashReason.value },
            });
            confirmTrash.value = null;
        };
        const baseRoute = "data.customers";
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
                label: "Customers",
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
            CustomerPreview,
            trashReason,
            queries,
            CustomerForm,
        };
    },
});
</script>

<style scoped></style>
