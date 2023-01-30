<template>
    <LayoutHeader title="Email Templates">
        <div class="text-center">
            <h2 class="font-semibold text-xl leading-tight">
                Email Template Management
            </h2>
        </div>
        <div class="top-drop-row stop-drop-roll">
            <inertia-link
                class="btn justify-self-end"
                :href="route('mass-comms.dashboard')"
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
    </LayoutHeader>
    <gym-revenue-crud
        base-route="emailTemplates"
        model-name="EmailTemplate"
        model-key="emailTemplate"
        :fields="fields"
        :actions="actions"
        :top-actions="{ create: { label: 'New Template' } }"
        :table-component="false"
        :cardComponent="EmailTemplateCard"
        :edit-component="EmailTemplateForm"
    />

    <confirm
        title="Really Trash?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to remove this template? It will be removed from
        any assigned campaigns.
    </confirm>
    <confirm-send-form
        v-if="confirmSend"
        :template-id="sendVars.templateId"
        :template-name="sendVars.templateName"
        @close="handleCloseTextModal"
    ></confirm-send-form>
</template>
<style scoped>
.top-drop-row {
    @apply flex flex-row justify-center lg:justify-start md:ml-4;
}
</style>
<script>
import { computed, defineComponent, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Confirm from "@/Components/Confirm.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";
import { faImage } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import ConfirmSendForm from "@/Presenters/MassComm/TestMsgs/SendTestEmail.vue";
import EmailTemplateCard from "./Partials/EmailTemplateCard.vue";
import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";
import queries from "@/gql/queries";

library.add(faChevronDoubleLeft, faEllipsisH, faImage);

export default defineComponent({
    name: "EmailTemplatesIndex",
    components: {
        FontAwesomeIcon,
        LayoutHeader,
        Confirm,
        GymRevenueCrud,
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
                route("mass-comms.email-templates.trash", confirmTrash.value)
            );
            confirmTrash.value = null;
        };

        const param = ref({
            page: 1,
        });

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

        // const fields = ["name", "created_at", "updated_at"];

        // const fields = computed(() => {
        //     return [
        //         "name",
        //         {
        //             name: "active",
        //             label: "status",
        //             props: {
        //                 truthy: "Active",
        //                 falsy: "Draft",
        //                 getProps: ({ data }) =>
        //                     !!data.active
        //                         ? { text: "Active", class: "badge-success" }
        //                         : { text: "Draft", class: "badge-warning" },
        //             },
        //             export: (active) => (active ? "Active" : "Draft"),
        //         },
        //         { name: "type", transform: () => "Regular" },
        //         { name: "updated_at", label: "date updated" },
        //         {
        //             name: "creator.name",
        //             label: "updated by",
        //             transform: (creator) => creator || "Auto Generated",
        //         },
        //     ];
        // });

        const fields = [
            "id",
            "name",
            "markup",
            "subject",
            "thumbnail",
            "thumbnail.key",
            "thumbnail.url",
            "active",
            "client_id",
            "team_id",
            "created_by_user_id",
            "creator.id",
            "created_at",
            "updated_at",
        ];

        const actions = computed(() => {
            return {
                selfSend: {
                    label: "Send You a Test Email",
                    handler: ({ data }) => handleOpenSendModal(data),
                },
                trash: {
                    handler: ({ data }) => handleClickTrash(data.id),
                },
            };
        });

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
            queries,
            param,
            EmailTemplateCard,
            EmailTemplateForm,
        };
    },
});
</script>
