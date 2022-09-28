<template>
    <LayoutHeader title="Templates Dashboard"> </LayoutHeader>

    <jet-bar-container>
        <h1 class="text-2xl pb-8">Templates Dashboard</h1>

        <!-- Email Templates -->
        <TemplateList
            v-if="permissions.email.read"
            :permissions="permissions.email"
            type="email"
            @create="handleTemplateEditor"
        >
            <template #current_templates>
                <EmailTemplatePreview
                    v-for="t in email_templates"
                    :key="t.id"
                    :template="t"
                    :trash_template="false"
                    :permissions="permissions.email"
                    @edit="handleTemplateEditor"
                    @trash="handleTrash"
                />
            </template>
            <template #trashed_templates>
                <EmailTemplatePreview
                    v-for="t in email_templates"
                    :key="t.id + '_trash'"
                    :template="t"
                    :permissions="permissions.email"
                    :trash_template="true"
                    @restore="handleRestore"
                />
            </template>
        </TemplateList>

        <!-- Call Templates -->
        <TemplateList
            v-if="permissions.call.read"
            :permissions="permissions.call"
            type="call"
            @create="handleTemplateEditor"
        >
            <template #current_templates>
                <CallTemplatePreview
                    v-for="t in call_templates"
                    :key="t.id"
                    :template="t"
                    :trash_template="false"
                    :permissions="permissions.call"
                    @edit="handleTemplateEditor"
                    @trash="handleTrash"
                />
            </template>
            <template #trashed_templates>
                <CallTemplatePreview
                    v-for="t in call_templates"
                    :key="t.id + '_trash'"
                    :template="t"
                    :trash_template="true"
                    :permissions="permissions.call"
                    @restore="handleRestore"
                />
            </template>
        </TemplateList>

        <!-- Sms Templates -->
        <TemplateList
            v-if="permissions.sms.read"
            :permissions="permissions.sms"
            type="sms"
            @create="handleTemplateEditor"
        >
            <template #current_templates>
                <SmsTemplatePreview
                    v-for="t in sms_templates"
                    :key="t.id"
                    :template="t"
                    :trash_template="false"
                    :permissions="permissions.sms"
                    @edit="handleTemplateEditor"
                    @trash="handleTrash"
                />
            </template>
            <template #trashed_templates>
                <SmsTemplatePreview
                    v-for="t in sms_templates"
                    :key="t.id + '_trash'"
                    :template="t"
                    :trash_template="true"
                    :permissions="permissions.sms"
                    @restore="handleRestore"
                />
            </template>
        </TemplateList>
    </jet-bar-container>

    <ModalUnopinionated v-if="templateCreator !== null">
        <!-- builder components -->
        <div
            class="flex flex-col h-full w-full justify-center items-center bg-black bg-opacity-80"
        >
            <div
                class="bg-neutral p-8 border-secondary border rounded-md scale-90"
            >
                <component
                    :is="templateCreator"
                    :template_item="currentTemplate"
                    :template="currentTemplate"
                    :topol-api-key="topolApiKey"
                    :can-activate="false"
                    :use-inertia="false"
                    @done="reloadTemplates"
                    @onClose="closeEditor"
                    @cancel="closeEditor"
                    @error="handleErrors"
                />
            </div>
        </div>
    </ModalUnopinionated>
</template>

<script setup>
import axios from "axios";
import { ref, computed } from "vue";
import { resolveTemplateType } from "./components/helpers";
import { toastInfo, toastError, toastSuccess } from "@/utils/createToast";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

import ModalUnopinionated from "@/Components/ModalUnopinionated.vue";

import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";

// display logic components
import TemplateList from "./partials/TemplateList.vue";
import EmailTemplatePreview from "./partials/previewItems/EmailTemplatePreview.vue";
import CallTemplatePreview from "./partials/previewItems/CallTemplatePreview.vue";
import SmsTemplatePreview from "./partials/previewItems/SmsTemplatePreview.vue";

// outside components
import CallScript from "../MassCommunication/components/Creator/CallScript.vue";
import EmailTemplateForm from "@/Pages/Comms/Emails/Templates/Partials/EmailTemplateForm.vue";
import SmsTemplateForm from "@/Pages/Comms/SMS/Templates/Partials/SmsTemplateForm.vue";

