<template>
    <app-layout :title="title">
        <page-toolbar-nav title="Leads" :links="navLinks"/>
        <gym-revenue-crud
            :resource="leads"
            :fields="fields"
            :base-route="baseRoute"
            :top-actions="{
                create: { label: 'Add Lead' },
            }"
            :actions="actions"
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
        >
            {{ firstName }} {{ lastName }} Are you sure you want to remove this
            lead?<br /> Reason for Deleting:<br />
            <select name="reasonforremoving">
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
    </app-layout>
</template>

<script>
import {computed, defineComponent, ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import LeadInteraction from "./Partials/LeadInteractionContainer";
import LeadAvailabilityBadge from "./Partials/LeadAvailabilityBadge";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge";
import PageToolbarNav from "@/Components/PageToolbarNav";
import LeadsFilters from "@/Pages/Leads/Partials/LeadsFilters";

export default defineComponent({
    components: {
        LeadsFilters,
        PageToolbarNav,
        GymRevenueCrud,
        AppLayout,
        Confirm,
        Button,
        JetBarContainer,
        LeadInteraction,
    },
    props: ["leads", "routeName", "title", "filters", "lead_types", 'grlocations', 'leadsources', 'user',
        'opportunities', 'leadsclaimed', 'dob', 'nameSearch'],
    setup(props) {
        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        };
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
        const fields = [
            {name: "created_at", label: "Created"},
            {name: "first_name", label: "First Name"},
            {name: "last_name", label: "Last Name"},
            {name: "location.name", label: "Location"},
            {
                name: "lead_type",
                label: "Type",
                component: CrudBadge,
                props: {
                    getProps: ({data: {lead_type}}) => ({
                        class: badgeClasses(lead_type?.id),
                        text: lead_type?.name,
                    }),
                },
            },
            {
                name: "details_desc",
                label: "Status",
                component: LeadAvailabilityBadge,
            },
        ];

        const  actions = {
            trash: {
                handler: ({data}) => handleClickTrash(data.id),
            },

            contact: {
                label: "Contact Lead",
                handler: ({data}) => {
                    Inertia.visit(route("data.leads.show", data.id));
                },
                shouldRender: ({data}) => {
                    const claimed = data.details_desc.filter(
                        (detail) => detail.field === "claimed"
                    );
                    console.log({claimed, props});
                    const yours = claimed.filter(
                        (detail) =>
                            parseInt(detail.value) === parseInt(props.user.id)
                    );
                    return yours.length;
                },
            },
        };

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        handleClickTrash();
        const handleConfirmTrash = () => {
            /* */
            axios.delete(route("data.leads.trash", confirmTrash.value)).then(
                (response) => {
                    setTimeout(() => response($result, 200), 10000);
                },
                Inertia.reload(),
                location.reload(),
                (confirmTrash.value = null)
            );
        };
        const baseRoute = 'data.leads';
        const navLinks = [
            {
                label: 'Dashboard',
                href: '#',
                onClick: comingSoon,
                active: false
            },
            {
                label: 'Calendar',
                href: '#',
                onClick: comingSoon,
                active: false
            },
            {
                label: 'Leads',
                href: '#',
                onClick: comingSoon,
                active: true
            },
            {
                label: 'Tasks',
                href: '#',
                onClick: comingSoon,
                active: false
            },
            {
                label: 'Contacts',
                href: '#',
                onClick: comingSoon,
                active: false
            },
            {
                label: 'Consultants',
                href: '#',
                onClick: comingSoon,
                active: false
            },
            {
                label: 'Lead Sources',
                href: route('data.leads.sources'),
                onClick: null,
                active: false
            },
            {
                label: 'Lead Statuses',
                href: route('data.leads.statuses'),
                onClick: null,
                active: false
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
            baseRoute
        };
    },
});
</script>

<style scoped></style>
