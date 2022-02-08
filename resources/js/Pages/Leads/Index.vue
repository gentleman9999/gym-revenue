<template>
    <app-layout :title="title">
        <jet-bar-container>
            <div class="bg-base-200 w-full rounded-lg p-4">
                <div class="flex flex-row items-center mb-4">
                    <h2 class="font-semibold text-xl leading-tight">Leads</h2>
                    <div class="flex-grow" />
                    <div
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                    ></div>
                </div>

                <div class="flex flex-row items-center mb-4">
                    <div class="hidden space-x-8 sm:-my-px sm:flex pt-6">
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Dashboard
                        </a>
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Calendar
                        </a>
                        <inertia-link
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            :class="(routeName === 'data.leads') ? 'bg-white text-black px-2 rounded' : ''"
                            :href="route('data.leads')"
                        >
                            Leads
                        </inertia-link>
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Tasks
                        </a>
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Contacts
                        </a>
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Consultants
                        </a>
                        <inertia-link
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            :href="route('data.leads.sources')"
                        >
                            Lead Sources
                        </inertia-link>

                        <inertia-link
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                        >
                            Lead Statuses
                        </inertia-link>
                    </div>

                    <div class="flex-grow" />
                </div>
            </div>
        </jet-bar-container>
        <gym-revenue-crud
            :resource="leads"
            :fields="fields"
            base-route="data.leads"
            :top-actions="{
                create: { label: 'Add Lead' },
            }"
            :actions="actions"
        />
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
import { computed, defineComponent, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import LeadInteraction from "./Partials/LeadInteractionContainer";
import LeadAvailabilityBadge from "./Partials/LeadAvailabilityBadge";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge";

export default defineComponent({
    components: {
        GymRevenueCrud,
        AppLayout,
        Confirm,
        Button,
        JetBarContainer,
        LeadInteraction,
    },
    props: ["leads", "routeName", "title", "filters", "lead_types", 'grlocations', 'leadsources', 'user'],
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
            { name: "created_at", label: "Created" },
            { name: "first_name", label: "First Name" },
            { name: "last_name", label: "Last Name" },
            { name: "location.name", label: "Location" },
            {
                name: "lead_type",
                label: "Type",
                component: CrudBadge,
                props: {
                    getProps: ({ data: { lead_type } }) => ({
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
                    const claimed = data.details_desc.filter(
                        (detail) => detail.field === "claimed"
                    );
                    console.log({ claimed, props });
                    const yours = claimed.filter(
                        (detail) =>
                            parseInt(detail.value) === parseInt(props.user.id)
                    );
                    if(props.user.permissions['leads.contact']) {
                        return yours.length;
                    } else {
                        return false;
                    }
                },
            },
            edit: {
                shouldRender: ({data}) => {
                    return props.user.permissions['leads.edit']
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

        return {
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            fields,
            actions,
            Inertia,
            comingSoon,
        };
    },
});
</script>

<style scoped></style>
