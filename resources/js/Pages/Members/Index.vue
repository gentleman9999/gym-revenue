<template>
    <page-toolbar-nav title="Member" :links="navLinks" />
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
                            New Members Today
                        </p>
                        <div class="h-52 mb-4 mt-5 totalcenter">
                            <span class="text-8xl">{{ newMemberCount }}</span>
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
        model-key="member"
        :fields="fields"
        :base-route="baseRoute"
        :top-actions="{
            create: { label: 'Add Member' },
        }"
        :actions="actions"
        :preview-component="MemberPreview"
        :edit-component="MemberForm"
    >
        <template #filter>
            <member-filters :handleCrudUpdate="handleCrudUpdate" />
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

<script setup>
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
import MemberForm from "@/Pages/Members/Partials/MemberForm.vue";

import CalendarGrid from "@/Pages/components/CalendarGrid.vue";
import CalendarSummaryCard from "@/Pages//components/CalendarSummaryCard.vue";

import gql from "graphql-tag";
import queries from "@/gql/queries";

const props = defineProps({
    members: {
        type: [Array, Object],
    },
    routeName: {
        type: String,
    },
    title: {
        type: String,
    },
    filters: {
        type: [Array, Object],
    },
    grlocations: {
        type: [Array, String, Object],
    },
    user: {
        type: Object,
    },
    nameSearch: {
        type: [String, Object, Array],
    },
    newMemberCount: {
        type: [String, Number],
    },
});

const fields = [
    { name: "first_name", label: "First Name" },
    { name: "last_name", label: "Last Name" },
    { name: "home_location.name", label: "Location" },
    { name: "created_at", label: "Joined" },
    { name: "updated_at", label: "Updated" },
];

const actions = {
    trash: {
        handler: ({ data }) => handleClickTrash(data.id),
    },
    contact: {
        label: "Contact Member",
        shouldRender: (data) => {
            return true;
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

const getMembers = (data) => {
    return _.cloneDeep(data.members);
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
</script>

<style scoped></style>
