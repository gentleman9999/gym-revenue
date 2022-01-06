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
               
                contact: {
                    label: 'Contact Lead',
                    handler: ({ data }) => {
                        Inertia.visit(route('data.leads.show', data.id));
                    },
                },
            }"
        />
		 <confirm
            title="Really Trash?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to remove this lead?  
        </confirm>
		
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/Components/Confirm";

import {computed,  ref} from "vue";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import LeadInteraction from "./Partials/LeadInteractionContainer";
import LeadAvailabilityBadge from "./Partials/LeadAvailabilityBadge";
import CrudBadge from "@/Components/CRUD/Fields/CrudBadge";


import {library} from '@fortawesome/fontawesome-svg-core';
import {faChevronDoubleLeft, faEllipsisH} from '@fortawesome/pro-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(faChevronDoubleLeft, faEllipsisH)

export default defineComponent({
    components: {
        GymRevenueCrud,
        AppLayout,
		Confirm,
        Button,
        JetBarContainer,
        LeadInteraction,
		
    },
    props: ["leads", "title", "isClientUser", "filters"],
    setup() {
	
		const actions = computed(() => {
            return {
                
                trash:{
                    handler: ({data}) => handleClickTrash(data.id)
                }
            };
        });
		
        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        }
        const badgeClasses = (lead_type) => {
            return {
                "badge-primary": lead_type === "facebook",
                "badge-secondary": lead_type === "snapchat",
                "badge-info": lead_type === "free_trial",
                "badge-accent": lead_type === "instagram",
                "badge-success": lead_type === "grand_opening",
                "badge-outline": lead_type === "contact_us",
                "badge-ghost": lead_type === "app_referral",
                "badge-error": lead_type === "streaming_preview",
                "badge-warning": lead_type === "personal_training",
            };
        };
        const fields =   [
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
                        class: badgeClasses(lead_type),
                        text: lead_type,
                    }),
                },
            },
            {
                name: "details_desc",
                label: "Status",
                component: LeadAvailabilityBadge,
            },
        ];

	    const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(route("data.leads.trash", confirmTrash.value));
            confirmTrash.value = null;
        };
        return { handleClickTrash, confirmTrash, handleConfirmTrash,  fields, Inertia,  comingSoon };
    },
});
</script>

<style scoped></style>
