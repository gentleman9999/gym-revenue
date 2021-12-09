<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl  leading-tight">
                    SMS Campaigns Management
                </h2>
            </div>
            <div class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 lg:justify-start">
                <inertia-link
                    class="btn justify-self-end"
                    :href="route('comms.dashboard')">
                    <span><font-awesome-icon :icon="['far', 'chevron-double-left']" size="sm"/> Back</span>
                </inertia-link>
            </div>
        </template>
        <gym-revenue-crud
            base-route="comms.sms-campaigns"
            model-name="SMS Campaign"
            :fields="fields"
            :resource="campaigns"
            :actions="actions"
            :top-actions="{ create: { label: 'New Campaign' } }"
        />
        <confirm
            title="Really Trash?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to remove this campaign?  It will unassign all audiences and/or templates.
        </confirm>
    </app-layout>
</template>

<script>
import {computed, defineComponent, ref} from "vue";
import AppLayout from '@/Layouts/AppLayout'
import Confirm from "@/Components/Confirm";

import {library} from '@fortawesome/fontawesome-svg-core';
import {faChevronDoubleLeft, faEllipsisH} from '@fortawesome/pro-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import {Inertia} from "@inertiajs/inertia";

library.add(faChevronDoubleLeft, faEllipsisH)

export default defineComponent({
    name: "SMSCampaignsIndex",
    components: {
        AppLayout,
        FontAwesomeIcon,
        GymRevenueCrud,
        Confirm
    },
    props: ['title', 'filters', 'campaigns'],
    setup(props) {
        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        };

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(route("comms.sms-templates.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const fields = computed(() => {
            return [
                "name",
                {
                    name: "active",
                    label: "status",
                    props: {
                        getProps: ({ data }) =>
                            !!data.active
                                ? { text: "Active", class: "badge-success" }
                                : { text: "Draft", class: "badge-warning" },
                    },
                },
                { name: "type", transform: () => "Regular" },
                { name: "updated_at", label: "date updated" },
                {
                    name: "creator.name",
                    label: "updated by",
                    transform: (creator) => creator || "Auto Generated",
                },
            ];
        });

        const actions = computed(() => {
            return {
                quickSend: {
                    label: "Quick Send",
                    handler: () => comingSoon(),
                },
                trash:{
                    handler: ({data}) => handleClickTrash(data.id)
                }
            };
        });
        return { handleClickTrash, confirmTrash, handleConfirmTrash, fields, actions };
    },
});
</script>