const props = defineProps({
    topolApiKey: {
        type: String,
        required: true,
    },
    campaigns: {
        type: Array,
        required: true,
    },
    email_templates: {
        type: Array,
        default: [],
    },
    sms_templates: {
        type: Array,
        default: [],
    },
    call_templates: {
        type: Array,
        default: [],
    },
});

/** set to the template creator component - set to null if none should be open */
const templateCreator = ref(null);
/** set to current template to manipulate state of */
const currentTemplate = ref({});

/**
 * handles the creation and modification of existing templates via their cooresponding builders
 * @param {string} templateType - which type of new template are we making
 */
const handleTemplateEditor = (templateType, templateItem) => {
    let componentToUse = null;

    switch (templateType) {
        case "call":
            componentToUse = CallScript;
            break;
        case "email":
            componentToUse = EmailTemplateForm;
            break;
        case "sms":
            componentToUse = SmsTemplateForm;
            break;
        default:
            componentToUse = null;
            break;
    }

    templateCreator.value = componentToUse;
    currentTemplate.value = templateItem;
    return;
};

/**
 * Sends a trash request for the passed template
 * @param {Object} templateItem template we wish to trash
 */
async function handleTrash(templateType, templateItem = null) {
    if (!templateItem) return toastError("Template/script does not exist");

    try {
        const res = await axios.delete(
            `mass-comms/${templateType}-templates/${templateItem?.id}`,
            {
                data: templateItem,
            }
        );

        if (res.status === 200) {
            Inertia.reload({
                only: [`${templateType}_templates`],
                onFinish: () => {
                    toastSuccess(templateType + " Template Trashed!");
                },
            });
        }
    } catch (err) {
        handleErrors(err);
    }
}

/**
 * Sends a restore request for the passed template
 * @param {Object} templateItem template we wish to restore
 */
async function handleRestore(templateType, templateItem = null) {
    if (!templateItem) return toastError("Template/script does not exist");

    try {
        const res = await axios.post(
            `mass-comms/${templateType}-templates/${templateItem?.id}`,
            {
                data: templateItem,
            }
        );

        if (res.status === 200) {
            Inertia.reload({
                only: [`${templateType}_templates`],
                onFinish: () => {
                    toastSuccess(templateType + " Template Restored!");
                },
            });
        }
    } catch (err) {
        handleErrors(err);
    }
}

const closeEditor = () => {
    templateCreator.value = null;
    currentTemplate.value = {};
};

/** State reorientation after a successful POST/PUT requests */
const reloadTemplates = (newData) => {
    closeEditor();
    let templateType = resolveTemplateType(newData);
    if (!templateType) return;
    let reloadPropName = templateType.toLocaleLowerCase() + "_templates";

    toastSuccess(templateType + " Template Saved!");

    Inertia.reload({
        only: [reloadPropName],
        onFinish: () => {
            document
                .getElementById(newData?.id)
                .scrollIntoView({ block: "center" });
        },
    });
};

/** Graciously handle errors & log them to the console */
const handleErrors = (err) => {
    console.error(err);
    toastError("There was a problem with the operation");
};

/** Actions the current user can perform (affects layout) */
const permissions = computed(() => {
    let page = usePage();
    let user = page.props.value.user;

    let isAdmin = user?.abilities?.includes("*");

    const checkPermission = (prefix, suffix) => {
        if (isAdmin) return true;
        return user?.abilities?.includes(`${prefix}-templates.${suffix}`);
    };

    return {
        email: {
            create: checkPermission("email", "create"),
            update: checkPermission("email", "update"),
            trash: checkPermission("email", "trash"),
            restore: checkPermission("email", "restore"),
            read: checkPermission("email", "read"),
            delete: checkPermission("email", "delete"),
        },
        call: {
            create: checkPermission("call", "create"),
            update: checkPermission("call", "update"),
            trash: checkPermission("call", "trash"),
            restore: checkPermission("call", "restore"),
            read: checkPermission("call", "read"),
            delete: checkPermission("call", "delete"),
        },
        sms: {
            create: checkPermission("sms", "create"),
            update: checkPermission("sms", "update"),
            trash: checkPermission("sms", "trash"),
            restore: checkPermission("sms", "restore"),
            read: checkPermission("sms", "read"),
            delete: checkPermission("sms", "delete"),
        },
    };
});
</script>

<style scoped></style>

<style>
main {
    @apply !p-0;
}

#layout-header {
    @apply !p-0 !m-0 !max-w-none;
}
</style>
