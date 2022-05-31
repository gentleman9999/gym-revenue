<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl leading-tight">
                    Email Template Management
                </h2>
            </div>
            <div
                class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 lg:justify-start"
            >
                <inertia-link
                    class="btn justify-self-end"
                    :href="route('comms.dashboard')"
                >
                    <span>
                        <font-awesome-icon
                            :icon="['far', 'chevron-double-left']"
                            size="sm"
                        />
                        Back
                    </span>
                </inertia-link>
            </div>
        </template>

        <gym-revenue-crud
            base-route="comms.email-templates"
            model-name="Email Template"
            model-key="template"
            :fields="fields"
            :resource="templates"
            :actions="actions"
            :top-actions="topActions"
            :table-component="false"
            :card-component="EmailTemplateCard"
        />

        <confirm
            title="Really Trash?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to remove this template? It will be removed
            from any assigned campaigns.
        </confirm>
        <confirm-send-form
            v-if="confirmSend"
            :template-id="sendVars.templateId"
            :template-name="sendVars.templateName"
            @close="handleCloseTextModal"
        ></confirm-send-form>
    </app-layout>
</template>

<script>
import { defineComponent, ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";

import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/Components/Confirm";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";
import { faImage } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import ConfirmSendModal from "@/Components/SweetModal3/SweetModal";
import ConfirmSendForm from "@/Presenters/MassComm/TestMsgs/SendTestEmail";
import EmailTemplateCard from "./Partials/EmailTemplateCard";
library.add(faChevronDoubleLeft, faEllipsisH, faImage);

export default defineComponent({
    name: "EmailTemplatesIndex",
    components: {
        FontAwesomeIcon,
        AppLayout,
        Confirm,
        GymRevenueCrud,
        ConfirmSendModal,
        ConfirmSendForm,
    },
    props: ["title", "filters", "templates"],
    setup(props) {
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

        const confirmSend = ref(null);
        const sendVars = () => {
            return {
                templateId: "",
                templateName: "",
            };
        };

        const handleOpenSendModal = (data) => {
            console.log("looking at data", data, sendVars);
            confirmSend.value = data.id;
            sendVars.templateId = data.id;
            sendVars.templateName = data.name;
            //sendModal.value.open();
        };
        const handleCloseTextModal = () => {
            sendVars.templateId = "";
            sendVars.templateName = "";
            //sendModal.value.close();
            confirmSend.value = null;
        };
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
                    export: (active) => (active ? "Active" : "Draft"),
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
                edit: {
                    handler: ({ data }) => {
                        Inertia.visitInModal(
                            route("comms.email-templates.edit", data.id),
                            {
                                redirectBack: (e) => {
                                    console.log("redirect-back", e);
                                },
                                modalProps: {
                                    class: "max-w-[90vw] h-[90vh] p-0",
                                    showCloseButton: false,
                                },
                                // reloadOnClose: true,
                            }
                        );
                    },
                },
                selfSend: {
                    label: "Send You a Test Email",
                    handler: ({ data }) => handleOpenSendModal(data),
                },
                trash: {
                    handler: ({ data }) => handleClickTrash(data.id),
                },
            };
        });

        const topActions = {
            create: {
                label: "New Template",
                handler: () => {
                    Inertia.visitInModal(
                        route("comms.email-templates.create"),
                        {
                            // redirectBack: (e) => {
                            //     console.log("redirect-back-after-create", e);
                            // },
                            modalProps: {
                                class: "max-w-[90vw] h-[90vh] p-0",
                                showCloseButton: false,
                            },
                            reloadOnClose: true,
                        }
                    );
                },
            },
        };

        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        };
        return {
            fields,
            actions,
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            handleOpenSendModal,
            handleCloseTextModal,
            confirmSend,
            sendVars,
            topActions,
            EmailTemplateCard,
        };
    },
});
</script>
