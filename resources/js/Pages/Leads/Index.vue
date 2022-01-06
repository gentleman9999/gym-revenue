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
                            >Dashboard</a
                        >
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                            >Calendar</a
                        >
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                            >Leads</a
                        >
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                            >Tasks</a
                        >
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                            >Contacts</a
                        >
                        <a
                            class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                            href="#"
                            @click="comingSoon()"
                            >Consultants</a
                        >
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
            :actions="{
                trash: false,
                contact: {
                    label: 'Contact Lead',
                    handler: ({ data }) => {
                        Inertia.visit(route('data.leads.show', data.id));
                    },
                },
            }"
        />
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout";
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
        Button,
        JetBarContainer,
        LeadInteraction,
    },
    props: ["leads", "title", "isClientUser", "filters"],
    setup() {
        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        }
        const  badgeClasses = (lead_type_id) => {
            if(!lead_type_id){
                return '';
            }

            const badges =  [
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

            return badges[ lead_type_id < badges.length ? lead_type_id : badges.length % lead_type_id]
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

        return { fields, Inertia, comingSoon };
    },
});
</script>

<style scoped></style>
