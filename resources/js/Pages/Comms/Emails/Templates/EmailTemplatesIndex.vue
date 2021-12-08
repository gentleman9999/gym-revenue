<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl leading-tight">
                    Email Template Management
                </h2>
                <small></small>
            </div>
        </template>
        <gym-revenue-crud
            base-route="comms.email-templates"
            model-name="Email Template"
            :fields="fields"
            :resource="templates"
            :actions="actions"
            :top-actions="{ create: { label: 'New Template' } }"
        />

        <confirm
            title="Really Trash?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
        >
            Are you sure you want to trash this template? It will be removed
            from any assigned campaigns.
        </confirm>
    </app-layout>
</template>

<script>
import { defineComponent, ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/Components/Confirm";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";

import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    name: "EmailTemplatesIndex",
    components: {
        AppLayout,
        Confirm,
        GymRevenueCrud,
    },
    props: ["title", "filters", "templates"],
    setup(props) {
        const fields = computed(() => {
            return [
                "name",
                {
                    name: "active",
                    label: "status",
                    props: {
                        truthy: "Active",
                        falsy: "Draft",
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
                selfSend: {
                    label: "Send You a Test Email",
                    handler: () => comingSoon(),
                },
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
        };
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(
                route("comms.email-templates.trash", confirmTrash.value)
            );
            confirmTrash.value = null;
        };
        return {
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            fields,
            actions,
            comingSoon,
        };
    },
});
</script>
