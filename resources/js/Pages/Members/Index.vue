<template>
    <LayoutHeader title="Members" />
    <page-toolbar-nav title="Member" :links="navLinks" />
    <gym-revenue-crud
        :resource="members"
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
            <option>Select a reason</option>
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
// import LeadInteraction from "./Partials/LeadInteractionContainer.vue";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import MemberFilters from "@/Pages/Members/Partials/MemberFilters.vue";
import MemberPreview from "@/Pages/Members/Partials/MemberPreview.vue";

export default defineComponent({
    components: {
        MemberFilters,
        PageToolbarNav,
        GymRevenueCrud,
        LayoutHeader,
        Confirm,
        Button,
        JetBarContainer,
        // LeadInteraction,
        MemberPreview,
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

        const actions = {
            trash: {
                handler: ({ data }) => handleClickTrash(data.id),
            },

            // contact: {
            //     label: "Contact Member",
            //     handler: ({ data }) => {
            //         Inertia.visit(route("data.members.show", data.id));
            //     },
            // },
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
        };
    },
});
</script>

<style scoped></style>
